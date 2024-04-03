<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute("app_login");
        }
        if (!$user->getAbonnement() or $user->getAbonnement()->getNom() != "Admin") {
            return $this->redirectToRoute("app_home_page");
        }

        $reclamations = $entityManager->getRepository(Reclamation::class)->findAllOrderDate();
        $rec_traitees = [];
        $rec_non_traitees = [];
        foreach ($reclamations as $rec) {
            if ($rec->getStatutReclamation() == 'En cours') {
                $rec_non_traitees[] = $rec;
            } else {
                $rec_traitees[] = $rec;
            }
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'reclamationsNonTraitees' => $rec_non_traitees,
            'reclamationsTraitees' => $rec_traitees,
        ]);
    }

    #[Route('/ajax/reclamation/{id}', name: 'app_reclamation')]
    public function showReclamation(EntityManagerInterface $entityManager, $id): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->findOneBy(['id' => $id]);
        return $this->render('admin/reclamationWindow.html.twig', [
            'controller_name' => 'AdminController',
            'reclamation' => $reclamation,
        ]);
    }
}
