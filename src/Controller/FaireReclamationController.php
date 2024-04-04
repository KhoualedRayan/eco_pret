<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieReclamation;
use App\Entity\Transaction;
use App\Entity\Reclamation;
use App\Entity\User;
use App\Repository\CategorieReclamationRepository;
use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Request;



class FaireReclamationController extends AbstractController
{
    #[Route('/faire/reclamation', name: 'app_faire_reclamation')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getAbonnement() && $user->getAbonnement()->getNom() == "Admin") {
            return $this->redirectToRoute('app_admin');
        }
        if ($user->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }

        // Récupérer les transactions où l'utilisateur connecté est le posteur de l'annonce
        // l'annonce est une annonce de matériel et la transaction a été au moins validé 
        $transactions = array_filter($entityManager->getRepository(Transaction::class)->findByPandEnded($user), function($a) { return $a->getType() == "Materiel"; });

         return $this->render('faire_reclamation/index.html.twig', [
            'controller_name' => 'FaireReclamationController',
            'transactions' => $transactions,
        ]);
    }

    #[Route('/handle_form', name: 'handle_form')]
    public function handleFormSubmission(EntityManagerInterface $entityManager,Request $request): Response
    {
        $objet = $request->request->get('recla');
        $tr_id = $request->request->get('transaction_l');
        $description = $request->request->get('description');
        $titre = $request->request->get('titre');
        $reclamation = new Reclamation();
        $reclamation->setObjet($objet);
        $reclamation->setTitre($titre);
        $reclamation->setDescription($description);
        $reclamation->setUser($this->getUser());
        $reclamation->setStatutReclamation("En cours");
        $reclamation->setDatePoste(new \DateTime());
        // transaction à ajouter
        $reclamation->setTransaction($entityManager->getRepository(Transaction::class)->findOneBy(['id' => $tr_id]));
        $entityManager->persist($reclamation);
        $entityManager->flush();
        $this->addFlash('notifications', 'Votre réclamation a été envoyée avec succès !');

        return $this->redirectToRoute('app_home_page');
    }
}
