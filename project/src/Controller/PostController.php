<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request
    ): Response
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
    public function show(
        EntityManagerInterface $em,
        Post $post,
        Request $request
    ): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        $user = $this->getUser();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $user)
        {
            $comment->setAuthor($user);

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

    #[Route('/mes-articles', name: 'post.my_articles', methods: ['GET'])]
    public function myArticles(
        PostRepository $postRepository,
        Request $request,
        UserInterface $user
    ): Response
    {
        if(!$user instanceof User){
            throw new \Exception('Invalid user type');
        }

        $page = $request->query->getInt('page', 1);

        if($page){
            $posts = $postRepository->findPostByUser($page, $user);
        } else {
            $posts = null;
        }

        return $this->render('pages/blog/edit_articles.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/nouvel-article', name: 'post.write', methods: ['GET', 'POST'])]
    public function newArticle(
        EntityManagerInterface $em,
        Request $request,
        UserInterface $user
    ): Response
    {
        if(!$user instanceof User){
            throw new \Exception('Invalid user type');
        }

        $post = new Post();
        $post->setUser($user);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Article créé');
            return $this->redirectToRoute('post.my_articles');
        }

        return $this->render('pages/blog/new_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit-article/{slug}', name: 'post.edit', methods: ['GET', 'POST'])]
    public function editArticle(
        EntityManagerInterface $em,
        Post $post,
        Request $request,
        UserInterface $user
    ): Response
    {
        if(!$user instanceof User){
            throw new \Exception('Invalid user type');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Article édité.');
            return $this->redirectToRoute('post.my_articles');
        }

        return $this->render('pages/blog/new_article.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);

    }
}
