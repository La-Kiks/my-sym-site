<?php

namespace App\Controller\Projects;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CooldownsV1Controller extends AbstractController
{
    #[Route('/cooldowns/v1', name: 'app_cooldowns_v1')]
    public function index(): Response
    {
        return $this->render('pages/projects/cooldowns_v1.html.twig', [
            'controller_name' => 'CooldownsV1Controller',
        ]);
    }
}
