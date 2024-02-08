<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PretMaterielController extends AbstractController
{
    #[Route('/pret/materiel', name: 'app_pret_materiel')]
    public function index(): Response
    {
        return $this->render('pret_materiel/index.html.twig', [
            'controller_name' => 'PretMaterielController',
        ]);
    }
}
