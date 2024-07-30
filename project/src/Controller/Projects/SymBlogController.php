<?php

namespace App\Controller\Projects;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SymBlogController extends AbstractController
{
    #[Route('/sym-blog', name: 'app_sym_blog')]
    public function index(): Response
    {
        return $this->render('pages/projects/blog_symfony.html.twig', [
            'controller_name' => 'SymBlogController',
        ]);
    }
}
