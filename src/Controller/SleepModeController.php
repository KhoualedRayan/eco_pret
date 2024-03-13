<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Disponibilite;
use DateTime;


class SleepModeController extends AbstractController
{
    #[Route('/sleep/mode', name: 'app_sleep_mode')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if($this->getUser()){
            if ($this->getUser()->isSleepMode()==false) {
                return $this->redirectToRoute('app_home_page');
            }
        }
    
        return $this->render('sleep_mode/index.html.twig', [
            'controller_name' => 'SleepModeController',
        ]);
    }

    #[Route('/form_sleepmode', name: 'form_sleepmode')]
    public function formSleepMode(EntityManagerInterface $entityManager,Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $this->getUser()->setSleepMode(false);
        $dispo = new Disponibilite();
        $today = new DateTime();
        $this->getUser()->removeDisponibiliteByDate($today);
        $entityManager->flush();
        $this->addFlash('notifications', 'Mode sommeil désactivé');
        return $this->redirectToRoute('app_profile');
    }
}
