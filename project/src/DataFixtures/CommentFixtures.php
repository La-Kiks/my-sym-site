<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Repository\UserRepository;
use App\Repository\PostRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $this->userRepository->findAll();
        $posts = $this->postRepository->findAll();

        foreach ($posts as $post) {
            for ($i = 0; $i < mt_rand(0, 15); $i++) {
                $comment = new Comment();
                $comment->setContent($faker->realText(maxNbChars: 200, indexSize: 1))
                    ->setIsApproved(!(mt_rand(0, 3) === 0))
                    ->setAuthor($users[mt_rand(0, count($users) -1)])
                    ->setPost($post);

                $manager->persist($comment);
                $post->addComment($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class
        ];
    }
}
