<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index', methods: ['GET'])]
    public function index(PostRepository $postRepository,
                          Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $postRepository->findBySearch($searchData);

            return $this->render('pages/blog/index.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }


        // Default view, without any research.
        return $this->render('pages/blog/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $postRepository->findPublished($request->query->getInt('page', 1))
        ]);
    }


    #[Route('/article/{slug}', name: 'post.show', methods: ['GET', 'POST'])]
    public function show(EntityManagerInterface $em, Post $post, PostRepository $postRepository, Request $request): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        if($this->getUser())
        {
            $comment->setAuthor($this->getUser());
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a bien été enregistré. Il sera soumis à modération dans les plus brefs délais');
            return $this->redirectToRoute('post.show', ['slug' => $post->getSlug()]);
        }

        return $this->render('pages/blog/show.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }
}
