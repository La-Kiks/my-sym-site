<?php

namespace App\Controller\Projects;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CooldownsV2Controller extends AbstractController
{
    #[Route('/cooldowns/v2', name: 'app_cooldowns_v2')]
    public function index(): Response
    {
        return $this->render('pages/projects/cooldowns_v2.html.twig', [
            'controller_name' => 'CooldownsV2Controller',
        ]);
    }
}
