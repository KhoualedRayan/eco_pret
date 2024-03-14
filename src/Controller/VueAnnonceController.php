<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Annonce;
use App\Entity\FileAttente;

class VueAnnonceController extends AbstractController
{
    #[Route('/vue/annonce/{id}', name: 'app_vue_annonce')]
    public function index($id, EntityManagerInterface $em): Response
    {
        $annonce = $em->getRepository(Annonce::class)->find(intval($id));
        if (!$annonce) {
            return $this->redirectToRoute('app_home_page');
        }
        $l = $em->getRepository(FileAttente::class)->findAll();
        $l = array_filter($l, function ($a) use ($annonce) {return $a->getAnnonce() == $annonce; });
        usort($l, function($a, $b) {
            return $a->getRang() <=> $b->getRang();
        });
    
        // Initialiser la liste de résultats
        $listeClients = [];
    
        // Parcourir chaque entité A triée
        foreach ($l as $a) {
            // Ajouter l'entité B associée à l'entité A dans la liste des résultats
            $listeClients[] = $a->getUser();
        }
    
        return $this->render('vue_annonce/index.html.twig', [
            'controller_name' => 'VueAnnonceController',
            'annonce' => $annonce,
            'listeClients' => $listeClients,
        ]);
    }
}
