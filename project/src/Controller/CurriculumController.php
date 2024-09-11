<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CurriculumController extends AbstractController
{
    #[Route('/curriculum', name: 'app_curriculum')]
    public function index(): Response
    {
        return $this->render('pages/curriculum/index.html.twig', [
            'controller_name' => 'CurriculumController',
        ]);
    }
}
