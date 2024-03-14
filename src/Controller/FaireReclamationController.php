<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaireReclamationController extends AbstractController
{
    #[Route('/faire/reclamation', name: 'app_faire_reclamation')]
    public function index(): Response
    {
        return $this->render('faire_reclamation/index.html.twig', [
            'controller_name' => 'FaireReclamationController',
        ]);
    }

    #[Route('/handle_form', name: 'handle_form')]
    public function handleFormSubmission(EntityManagerInterface $entityManager,Request $request): Response
    {
        
        return $this->redirectToRoute('app_home_page');
    }
}
