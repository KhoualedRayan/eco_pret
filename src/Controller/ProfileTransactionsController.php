<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AnnonceService;
use App\Entity\AnnonceMateriel;
use App\Entity\Transaction;
use App\Entity\FileAttente;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Annonce;
use App\Entity\User;
use App\Entity\Notification;
use DateTime;

class ProfileTransactionsController extends AbstractController
{
    #[Route('/profile/transactions', name: 'app_profile_transactions')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $transactionsClient = $entityManager->getRepository(Transaction::class)->findBy(['client' => $this->getUser()]);
        $transactionsPosteur = $entityManager->getRepository(Transaction::class)->findBy(['posteur' => $this->getUser()]);
        $filesDattentes = $this->getUser()->getAnnoncesOuJattends();
        $annoncesEnAttente = []; // Pour stocker les annonces où l'utilisateur n'est pas premier

        foreach ($filesDattentes as $fileDattente) {
            // Vérifiez si l'utilisateur n'est pas le premier de la file
            // Cela dépend de la structure de votre entité FileAttente, par exemple :
            if ( $fileDattente != $fileDattente->getAnnonce()->getAttentes()->first() ) {
                 $annonce = $fileDattente->getAnnonce();
                 $annoncesEnAttente[] = $annonce;
            }
        }

        return $this->render('profile_transactions/index.html.twig', [
            'controller_name' => 'ProfileTransactionsController',
            'transactionsClient' => $transactionsClient,
            'transactionsPosteur' => $transactionsPosteur,
            'annoncesEnAttente' => $annoncesEnAttente,
            'onglet' => "transactions",
        ]);
    }

    #[Route('/ajax/suppr_transaction', name: 'suppr_transaction')]
    public function supprTransaction(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $transactionId = (int) $data->get('transactionId');

        $transaction = $entityManager->getRepository(Transaction::class)->find($transactionId);
        $annonce = $transaction->getAnnonce();
        if ($transaction) {
            $files = $transaction->getAnnonce()->getAttentes();
            if($files && !$files->isEmpty()){
                //On supprime l'utilisateur de la file d'attente
                $transaction->getAnnonce()->removeAttente($files->first());
                $entityManager->flush();

                //On met le prochain utilisateur si il y'en a en transaction
                $nouvelleFile = $transaction->getAnnonce()->getAttentes()->first();
                if($nouvelleFile){
                    //Nouvelle transaction avec le premier de la file d'attente
                    $nouveauUser = $nouvelleFile->getUser();
                    $nouvelleTransaction = $this->creationTransaction($transaction->getAnnonce(),$entityManager,$nouveauUser);
                    $this->getUser()->removeDemande($transaction);
                    $annonce->setTransaction($nouvelleTransaction);
                    $entityManager->flush();
                    $contenuNotif1 = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : " . $annonce->getTitre() .".";
                    $this->envoieNotificationA($entityManager, $contenuNotif1, $annonce->getPosteur());
                    $contenuNotif2 = "Vous avez une nouvelle transaction avec ". $nouvelleFile->getUser()->getUsername() . ". Allez dans votre profil pour finaliser la transaction !";
                    $this->envoieNotificationA($entityManager, $contenuNotif2, $annonce->getPosteur());
                    $contenuNotif3 = "Vous avez une nouvelle transaction avec " . $annonce->getPosteur()->getUsername() . ". Allez dans votre profil pour finaliser la transaction !";
                    $this->envoieNotificationA($entityManager, $contenuNotif3, $nouvelleFile->getUser());
                }else{
                    //Personne dans la file d'attente
                    $contenuNotif = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : ". $annonce->getTitre(). ".";
                    $this->envoieNotificationA($entityManager,$contenuNotif,$annonce->getPosteur());
                    $this->getUser()->removeDemande($transaction);
                    $annonce->setTransaction(null);
                    $entityManager->flush();
                }

                $this->addFlash('notifications', 'Vous avez désisté la transaction en succès !');
                return new Response("OK");
            }

        }

        return new Response("Erreur sur la suppresion de l'annonce");
    }

    #[Route('/ajax/se_desister', name: 'se_desister')]
    public function seDesister(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');

        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        $annoncesOuJattends = $this->getUser()->getAnnoncesOuJattends();
        foreach($annoncesOuJattends as $file){
            if($file->getAnnonce() == $annonce){
                $entityManager->remove($file);
                $entityManager->flush();
                $this->addFlash('notifications', "Vous avez quitté la file d'attente avec succès !");
                return new Response("OK");
            }
        }
        $this->addFlash('notifications', "Erreur sur la désistation.");
        return new Response("Erreur sur la désistation.");
    }

    #[Route('/ajax/annul_transaction', name: 'annul_transaction')]
    public function annulerTransactionAvecClient(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $transactionId = (int) $data->get('transactionId');
        $transaction = $entityManager->getRepository(Transaction::class)->find($transactionId);
        $annonce = $transaction->getAnnonce();
        if ($transaction) {
            $files = $transaction->getAnnonce()->getAttentes();
            if ($files && !$files->isEmpty()) {
                //On supprime l'utilisateur de la file d'attente
                $ancienUser = $annonce->getAttentes()->first()->getUser();
                $transaction->getAnnonce()->removeAttente($files->first());
                $entityManager->flush();

                //On met le prochain utilisateur si il y'en a en transaction
                $nouvelleFile = $transaction->getAnnonce()->getAttentes()->first();
                if ($nouvelleFile) {
                    //Nouvelle transaction avec le premier de la file d'attente
                    $nouveauUser = $nouvelleFile->getUser();
                    $nouvelleTransaction = $this->creationTransaction($transaction->getAnnonce(), $entityManager, $nouveauUser);
                    $ancienUser->removeDemande($transaction);
                    $annonce->setTransaction($nouvelleTransaction);
                    $entityManager->flush();
                    $contenuNotif1 = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : " . $annonce->getTitre() . ".";
                    $this->envoieNotificationA($entityManager, $contenuNotif1, $ancienUser);
                    $contenuNotif2 = "Vous avez une nouvelle transaction avec " . $this->getUser()->getUsername() . ". Allez dans votre profil pour finaliser la transaction !";
                    $this->envoieNotificationA($entityManager, $contenuNotif2, $nouvelleFile->getUser());
                } else {
                    //Personne dans la file d'attente
                    $contenuNotif1 = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : " . $annonce->getTitre() . ".";
                    $this->envoieNotificationA($entityManager, $contenuNotif1, $ancienUser);
                    $ancienUser->removeDemande($transaction);
                    $annonce->setTransaction(null);
                    $entityManager->flush();
                }

                $this->addFlash('notifications', 'Vous avez annuler la transaction avec le client avec succès !');
                return new Response("OK");
            }
        }
        $this->addFlash('notifications', "Erreur lors de l'annulation de la transaction avec le client !");
        return new Response("Erreur lors de l'annulation de la transaction avec le client ...");
    }

    public function creationTransaction(Annonce $annonce, EntityManagerInterface $entityManagerInterface,User $user)
    {
        $transaction = new Transaction();
        $date = new DateTime();
        $transaction->setStatutTransaction("En cours");
        $transaction->setAnnonce($annonce);
        $transaction->setClient($user);
        $transaction->setPosteur($annonce->getPosteur());
        $transaction->setDateTransaction($date);
        $entityManagerInterface->persist($transaction);
        $entityManagerInterface->flush();
        return $transaction;
    }
    public function envoieNotificationA(EntityManagerInterface $entityManagerInterface,string $contenu,User $user)
    {
        $notif = new Notification();
        $date = new DateTime();
        $messageCrypter = $this->crypterMessage($contenu);
        $notif->setAEteLu(false);
        $notif->setContenu($messageCrypter);
        $notif->setDateEnvoi($date);
        $notif->setUser($user);
        $entityManagerInterface->persist($notif);
        $entityManagerInterface->flush();
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