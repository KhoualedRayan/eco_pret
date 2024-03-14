<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AnnonceService;
use App\Entity\AnnonceMateriel;
use App\Entity\Annonce;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Transaction;
use App\Entity\FileAttente;
use DateTime;
use App\Entity\Notification;
use App\Service\Outils;

class HomePageController extends AbstractController
{
    private $outils;

    public function __construct(Outils $outils)
    {
        $this->outils = $outils;
    }

    #[Route('', name: 'app_home_page')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()){
            if ($this->getUser()->isSleepMode()) {
                return $this->redirectToRoute('app_sleep_mode');
            }
        }

        $annonces = $entityManager->getRepository(Annonce::class)->findAll();
        // Fonction de comparaison personnalisée pour trier par date de publication
        usort($annonces, function($a, $b) {
            return $b->getDatePublication() <=> $a->getDatePublication();
        });


        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'annonces' => $annonces,
        ]);
    }

    #[Route('/ajax/emprunt', name: 'emprunt_annonce')]
    public function emprunterAnnnonce(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');
        $annonceType = $data->get('annonceType');
        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        if (!$annonce) {
            return new Response("Erreur l'annonce n'existe plus");
        }
        if ($annonce->getAttentes()->isEmpty()) {
            $file = new FileAttente();
            $file->setUser($this->getUser());
            $file->setAnnonce($annonce);
            $file->setRang(0);
            $entityManager->persist($file);
            $entityManager->flush();
            $transaction = $this->creationTransaction($annonce, $entityManager);
            $annonce->addAttente($file);
            $annonce->setTransaction($transaction);
            $entityManager->flush();
            $this->envoieNotification($annonce, $entityManager);
        }else{
            $derniereFile = $annonce->getAttentes()->last();
            $file = new FileAttente();
            $file->setUser($this->getUser());
            $file->setAnnonce($annonce);
            $file->setRang($derniereFile->getRang()+1);
            $entityManager->persist($file);
            $entityManager->flush();
            $annonce->addAttente($file);
            $entityManager->flush();
        }
        $this->addFlash('notifications', "Vous pouvez désormais communiquer avec le créateur de l'annonce !");
        return new Response("OK");
    }
    public function creationTransaction(Annonce $annonce,EntityManagerInterface $entityManagerInterface){
        $transaction = new Transaction();
        $date = new DateTime();
        $transaction->setStatutTransaction("En cours");
        $transaction->setAnnonce($annonce);
        $transaction->setClient($this->getUser());
        $transaction->setDateTransaction($date);
        $entityManagerInterface->persist($transaction);
        $entityManagerInterface->flush();
        return $transaction;
    }
    public function envoieNotification(Annonce $annonce, EntityManagerInterface $entityManagerInterface){
        $notif = new Notification();
        $date = new DateTime();
        $contenu = "Vous avez une nouvelle transaction ! Allez dans votre profil pour finaliser votre transaction avec ". $this->getUser()->getUsername().".";
        $messageCrypter = $this->outils->crypterMessage($contenu);
        $notif->setAEteLu(false);
        $notif->setContenu($messageCrypter);
        $notif->setDateEnvoi($date);
        $notif->setUser($annonce->getPosteur());
        $entityManagerInterface->persist($notif);
        $entityManagerInterface->flush();
    }
}
