<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AnnonceService;
use App\Entity\AnnonceMateriel;
use Doctrine;
class HomePageController extends AbstractController
{
    #[Route('', name: 'app_home_page')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $annonceService = $entityManager->getRepository(AnnonceService::class)->findAll();
        $entityManager->clear();
        $annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->findAll();
        #$annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->findAll();
        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'annonceService' => $annonceService,
            'annonceMateriel' => $annonceMateriel,
        ]);
    }
}
