<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\UserProvider;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        # il peut ne pas avoir d'abonnement
        if ($user && $user->getAbonnement() && $user->getAbonnement()->getNom() == "Admin") {
            return $this->redirectToRoute('app_admin');
        } else if ($user) {
            return $this->redirectToRoute('app_home_page');
        }

        // get the login error if there is one
        $errorM = $authenticationUtils->getLastAuthenticationError() == null ? null : "Identifiant ou mot de passe incorrect.";
        
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $errorM]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
