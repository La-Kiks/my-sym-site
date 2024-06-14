<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController
{
    #[Route('like/article/{id}', name: 'like.post', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function like(Post $post, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if(isset($user)){

            /** @var User $user */
            if($post->isLikedByUser($user)){
            $post->removeLike($user);
            $manager->flush();
            return $this->json([
                'message' => 'Le like a été supprimé.',
                'nbLike' => count($post->getLikes())]);
        }
        $post->addLike($user);
        $manager->flush();
        return $this->json([
            'message' => 'Le like a été ajouté.',
            'nbLike' => count($post->getLikes())]);
        } else {
            // Normalement via IsGranted, on a une redirection vers la page de connexion
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }
}