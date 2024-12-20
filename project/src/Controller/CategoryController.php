<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/{slug}', name: 'category.index', methods: ['GET'])]
    public function index(
        Category $category,
        PostRepository $postRepository,
        Request $request
    ): Response
    {
        $posts = $postRepository->findPublished($request->query->getInt('page', 1), $category);
        return $this->render('pages/category/contact_index.html.twig', [
            'category' => $category,
            'posts' => $posts
        ]);
    }
}
