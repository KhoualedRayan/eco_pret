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
        } else if ($this->getUser( ) && $this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }
        $transactions = $entityManager->getRepository(Transaction::class)->findByClientOrPosteur($this->getUser());

        return $this->render('messagerie/index.html.twig', [
            'controller_name' => 'MessagerieController',
            'transactions' => $transactions,
        ]);
    }

    #[Route('/charger-messages/{id}', name: 'charger_messages')]
    public function chargerMessages(int $id, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->find($id);
        $messages = [];

        if ($transaction) {
            $messages = $transaction->getMessages();
        }
        // Générer le HTML pour les messages
        $html = $this->renderView('messagerie/messages.html.twig', [
            'messages' => $messages,
        ]);

        return $this->json(['html' => $html]);
    }

    #[Route('/ajax/nouveau_message', name: 'nouveau_message')]
    public function nouveauMessage(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $transactionId = $data->get('transactionId');
        $destinataire = $data->get('destinataire');
        $message = $data->get('message');
        $user= null;
        $transaction = $entityManager->getRepository(Transaction::class)->find($transactionId);
        if($transaction){
            if($transaction->getClient()->getUsername() == $destinataire){
                $user = $transaction->getPosteur();
            }else if($transaction->getPosteur()->getUsername() == $destinataire) {
                $user = $transaction->getClient();
            }
            if($user){
                $this->envoieMessage($entityManager, $transaction, $message, $user);
                $this->addFlash('notifications', "Vous avez envoyé un nouveau message !");
                return new Response('OK');
            }
        }

        $this->addFlash('notifications', "Erreur lors de l'annulation de la transaction avec le client !");
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
}
