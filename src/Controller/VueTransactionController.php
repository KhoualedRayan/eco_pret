<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;

class VueTransactionController extends AbstractController
{
    private int $identifiant;
    #[Route('/vue/transaction/{id}', name: 'app_vue_transaction')]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $identifiant = $id;
        $transaction = $em->getRepository(Transaction::class)->find($id);
        if (!$transaction) {
            return $this->redirectToRoute('app_home_page');
        }
        return $this->render('vue_transaction/index.html.twig', [
            'controller_name' => 'VueTransactionController',
            'transaction' => $transaction,
        ]);
    }
    public function modifierNote(EntityManagerInterface $em):Response{
        // à faire
        //nécessite les notes d'abord...
        return $this->redirectToRoute('app_vue_transaction');
    }
}
