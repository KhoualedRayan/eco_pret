<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileAvisController extends AbstractController
{
    #[Route('/profile/avis', name: 'app_profile_avis')]
    public function index(): Response
    {
        return $this->render('profile_avis/index.html.twig', [
            'controller_name' => 'ProfileAvisController',
        ]);
    }
}
