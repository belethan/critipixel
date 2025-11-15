<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\RegisterType;
use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/auth', name: 'auth_')]
final class AuthController extends AbstractController
{
    #[Route('/login', name: 'login', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('views/auth/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/register', name: 'register', methods: ['GET','POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        // Cas 1 : formulaire soumis NON valide -> renvoie 422
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('views/auth/register.html.twig', [
                'form' => $form,
            ], new Response('', 422));
        }

        // Cas 2 : formulaire soumis ET valide -> on persiste
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérer le mot de passe car mapped=false dans RegisterType
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPlainPassword($plainPassword);
            // Doctrine + UserListener vont générer le password hashé
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('auth_login');
        }

        // Cas 3 : simple GET
        return $this->render('views/auth/register.html.twig', [
            'form' => $form,
        ]);
    }

}
