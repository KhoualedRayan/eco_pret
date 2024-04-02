<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

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
            'onglet' => "infos",
        ]);
    }
}
