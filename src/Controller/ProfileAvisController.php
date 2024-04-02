<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TransactionRepository;

class ProfileAvisController extends AbstractController
{
    #[Route('/profile/avis', name: 'app_profile_avis')]
    public function index(TransactionRepository $transactionRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }

        // Récupère les transactions en tant qu'offrant
        $offrantTransactions = $transactionRepository->findTransactionsAsOffrant($this->getUser());

        // Récupère les transactions en tant que client
        $clientTransactions = $transactionRepository->findBy(['client' => $this->getUser()]);

        return $this->render('profile_avis/index.html.twig', [
            'controller_name' => 'ProfileAvisController',
            'onglet' => "avis",
            'note_globale' => $this->getUser()->getNote(),
            'offrantTransactions' => $offrantTransactions,
            'clientTransactions' => $clientTransactions,
        ]);
    }
}
