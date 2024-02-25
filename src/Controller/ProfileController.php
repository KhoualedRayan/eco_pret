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
use App\Entity\Abonnement;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $entityManager->clear();
        $annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->findBy(['posteur' => $this->getUser()]);
        // Fusionner les annonces dans un seul tableau
        $annonces = array_merge($annonceService, $annonceMateriel);

        // Fonction de comparaison personnalisée pour trier par date de publication
        usort($annonces, function($a, $b) {
            return $b->getDatePublication() <=> $a->getDatePublication();
        });

        $abonnements = $entityManager->getRepository(Abonnement::class)->findAll();
        foreach ($abonnements as $key => $abonnement) {
            if($abonnement->getNom() == 'Admin')
                unset($abonnements[$key]);
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'errors' => $errors,
            'edit_mode' => $edit_mode,
            'annonces' => $annonces,
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
        $this->addFlash('notifications', 'Vos modifications ont été enregistrées avec succès !');
        return $this->redirectToRoute('app_profile');
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
    #[Route('/ajax/modif_annonce', name: 'modif_annonce')]
    public function modifAnnonce(Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = $data->get('annonceId');
        $annonceType = $data->get('annonceType');
        $nouveauPrix = $data->get('nouveauPrix');
        $nouveauTitre = $data->get('nouveauTitre');
        $nouvelleDescription = $data->get('nouvelleDescription');
        var_dump($annonceId);
        var_dump($annonceType);
        var_dump($nouveauPrix);
        var_dump($nouveauTitre);
        var_dump($nouvelleDescription);


        if($annonceType === 'Materiel'){

            #$annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->find($annonceId);
            /*
            $annonceMateriel->setPrix($nouveauPrix);
            $annonceMateriel->setTitre($nouveauTitre);
            $annonceMateriel->setDescription($nouvelleDescription);
            $entityManager->persist($annonceMateriel);
            $entityManager->flush();
            */
        }


        $this->addFlash('notifications', 'Votre annonce a été modifié avec succès !' );
        return new Response("OK");
    }
}


