<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AbonnementRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\AnnonceService;
use App\Entity\AnnonceMateriel;
use App\Entity\Transaction;
use App\Entity\FileAttente;
use DateTime;
use App\Entity\Abonnement;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Repository\CategorieServiceRepository;
use App\Repository\CategorieMaterielRepository;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Notification;
use App\Entity\Disponibilite;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // si pas d'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // si c'est un admin il n'a pas accès à cette page
        if ($user->getAbonnement() && $user->getAbonnement()->getNom() == "Admin") {
            return $this->redirectToRoute('app_admin');
        }
        // s'il est en mode sommeil aussi
        if ($user->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }

        $abonnements = $entityManager->getRepository(Abonnement::class)->findAll();
        foreach ($abonnements as $key => $abonnement) {
            if($abonnement->getNom() == 'Admin')
                unset($abonnements[$key]);
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'abonnements' => $abonnements,
            'onglet' => "infos",
        ]);
    }

    #[Route('/ajax/handle_infos_form', name: 'handle_infos_form')]
    public function handleInfosForm(EntityManagerInterface $entityManager,Request $request, UserRepository $ur, AbonnementRepository $ar): Response
    {
        $infos = $request->request;
        $newUsername = $infos->get('username');
        $newEmail = $infos->get('email');
        $newNom = $infos->get('nom');
        $newPrenom = $infos->get('prenom');
        $newAbo = $infos->get('options');

        $user = $this->getUser();

        if ($newAbo != null) {
            $newAbo = $ar->findOneByName($newAbo);
            if ($user->getAbonnement() == null || $user->getAbonnement()->getNiveau() == 1) {
                if ($user->getAbonnement() == null) $user->setDateAbonnement(new DateTime());
                $user->setAbonnement($newAbo);
                $user->setNextAbonnement($newAbo);
            } else {
                $user->setNextAbonnement($newAbo);
            }
        }

        // Check if new username contains "@" or already exists
        if (strpos($newUsername, '@') == true) {
            return $this->json(['type' => 'erreurU', 'content' => 'Le nom d\'utilisateur ne peut pas contenir "@".']);
        } else if ($newUsername != $user->getUsername()) {
            if ($ur->findOneByUsername($newUsername)) 
                return $this->json(['type' => 'erreurU', 'content' => 'Le pseudo '.$newUsername.' est déjà utilisé par un autre utilisateur.']);
            else $user->setUsername($newUsername);
        }

        if ($newEmail != $user->getEmail()) {
            if ($ur->findOneByEmail($newEmail)) return $this->json(['type' => 'erreurE', 'content' => 'Il existe déjà un compte associé a l\'email '.$newEmail.'.']);
            else $user->setEmail($newEmail);
        }

        $user->setSurname($newNom);
        $user->setFirstName($newPrenom);

        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('notifications', 'Vos modifications ont été enregistrées avec succès !');
        return $this->json(['type' => 'success', 'content' =>$this->generateUrl('app_profile')]);
    }

    #[Route('/ajax/mdpForm', name: 'mdp_form')]
    public function checkMDP(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $data = $request->request;

        $motDePasseActuel = $data->get('motDePasseActuel') == null ? "" : $data->get('motDePasseActuel');
        $nouveauMotDePasse = $data->get('nouveauMotDePasse');
        $confirmNouveauMDP = $data->get('confirmNouveauMDP');

        $bonMDPActuel = $userPasswordHasher->isPasswordValid($this->getUser(), $motDePasseActuel);
        $motDePasseSecurise = strlen($nouveauMotDePasse) >= 6;
        $confirmerCorrect = $nouveauMotDePasse == $confirmNouveauMDP;

        if ($bonMDPActuel && $confirmerCorrect && $motDePasseSecurise) {

            $this->getUser()->setPassword(
                $userPasswordHasher->hashPassword(
                    $this->getUser(),
                    $nouveauMotDePasse
                )
            );
            $entityManager->persist($this->getUser());
            $entityManager->flush();

            $this->addFlash('notifications', 'Votre mot de passe a été modifié avec succès !');

            return new Response("OK");
        } else {
            if (!$motDePasseSecurise) {
                return new Response("nouveauMotDePasse");
            } else if (!$confirmerCorrect) {
                return new Response("confirmNouveauMDP");
            } else {
                return new Response("motDePasseActuel");
            }
        }
    }
    #[Route('/ajax/desabo_form', name: 'desabo_form')]
    public function checkDesabo(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $this->getUser()->setNextAbonnement(null);
        $entityManager->persist($this->getUser());
        $entityManager->flush();
        $this->addFlash('notifications', 'Vous êtes bien désabonné !');
        return new Response("");
    }

}


