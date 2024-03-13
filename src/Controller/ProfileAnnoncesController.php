<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Annonce;
use App\Entity\CategorieMateriel;
use App\Entity\CategorieService;
use App\Repository\CategorieServiceRepository;
use App\Repository\CategorieMaterielRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\AnnonceService;
use App\Entity\AnnonceMateriel;
use DateTime;
use App\Entity\DatePonctuelleService;
use App\Entity\Recurrence;
class ProfileAnnoncesController extends AbstractController
{
    #[Route('/profile/annonces', name: 'app_profile_annonces')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if($this->getUser()){
            if ($this->getUser()->isSleepMode()) {
                return $this->redirectToRoute('app_sleep_mode');
            }
        }

        $annonces = $entityManager->getRepository(Annonce::class)->findBy(['posteur' => $this->getUser()]);

        // Fonction de comparaison personnalisée pour trier par date de publication
        usort($annonces, function($a, $b) {
            return $b->getDatePublication() <=> $a->getDatePublication();
        });

        $categoriesService = $entityManager->getRepository(CategorieService::class)->findAll();
        $categoriesMateriel = $entityManager->getRepository(CategorieMateriel::class)->findAll();

        return $this->render('profile_annonces/index.html.twig', [
            'controller_name' => 'ProfileAnnoncesController',
            'annonces' => $annonces,
            'catMat' => $categoriesMateriel,
            'catService' => $categoriesService,
            'onglet' => "annonces",
        ]);
    }

    #[Route('/ajax/modif_annonce', name: 'modif_annonce')]
    public function modifAnnonce(Request $request, EntityManagerInterface $entityManager, CategorieMaterielRepository $cmr, CategorieServiceRepository $csr): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');
        $annonceType = $data->get('annonceType');
        $nouveauPrix = $data->get('nouveauPrix');
        $nouveauTitre = $data->get('nouveauTitre');
        $nouvelleDescription = $data->get('nouvelleDescription');
        $nouvelleCategorie = $data->get('nouvelleCategorie');

        if($annonceType === 'Materiel'){
            $duree_pret_valeur = $data->get('nouvelleDureeValeur');
            $duree_pret = $data->get('nouvelleDureePeriode');
            $duree_pret = $duree_pret_valeur . ' ' . $duree_pret;
            $annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->find($annonceId);
            $annonceMateriel->setPrix($nouveauPrix);
            $annonceMateriel->setTitre($nouveauTitre);
            $annonceMateriel->setDuree($duree_pret);
            $annonceMateriel->setDescription($nouvelleDescription);
            $annonceMateriel->setCategorie($cmr->findOneByNom($nouvelleCategorie));
            $entityManager->flush();

            $this->addFlash('notifications', 'Votre annonce a été modifié avec succès !');
            return new Response("OK");
        }
        else if($annonceType === 'Service'){
            $annonceService = $entityManager->getRepository(AnnonceService::class)->find($annonceId);
            $annonceService->setPrix($nouveauPrix);
            $annonceService->setTitre($nouveauTitre);
            $annonceService->setDescription($nouvelleDescription);
            $annonceService->setCategorie($csr->findOneByNom($nouvelleCategorie));
            $this->modifDatesService($entityManager, $annonceService, $request);
            $this->addFlash('notifications', 'Votre annonce a été modifié avec succès !');
            return new Response("OK");
        }

        return new Response("Erreur");
    }
    #[Route('/ajax/suppr_annonce', name: 'suppr_annonce')]
    public function supprAnnonce(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');

        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        if($annonce){
            $entityManager->remove($annonce);
            $entityManager->flush();
            $this->addFlash('notifications', 'Votre annonce a été supprimée avec succès !');
            return new Response("OK");
        }

        return new Response("Erreur sur la suppresion de l'annonce");
    }

    public function modifDatesService(EntityManagerInterface $entityManager,AnnonceService $annonce, Request $request){
        $annonce->getRecurrence()->clear();
        $annonce->getDatePoncts()->clear();
        $additionalDates = $request->request->all()['additional_date'] ?? null;
        $additionalRecu = $request->request->all()['additional_recurrence'] ?? null;
        $additionalEnds = $request->request->all()['additional_ends'] ?? null;

        $init_date = $request->request->get('date_pret');
        $init_reccu = $request->request->get('recurrence');
        dump($request->request->all());
        #FIRST DATE
        if($init_reccu == ""){
            #DATE PONCTUELLE
            $exist = $entityManager->getRepository(DatePonctuelleService::class)->findOneBy(['date'=> new DateTime($init_date)]);
            $first_date = new DatePonctuelleService();
            $first_date->setDate(new DateTime($init_date));
            if($exist){
                $annonce->addDatePonct($exist);
            }else{
                $entityManager->persist($first_date);
                $annonce->addDatePonct($first_date);
            }
        }else{
            #RECCURENCE
            $exist = $entityManager->getRepository(Recurrence::class)->findOneBy(['date_debut' => new DateTime($init_date), 'date_fin' => new DateTime($additionalEnds[2]), 'typeRecurrence' => $init_reccu]);
            $first_reccu = new Recurrence();
            $first_date_debut = new DateTime($init_date);
            $first_reccu->setDateDebut($first_date_debut);
            $first_date_fin = new DateTime($additionalEnds[2]);
            $first_reccu->setDateFin($first_date_fin);
            $first_reccu->setTypeRecurrence($init_reccu);
            if ($exist) {
                $annonce->addRecurrence($exist);
            } else {
                $entityManager->persist($first_reccu);
                $annonce->addRecurrence($first_reccu);
            }
        }



        $index = 0;
        $index_ends = 3;
        if(is_array($additionalDates)) {
            foreach ($additionalDates as $date) {
                if($additionalRecu[$index]==""){
                    $exist = $entityManager->getRepository(DatePonctuelleService::class)->findOneBy(['date' => new DateTime($date)]);
                    $dateponct = new DatePonctuelleService();
                    $dateponct->setDate(new DateTime($date));
                    if($exist){
                        $annonce->addDatePonct($exist);
                    }else{
                        $entityManager->persist($dateponct);
                        $annonce->addDatePonct($dateponct);
                    }
                }else{
                    $exist = $entityManager->getRepository(Recurrence::class)->findOneBy(['date_debut' =>  new DateTime($date), 'date_fin' => new DateTime($additionalEnds[$index_ends]), 'typeRecurrence' => $additionalRecu[$index]]);
                    $reccu = new Recurrence();
                    $date_debut = new DateTime($date);
                    $reccu->setDateDebut($date_debut);
                    $date_fin = new DateTime($additionalEnds[$index_ends]);
                    $reccu->setDateFin($date_fin);
                    $reccu->setTypeRecurrence($additionalRecu[$index]);
                    if($exist){
                        $annonce->addRecurrence($exist);
                    }else{
                        $entityManager->persist($reccu);
                        $annonce->addRecurrence($reccu);
                    }
                    $index_ends++;
                }
                $index++;

            }
        }
        $entityManager->flush();
    }

}
