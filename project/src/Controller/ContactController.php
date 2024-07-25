<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Service\SendEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        SendEmailService $mail,

    ): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user_mail =  $form->get('email')->getData();
            $name = $form->get('name')->getData() ?? $user_mail;
            $message = $form->get('message')->getData();
            $copy = $form->get('copy')->getData();

            // Envoyer l'e-mail
            $mail->send(
                'kilian@kilian-au.fr',
                'kilian@kilian-au.fr',
                'Contacter Kilian-au',
                'contact',
                compact('name', 'message', 'user_mail')
            );

            if($copy){
                // Envoyer la copie
                $mail->send(
                    'kilian@kilian-au.fr',
                    $user_mail,
                    'Copie du message envoyé à Kilian-au',
                    'contact',
                    compact('name', 'message')
                );

            }

            $this->addFlash('success', 'E-mail envoyé avec succès.');

            return $this->redirectToRoute('app_home');

        }

        return $this->render('contact/contact_index.html.twig', [
            'form' => $form,
        ]);
    }
}
