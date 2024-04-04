<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reclamation;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Outils;

class AdminController extends AbstractController
{
    private $outils;

    public function __construct(Outils $outils)
    {
        $this->outils = $outils;
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute("app_login");
        }
        if (!$user->getAbonnement() or $user->getAbonnement()->getNom() != "Admin") {
            return $this->redirectToRoute("app_home_page");
        }

        $form = $request->request->all();
        if ($form != []) {
            $rec = $entityManager->getRepository(Reclamation::class)->findOneBy(['id' => $form['recId']]);
            # évite de retraiter un truc déjà traité (en rechargeant la page par exemple)
            if ($rec->getStatutReclamation() != "Traite") {
                $contenu = "";
                if ($rec->getObjet() != "Litige") {
                    $contenu = "Un administrateur a répondu à votre réclamation '".$rec->getTitre()."' avec :\n".$form['reponse'];
                    $rec->setReponse($form['reponse']);
                } else {
                    if ($form['resultat'] == 'Rejet') {
                        $contenu = "Un administrateur a rejeté votre réclamation '".$rec->getTitre()."' avec comme message :\n".$form['reponse'];
                        // 0 -> Rejet, autre -> Remboursement
                        $rec->setReponse("0-".$form['reponse']);
                    } else {
                        $montant = $form['montant'];
                        $contenu = "Un administrateur a accepté votre réclamation '".$rec->getTitre()."' avec comme message :\n".$form['reponse']."\nVous avez reçu un remboursement de ".$montant." euros.";
                        $rec->setReponse($montant."-".$form['reponse']);
                    }
                }
                $rec->setAdministrateur($user);
                $rec->setStatutReclamation("Traite");
                $this->outils->envoieNotificationA($entityManager,$contenu, $rec->getUser());
                $entityManager->persist($rec);
                $entityManager->flush();
            }
            
        }

        $reclamations = $entityManager->getRepository(Reclamation::class)->findAllOrderDate();
        $rec_traitees = [];
        $rec_non_traitees = [];
        foreach ($reclamations as $rec) {
            if ($rec->getStatutReclamation() == 'En cours') {
                $rec_non_traitees[] = $rec;
            } else {
                $rec_traitees[] = $rec;
            }
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'reclamationsNonTraitees' => $rec_non_traitees,
            'reclamationsTraitees' => $rec_traitees,
        ]);
    }

    #[Route('/ajax/reclamation/{id}', name: 'app_reclamation')]
    public function showReclamation(EntityManagerInterface $entityManager, $id): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->findOneBy(['id' => $id]);
        return $this->render('admin/reclamationWindow.html.twig', [
            'controller_name' => 'AdminController',
            'reclamation' => $reclamation,
        ]);
    }
}
