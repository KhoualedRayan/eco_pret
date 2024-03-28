<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;


class VueTransactionController extends AbstractController
{
    private int $identifiant;
    #[Route('/vue/transaction/{id}', name: 'app_vue_transaction')]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $identifiant = $id;
        $transaction = $em->getRepository(Transaction::class)->find($id);

        dump($identifiant);

        if (!$transaction) {
            return $this->redirectToRoute('app_home_page');
        }
        return $this->render('vue_transaction/index.html.twig', [
            'controller_name' => 'VueTransactionController',
            'transaction' => $transaction,
        ]);
    }



    #[Route('/ajax/validerNoteClient', name: 'valider_note_client')]
    public function validerNoteClient(Request $request, EntityManagerInterface $em): Response
    {   

        $idTransaction = $request->request->get('idTransaction');

        //$this->addFlash('notifications', 'Votre mot de passe a été eee avec succès !');

        dump($idTransaction);

        return $this->redirectToRoute('app_login');
        
        
    }

    #[Route('/ajax/validerNotePosteur', name: 'valider_note_posteur')]
    public function validerNotePosteur(Request $request, EntityManagerInterface $em): Response
    {
        
        $idTransaction = $request->request->get('id');

        $transaction = $em->getRepository(Transaction::class)->find($idTransaction);

        dump($transaction);

        $transaction->setNoteOffrant(4);

        dump($transaction);

        $this->addFlash('notifications', 'Votre commentaire et votre note ont été confirmés et envoyés avec succès !');

        $em->persist($transaction);
        $em->flush();

        return new Response("OK");
        
        
    }


}
