<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieReclamation;
use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;



class FaireReclamationController extends AbstractController
{
    #[Route('/faire/reclamation', name: 'app_faire_reclamation')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $objets = $entityManager->getRepository(CategorieReclamation::class)->findAll();

        $userId = $this->getUser()->getId();

        // Récupérer les transactions où l'utilisateur connecté est le client ou le posteur de l'annonce
        $transactions = $entityManager->getRepository(Transaction::class)->createQueryBuilder('t')
            ->leftJoin('t.annonce', 'a')
            ->where('t.client = :userId OR a.posteur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();

         return $this->render('faire_reclamation/index.html.twig', [
            'controller_name' => 'FaireReclamationController',
            'objets' => $objets,
            'transactions' => $transactions,
        ]);
    }

    #[Route('/handle_form', name: 'handle_form')]
    public function handleFormSubmission(EntityManagerInterface $entityManager,Request $request): Response
    {
        
        $this->addFlash('notifications', 'Votre réclamation a été envoyée avec succès !');

        return $this->redirectToRoute('app_home_page');
    }
}
