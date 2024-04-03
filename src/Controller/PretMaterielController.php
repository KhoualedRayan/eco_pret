<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieMateriel;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\AnnonceMateriel;
use DateTime;
use App\Entity\User;
use App\Repository\CategorieMaterielRepository;

class PretMaterielController extends AbstractController
{
    #[Route('/pret/materiel', name: 'app_pret_materiel')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // redirige l'utilisateur vers la page de connexion si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if($this->getUser()){
            if ($this->getUser()->isSleepMode()) {
                return $this->redirectToRoute('app_sleep_mode');
            }
        }
        // s'il n'a pas d'abonnement
        if ($this->getUser()->getAbonnement() == null) {
            $this->addFlash('notifications', 'Vous devez posséder un abonnement pour pouvoir publier des annonces !');
            return $this->redirectToRoute('app_profile');
        }

        $categories = $entityManager->getRepository(CategorieMateriel::class)->findAll();
        return $this->render('pret_materiel/index.html.twig', [
            'controller_name' => 'PretMaterielController',
            'categories' => $categories,
        ]);
    }
    #[Route('/handle_form_submission', name: 'handle_form_submission')]
    public function handleFormSubmission(EntityManagerInterface $entityManager,Request $request, CategorieMaterielRepository $cmr): Response
    {
        $titre = $request->request->get('titre');
        $date = new DateTime();
        $prix = $request->request->get('prix');
        $description = $request->request->get('description');
        $categorie = $request->request->get('materiel');
        $duree_pret_valeur = intval($request->request->get('duree_pret_valeur', ''));
        $duree_pret = $request->request->get('duree_pret');


        $annonce = new AnnonceMateriel();
        $annonce->setTitre($titre);
        $annonce->setDescription($description);
        $annonce->setDatePublication($date);
        # si ça a été mis (durée non obligatoire)
        if ($duree_pret_valeur > 0) {
            $annonce->setMode($duree_pret);
            $annonce->setDureeH($duree_pret == 'jours' ? $duree_pret_valeur*24 : $duree_pret_valeur);
        }
        $annonce->setCategorie($cmr->findOneByNom($categorie));
        $annonce->setPrix($prix);
        $annonce->setPosteur($this->getUser());
        $annonce->setStatut("Disponible");

        $entityManager->persist($annonce);
        $entityManager->flush();
        $this->addFlash('notifications', 'Félicitations, votre annonce à été publiée !');
        return $this->redirectToRoute('app_profile_annonces');
    }
}
