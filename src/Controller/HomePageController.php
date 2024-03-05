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
use DateTime;
class HomePageController extends AbstractController
{
    #[Route('', name: 'app_home_page')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $annonceService = $entityManager->getRepository(AnnonceService::class)->findAll();
        $entityManager->clear();
        $annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->findAll();
        // Fusionner les annonces dans un seul tableau
        $annonces = array_merge($annonceService, $annonceMateriel);

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


        if ($annonceType === 'Materiel') {
            $annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->findAll();
            $annonce = $entityManager->getRepository(AnnonceMateriel::class)->find($annonceId);
            if(!$annonce){
                return new Response("Erreur l'annonce n'existe plus");
            }
            $transaction = $this->creationTransaction($annonce,$entityManager);
            #$annonce->addTransaction($transaction);
            #$entityManager->persist($annonce);
            #$entityManager->flush();
            $this->addFlash('notifications', "Vous pouvez désormais communiquer avec le créateur de l'annonce !");
        }
        else if ($annonceType === 'Service') {

        }

        return new Response("Erreur lors de l'emprunt");
    }
    public function creationTransaction(Annonce $annonceMateriel,EntityManagerInterface $entityManagerInterface){
        $transaction = new Transaction();
        $date = new DateTime();
        $transaction->setStatutTransaction("En cours");
        $transaction->setAnnonce(4);
        $transaction->setClient($this->getUser());
        $transaction->setDateTransaction($date);
        $entityManagerInterface->persist($transaction);
        $entityManagerInterface->flush();
        return $transaction;
    }
}
