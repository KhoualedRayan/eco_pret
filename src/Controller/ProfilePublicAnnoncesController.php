<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
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

class ProfilePublicAnnoncesController extends AbstractController
{
    #[Route('/profile/public/annonces/{id}', name: 'app_profile_public_annonces')]
    public function index($id, EntityManagerInterface $em): Response
    {

        if ($this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }
        $user = $em->getRepository(User::class)->find(intval($id));
        if (!$user) {
            return $this->redirectToRoute('app_home_page');
        }
        if($user->getId() == $this->getUser()->getId()){
            return $this->redirectToRoute('app_profile');
        }

        $annonces = $em->getRepository(Annonce::class)->findBy(['posteur' => $user, 'statut' => 'Disponible']);
        $annonceFinis = $em->getRepository(Annonce::class)->findBy(['posteur' => $user, 'statut' => 'Indisponible']);

        // Fonction de comparaison personnalisée pour trier par date de publication
        usort($annonces, function($a, $b) {
            return $b->getDatePublication() <=> $a->getDatePublication();
        });

        $categoriesService = $em->getRepository(CategorieService::class)->findAll();
        $categoriesMateriel = $em->getRepository(CategorieMateriel::class)->findAll();

        return $this->render('profile_public_annonces/index.html.twig', [
            'controller_name' => 'ProfilePublicAnnoncesController',
            'user' => $user,
            'annonces' => $annonces,
            'annoncesFinis' => $annonceFinis,
            'catMat' => $categoriesMateriel,
            'catService' => $categoriesService,
            'onglet' => "annonces",
        ]);
    }
}
