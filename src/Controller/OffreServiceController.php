<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieService;
use App\Entity\CategorieMateriel;


class OffreServiceController extends AbstractController
{
    #[Route('/offre/service', name: 'app_offre_service')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieService::class)->findAll();
        return $this->render('offre_service/index.html.twig', [
            'controller_name' => 'OffreServiceController',
            'categories' => $categories,
        ]);
    }
}
