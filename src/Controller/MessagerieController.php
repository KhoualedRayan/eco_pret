<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Entity\User;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class MessagerieController extends AbstractController
{
    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $transactions = $entityManager->getRepository(Transaction::class)->findByClientOrPosteur($this->getUser());
        usort($transactions, function ($a, $b) {
            if (!$b->getMessages()->isEmpty() && !$a->getMessages()->isEmpty()) {
                return $b->getMessages()->last()->getDateEnvoi() <=> $a->getMessages()->last()->getDateEnvoi();
            }


            return $b->getId() <=> $a->getId();
        });

        return $this->render('messagerie/index.html.twig', [
            'controller_name' => 'MessagerieController',
            'transactions' => $transactions,
        ]);
    }

    #[Route('/messagerie_refresh', name: 'massgerie_refresh')]
    public function refreshMessages(EntityManagerInterface $entityManager): Response{
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } 
        if ($this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }
        $transactions = $entityManager->getRepository(Transaction::class)->findByClientOrPosteur($this->getUser());
        usort($transactions, function ($a, $b) {
            if (!$b->getMessages()->isEmpty() && !$a->getMessages()->isEmpty()) {
                return $b->getMessages()->last()->getDateEnvoi() <=> $a->getMessages()->last()->getDateEnvoi();
            }


            return $b->getId() <=> $a->getId();
        });

        $html = $this->renderView('messagerie/messagesRefresh.html.twig', [
            'transactions' => $transactions,
        ]);

        return $this->json(['html' => $html]);
    }

    #[Route('/charger-messages/{id}', name: 'charger_messages')]
    public function chargerMessages(int $id, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager): Response
    {
        $transaction = $transactionRepository->find($id);
        $messages = [];
        if ($transaction) {
            foreach ($transaction->getMessages() as $msg) {
                if($msg->getExpediteur() != $this->getUser()){
                    if(!$msg->isAEteLu()){
                        $msg->setAEteLu(true);
                        $entityManager->flush();
                    }
                }
                $contenuDecrypte = $this->decrypterMessage($msg->getContenu());
                $messages[] = [
                    'id' => $msg->getId(),
                    'contenu' => $contenuDecrypte,
                    'dateEnvoi' => $msg->getDateEnvoi(),
                    'expediteur' => $msg->getExpediteur(),
                    'transaction' => $msg->getTransaction(),
                ];
            }
        }
        
        $messages =$messages;
        // G�n�rer le HTML pour les messages
        $html = $this->renderView('messagerie/messages.html.twig', [
            'messages' => $messages,
        ]);

        return $this->json(['html' => $html, 'statut' => $transaction->getStatutTransaction(), 'userRole' => $transaction->getRole($this->getUser())]);
    }

    #[Route('/ajax/nouveau_message', name: 'nouveau_message')]
    public function nouveauMessage(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $transactionId = $data->get('transactionId');
        $expediteur = $data->get('expediteur');
        $message = $data->get('message');
        $transaction = $entityManager->getRepository(Transaction::class)->find($transactionId);
        if($transaction){
            $user = $entityManager->getRepository(User::class)->findOneByUsername($expediteur);
            if($user){
                $this->envoieMessage($entityManager, $transaction, $message, $user);
                return new Response('OK');
            }
        }

        $this->addFlash('notifications', "Erreur lors de l'envoie d'un message!");
        return new Response("Erreur lors de l'envoi du message ...");
    }
    function envoieMessage(EntityManagerInterface $entityManager,Transaction $transaction, string $contenu,User $user){
        $message = new Message();
        $date = new DateTime();
        $contenu = $this->crypterMessage($contenu);
        $message->setContenu($contenu);
        $message->setDateEnvoi($date);
        $message->setTransaction($transaction);
        $message->setExpediteur($user);
        $message->setAEteLu(false);
        $entityManager->persist($message);
        $entityManager->flush();
    }
    function crypterMessage($message)
    {
        $cleSecrete = $_ENV['APP_CLE_CRYPTAGE'];
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $messageCrypte = openssl_encrypt($message, 'aes-256-cbc', $cleSecrete, 0, $iv);
        return base64_encode($iv . $messageCrypte);
    }
    function decrypterMessage($messageCrypte)
    {
        $cleSecrete = $_ENV['APP_CLE_CRYPTAGE'];
        $messageCrypte = base64_decode($messageCrypte);
        $iv = substr($messageCrypte, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $messageCrypte = substr($messageCrypte, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($messageCrypte, 'aes-256-cbc', $cleSecrete, 0, $iv);
    }

    #[Route('/ajax/validation/{id}', name: 'validation')]
    public function openValider(int $id, TransactionRepository $tr): Response
    {
        if (!$this->getUser()) {
            return new Response("ERROR");
        }
        $transaction = $tr->find($id);
        if ($transaction && $transaction->getStatutTransaction() != "Valide") {
            return new Response($this->renderView('messagerie/validerDialog.html.twig', [
                                        'transaction' => $transaction,
                                        'statut' => $transaction->getStatutTransaction(), 'userRole' => $transaction->getRole($this->getUser())
                                    ]));
        } else if ($transaction) {
            return new Response("");
        }
       return new Response("ERROR");
    }

    #[Route('/validation1_form', name: 'validation1')]
    public function validation1(Request $request, TransactionRepository $tr, EntityManagerInterface $em) : Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $id = $data->get('id');

        $transaction = $tr->find(intval($id));
        if ($transaction) {
            // quelqu'un d'autre a modifié avant vous
            if ($transaction->getStatutTransaction() != "En cours") {
                $this->addFlash('notifications', "Erreur lors de la validation. Veuillez réessayer.");
            } else {
                $prix = $data->get('prix');
                if ($transaction->getAnnonce()->getType() == "Materiel") {
                    $duree = $data->get('duree');
                    $modalite = $data->get('duree_pret');
                    $transaction->setDureeFinal($duree." ".$modalite);
                }
                
                $transaction->setPrixFinal($prix);
                $transaction->setStatutTransaction("Valide-".$transaction->getRole($this->getUser()));
                $em->persist($transaction);
                $em->flush();
                $this->addFlash('notifications', "Vous avez bien validé la transaction ! En attente de l'autre utilisateur.");
                return $this->redirectToRoute('app_messagerie');
            }
        }
    }
}
