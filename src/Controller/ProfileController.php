<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AbonnementRepository;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\AnnonceService;
use App\Entity\AnnonceMateriel;
use App\Entity\Abonnement;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $session = new Session();
        $edit_mode = $session->has('errors');
        if ($edit_mode) {
            $errors = $session->get('errors');
            $session->remove('errors');
        } else {
            $errors = [];
        }
        $annonceService = $entityManager->getRepository(AnnonceService::class)->findBy(['posteur' => $this->getUser()]);
        $annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->findBy(['posteur' => $this->getUser()]);
        $abonnements = $entityManager->getRepository(Abonnement::class)->findAll();
        foreach ($abonnements as $key => $abonnement) {
            if($abonnement->getNom() == 'Admin')
                unset($abonnements[$key]);
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'errors' => $errors,
            'edit_mode' => $edit_mode,
            'annonce_service' => $annonceService,
            'annonce_materiel' => $annonceMateriel,
            'abonnements' => $abonnements,
        ]);
    }

    #[Route('/handle_infos_form', name: 'handle_infos_form')]
    public function handleInfosForm(EntityManagerInterface $entityManager,Request $request, UserRepository $ur, AbonnementRepository $ar): Response
    {
        $infos = $request->request;
        $newUsername = $infos->get('username');
        $newEmail = $infos->get('email');
        $newNom = $infos->get('nom');
        $newPrenom = $infos->get('prenom');
        $newAbo = $infos->get('options');

        $errors = [];
        $user = $this->getUser();

        $newAbo = $ar->findOneByName($newAbo);

        if ($user->getNextAbonnement() != $newAbo) {
            // cas Standard, Standard + Premium -> Premium, Premium + Payer x euros (différence)
            if ($user->getNextAbonnement()->getNiveau() == 1) {
                $user->setAbonnement($newAbo);
                $user->setNextAbonnement($newAbo);
                // signaler qu'il doit payer
            } else {
                $user->setNextAbonnement($newAbo);
            }
        }

        // Check if new username contains "@" or already exists
        if (strpos($newUsername, '@') == true) {
            $errors['username'] = 'Le nom d\'utilisateur ne peut pas contenir "@".';
        } else if ($newUsername != $user->getUsername()) {
            if ($ur->findOneByUsername($newUsername)) {
                $errors['username'] = 'Le pseudo '.$newUsername.' est déjà utilisé par un autre utilisateur.';
            } else {
                $user->setUsername($newUsername);
            }
        }

        if ($newEmail != $user->getEmail()) {
            if ($ur->findOneByEmail($newEmail)) {
                $errors['email'] = 'Il existe déjà un compte associé a l\'email '.$newEmail.'.';
            } else {
                $user->setEmail($newEmail);
            }
        }

        if ($errors != []) {
            $session = new Session();
            $session->set('errors', $errors);
            return $this->redirectToRoute('app_profile');
        }

        $user->setSurname($newNom);
        $user->setFirstName($newPrenom);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('notificationInfos', 'Vos modifications ont été enregistrées avec succès !');
        return $this->redirectToRoute('app_profile');
    }
}


