<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Disponibilite;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class ProfilePlanningController extends AbstractController
{
    #[Route('/profile/planning', name: 'app_profile_planning')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if($this->getUser()){
            if ($this->getUser()->isSleepMode()) {
                return $this->redirectToRoute('app_sleep_mode');
            }
        }

        return $this->render('profile_planning/index.html.twig', [
            'controller_name' => 'ProfilePlanningController',
            'onglet' => "planning",
        ]);
    }

    #[Route('/ajax/activeSleepMode', name: 'activeSleepMode')]
    public function activeSleepMode(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->getUser()->setSleepMode(true);
        $entityManager->flush();
        $this->addFlash('notifications', 'Compte en mode sommeil');
        return new Response("OK");
    }

    #[Route('/ajax/planning_validate', name: 'planning_validate')]
    public function planning_validate(Request $request, EntityManagerInterface $entityManager): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $this->getUser()->clearDisponibilites();
        $data = $request->request;
        $list_mois_jours = json_decode($data->get('editedDays'), true);
        foreach ($list_mois_jours as $monthYear => $daysArray) {
            foreach ($daysArray as $day) {
                if($day !=null){
                    $dispo = new Disponibilite();
                    $date = DateTime::createFromFormat('Y-m-d', $day);
                    $dispo->setDate($date);
                    $entityManager->persist($dispo);
                    $this->getUser()->addDisponibilite($dispo);
                } 
            }
        }
        $entityManager->flush();
        $this->addFlash('notifications', 'Planning sauvegardé');
        return new Response("OK");
    }

    #[Route('/ajax/planning_init', name: 'planning_init')]
    public function planning_init(Request $request, EntityManagerInterface $entityManager): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $liste_dates = $this->getUser()->getDisponibilites(); 

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
