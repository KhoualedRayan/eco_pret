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
        if($this->getUser()){
            if($this->getUser()->isBusy()){
                $this->getUser()->setSleepMode(true);
                $entityManager->flush();
            }
            if ($this->getUser()->isSleepMode()) {
                return $this->redirectToRoute('app_sleep_mode');
            }
        }
        // redirige l'utilisateur vers la page de connexion si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
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
        $duree_pret_valeur = $request->request->get('duree_pret_valeur');
        $duree_pret = $request->request->get('duree_pret');
        $duree_pret = $duree_pret_valeur . ' ' . $duree_pret;


        $annonce = new AnnonceMateriel();
        $annonce->setTitre($titre);
        $annonce->setDescription($description);
        $annonce->setDatePublication($date);
        $annonce->setDuree($duree_pret);
        $annonce->setCategorie($cmr->findOneByNom($categorie));
        $annonce->setPrix($prix);
        $annonce->setPosteur($this->getUser());
        $annonce->setStatut("Disponible");

        $entityManager->persist($annonce);
        $entityManager->flush();
        $this->addFlash('notifications', 'Félicitations, votre annonce à été publiée !');
        return $this->redirectToRoute('app_home_page');
    }
}
