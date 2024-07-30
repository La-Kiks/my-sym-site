<?php

namespace App\Controller\Projects;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProdraftController extends AbstractController
{
    #[Route('/prodraft', name: 'app_prodraft')]
    public function index(): Response
    {
        return $this->render('pages/projects/prodraft.html.twig', [
            'controller_name' => 'ProdraftController',
        ]);
    }
}
