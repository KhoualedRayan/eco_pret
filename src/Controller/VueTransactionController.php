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
use App\Repository\TransactionRepository;

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
        //Je suis le client
        $idTransaction = $request->request->get('id');
        $note = $request->request->get('note');
        $commentaire = $request->request->get('commentaire');
        $username = $request->request->get('username');

        $transaction = $em->getRepository(Transaction::class)->find($idTransaction);
        $titreTransaction = $transaction->getAnnonce()->getTitre();

        //On change la note global du posteur
        $transactions = $em->getRepository(Transaction::class)->findByClientOrPosteur($transaction->getAnnonce()->getPosteur() );
        $nbNotes = 1;
        $totalNotes = $note;
        foreach($transactions as $t){
            if($t->getId() != $transaction->getId()){
                if ($t->getClient()->getId() == $transaction->getAnnonce()->getPosteur()->getId()) {
                    if ($t->getNoteOffrant() != null) {
                        $totalNotes += $t->getNoteOffrant();
                        $nbNotes++;
                    }
                } else {
                    if ($t->getNoteClient() != null) {
                        $totalNotes += $t->getNoteClient();
                        $nbNotes++;
                    }
                }
            }
        }
        $transaction->getAnnonce()->getPosteur()->setNote($totalNotes / $nbNotes);
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
        //Je suis le posteur
        $idTransaction = $request->request->get('id');
        $note = $request->request->get('note');
        $commentaire = $request->request->get('commentaire');
        $username = $request->request->get('username');

        $transaction = $em->getRepository(Transaction::class)->find($idTransaction);

        $titreTransaction = $transaction->getAnnonce()->getTitre();

        //On change la note global du client
        $transactions = $em->getRepository(Transaction::class)->findByClientOrPosteur($transaction->getClient());
        $nbNotes = 1;
        $totalNotes = $note;
        foreach ($transactions as $t) {
            if ($t->getId() != $transaction->getId()) {
                if ($t->getClient()->getId() == $transaction->getClient()->getId()) {
                    if ($t->getNoteOffrant() != null) {
                        $totalNotes += $t->getNoteOffrant();
                        $nbNotes++;
                    }
                } else {
                    if ($t->getNoteClient() != null) {
                        $totalNotes += $t->getNoteClient();
                        $nbNotes++;
                    }
                }
            }
        }
        $transaction->getClient()->setNote($totalNotes / $nbNotes);

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
