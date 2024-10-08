<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        JWTService $jwt,
        SendEmailService $mail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

             // Générer le token
            // Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // Envoyer l'e-mail
//            $mail->send(
//                'no-reply@openblog.test',
//                $user->getEmail(),
//                'Activation de votre compte sur Kilian-au',
//                'register',
//                compact('user', 'token') // ['user' => $user, 'token'=>$token]
//            );

//            $this->addFlash('success', 'Utilisateur inscrit, un e-mail a été envoyé.  ');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        //  'security/register.html.twig'
        //'bundles/HWIOAuthBundle/Connect/registration.html.twig'
        return $this->render('bundles/HWIOAuthBundle/Connect/registration.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifUser(
        $token,
        JWTService $jwt,
        UserRepository $usersRepository,
        EntityManagerInterface $em): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token);

            $user = $usersRepository->find($payload['user_id']);

            if($user && !$user->getIsVerified()){
                $user->setIsVerified(true);
                $em->flush();

                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('app_home');
            }
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }
}