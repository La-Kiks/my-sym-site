<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CurriculumEnglishController extends AbstractController
{
    #[Route('/curriculum/english', name: 'app_curriculum_english')]
    public function index(): Response
    {
        return $this->render('pages/curriculum_english/index.html.twig', [
            'controller_name' => 'CurriculumEnglishController',
        ]);
    }
}
