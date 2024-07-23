<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private readonly PostRepository $postRepository,
    )
    {

    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_Fr');

        $posts = $this->postRepository->findAll();

        // Custom User
        $user = new User();
        $user->setEmail('admin@gmail.com')
            ->setFirstName('Admin')
            ->setPassword(
                $this->hasher->hashPassword($user, 'password')
            );

        $manager->persist($user);

        foreach ($posts as $post){
            $user = new User();
            $user->setEmail($faker->email())
                ->setLastName($faker->lastName())
                ->setFirstName($faker->firstName)
                ->setPassword(
                    $this->hasher->hashPassword($user, 'password')
                )
                ->addPost($post)
            ;

            $manager->persist($user);
            $post->setUser($user);
        }

//        for ($i=0; $i < 9; $i++){
//            $user = new User();
//            $user->setEmail($faker->email())
//                ->setLastName($faker->lastName())
//                ->setFirstName($faker->firstName)
//                ->setPassword(
//                    $this->hasher->hashPassword($user, 'password')
//                );
//
//            $manager->persist($user);
//        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PostFixtures::class
        ];
    }
}
