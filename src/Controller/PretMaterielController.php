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
use App\Entity\Abonnement;

class PretMaterielController extends AbstractController
{
    #[Route('/pret/materiel', name: 'app_pret_materiel')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieMateriel::class)->findAll();
        return $this->render('pret_materiel/index.html.twig', [
            'controller_name' => 'PretMaterielController',
            'categories' => $categories,
        ]);
    }
    #[Route('/handle_form_submission', name: 'handle_form_submission')]
    public function handleFormSubmission(EntityManagerInterface $entityManager,Request $request): Response
    {
        $users = $entityManager->getRepository(User::class);
        $user = $users->find(1);
        //$posteur_id = $this->getUser();
        $titre = $request->request->get('titre');
        $date = new DateTime();
        $prix = $request->request->get('prix');
        $description = $request->request->get('description');
        $duree_pret_valeur = $request->request->get('duree_pret_valeur');
        $duree_pret = $request->request->get('duree_pret');
        $duree_pret += $duree_pret_valeur;

        // Créez une instance de votre entité et affectez les valeurs
        $annonce = new AnnonceMateriel();
        $annonce->setTitre($titre);
        $annonce->setDescription($description);
        $annonce->setDatePublication($date);
        $annonce->setDuree($duree_pret);
        $annonce->setPrix($prix);
        $annonce->setPosteur($user);
        $annonce->setStatut("Disponible");

        // Obtenez l'EntityManager et persistez votre entité
        $entityManager->persist($annonce);
        $entityManager->flush();

        // Retourne la réponse HTTP contenant le code JavaScript
        return $this->redirectToRoute('app_pret_materiel');
    }
}
