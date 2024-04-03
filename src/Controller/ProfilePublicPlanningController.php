<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProfilePublicPlanningController extends AbstractController
{
    #[Route('/profile/public/planning/{id}', name: 'app_profile_public_planning')]
    public function index($id, EntityManagerInterface $em): Response
    {

        $user = $em->getRepository(User::class)->find(intval($id));
        if (!$user) {
            return $this->redirectToRoute('app_home_page');
        }
        if($user->getId() == $this->getUser()->getId()){
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile_public_planning/index.html.twig', [
            'controller_name' => 'ProfilePublicPlanningController',
            'user' => $user,
        ]);
    }

       #[Route('/ajax/planning_public_init', name: 'planning_public_init')]
    public function planning_init(Request $request, EntityManagerInterface $entityManager): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $id = $request->get('id');
        
        $user = $entityManager->getRepository(User::class)->find(intval($id));

        $liste_dates = $user->getDisponibilites(); 

        $dates_par_mois = [];
        
        foreach ($liste_dates as $date) {
            $annee = $date->getDate()->format("Y");
            $mois = $date->getDate()->format("n");
        
            $key = $annee . "-" . $mois;
        
            if (!isset($dates_par_mois[$key])) {
                $dates_par_mois[$key] = [];
            }
        
            $dates_par_mois[$key][] = $date->getDate();
        }
        $jsonContent = json_encode($dates_par_mois);
        return new Response($jsonContent);
    }

}
