<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Annonce;
use App\Entity\CategorieMateriel;
use App\Entity\CategorieService;

class ProfileAnnoncesController extends AbstractController
{
    #[Route('/profile/annonces', name: 'app_profile_annonces')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // ?????
        if($this->getUser()->isBusy()){
            $this->getUser()->setSleepMode(true);
            $entityManager->flush();
        }
        if ($this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
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
            $entityManager->persist($annonceMateriel);

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
            $entityManager->persist($annonceService);
            $entityManager->flush();
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
}
