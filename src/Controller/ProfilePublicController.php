<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;


class ProfilePublicController extends AbstractController
{
    #[Route('/profile/public/{id}', name: 'app_profile_public')]
    public function index($id, EntityManagerInterface $em,TransactionRepository $transactionRepository): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }
        $user = $em->getRepository(User::class)->find(intval($id));
        if (!$user) {
            return $this->redirectToRoute('app_home_page');
        }
        if($user->getId() == $this->getUser()->getId()){
            return $this->redirectToRoute('app_profile');
        }

        // Récupère les transactions en tant qu'offrant
        $offrantTransactions = $transactionRepository->findTransactionsAsOffrant($user);

        // Récupère les transactions en tant que client
        $clientTransactions = $transactionRepository->findBy(['client' => $user]);
        
        return $this->render('profile_public/index.html.twig', [
            'controller_name' => 'ProfilePublicController',
            'user' => $user,
            'note_globale' => $user->getNote(),
            'offrantTransactions' => $offrantTransactions,
            'clientTransactions' => $clientTransactions,
        ]);
    }
}
