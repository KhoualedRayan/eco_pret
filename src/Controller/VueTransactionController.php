<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Notification;
use DateTime;
use App\Service\Outils;

class VueTransactionController extends AbstractController
{
    private int $identifiant;

    private $outils;

    public function __construct(Outils $outils)
    {
        $this->outils = $outils;
    }

    #[Route('/vue/transaction/{id}', name: 'app_vue_transaction')]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $transaction = $em->getRepository(Transaction::class)->find($id);
        
        if (!$transaction) {
            return $this->redirectToRoute('app_home_page');
        }

        $note = ($transaction->getNoteOffrant() !== null) ? $transaction->getNoteOffrant() : $transaction->getNoteClient();
        $note = ($note !== null) ? $note : 3; 
    
        return $this->render('vue_transaction/index.html.twig', [
            'controller_name' => 'VueTransactionController',
            'transaction' => $transaction,
            'note' => $note,
        ]);
    }



    #[Route('/ajax/validerNoteClient', name: 'valider_note_client')]
    public function validerNoteClient(Request $request, EntityManagerInterface $em): Response
    {

        $idTransaction = $request->request->get('id');
        $note = $request->request->get('note');
        $commentaire = $request->request->get('commentaire');
        $username = $request->request->get('username');

        $transaction = $em->getRepository(Transaction::class)->find($idTransaction);
        $titreTransaction = $transaction->getAnnonce()->getTitre();

        $transaction->setNoteClient($note);

        $transaction->setCommentaireClient($commentaire);

        $this->addFlash('notifications', 'Votre commentaire et votre note ont été confirmés et envoyés avec succès !');

        $notif = "Vous avez recu un avis de $username concernant votre transaction $titreTransaction !";
        $notification = new Notification();
        $notification->setContenu($this->outils->crypterMessage($notif));
        $notification->setAEteLu(false);
        $notification->setDateEnvoi(new DateTime());
        $notification->setUser($transaction->getAnnonce()->getPosteur());
        
        $em->persist($notification);
        $em->flush();

        return new Response("OK");


    }

    #[Route('/ajax/validerNotePosteur', name: 'valider_note_posteur')]
    public function validerNotePosteur(Request $request, EntityManagerInterface $em): Response
    {

        $idTransaction = $request->request->get('id');
        $note = $request->request->get('note');
        $commentaire = $request->request->get('commentaire');
        $username = $request->request->get('username');

        $transaction = $em->getRepository(Transaction::class)->find($idTransaction);

        $titreTransaction = $transaction->getAnnonce()->getTitre();

        $transaction->setNoteOffrant($note);

        $transaction->setCommentaireOffrant($commentaire);

        $this->addFlash('notifications', 'Votre commentaire et votre note ont été confirmés et envoyés avec succès !');

        $notif = "Vous avez recu un avis de $username concernant votre transaction $titreTransaction !";
        $notification = new Notification();
        $notification->setContenu($this->outils->crypterMessage($notif));
        $notification->setAEteLu(false);
        $notification->setDateEnvoi(new DateTime());
        $notification->setUser($transaction->getClient());
        
        $em->persist($notification);
        $em->flush();

        return new Response("OK");


    }


}
