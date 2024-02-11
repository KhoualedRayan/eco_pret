<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieService;
use App\Entity\CategorieMateriel;


class OffreServiceController extends AbstractController
{
    #[Route('/offre/service', name: 'app_offre_service')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieService::class)->findAll();
        return $this->render('offre_service/index.html.twig', [
            'controller_name' => 'OffreServiceController',
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

        $annonce = new AnnonceService();
        $annonce->setTitre($titre);
        $annonce->setDescription($description);
        $annonce->setDatePublication($date);
        $annonce->setDuree($duree_pret);
        $annonce->setPrix($prix);
        $annonce->setPosteur($user);
        $annonce->setStatut("Disponible");

        $entityManager->persist($annonce);
        $entityManager->flush();

        return $this->redirectToRoute('app_home_page');
    }
}
