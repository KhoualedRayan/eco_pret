<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SleepModeController extends AbstractController
{
    #[Route('/sleep/mode', name: 'app_sleep_mode')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
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
        $entityManager->flush();
        $this->addFlash('notifications', 'Mode sommeil désactivé');
        return $this->redirectToRoute('app_profile');
    }
}
