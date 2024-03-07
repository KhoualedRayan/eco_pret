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
use App\Entity\Annonce;
use App\Entity\Transaction;
use App\Entity\Abonnement;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Entity\CategorieMateriel;
use App\Entity\CategorieService;
use App\Repository\CategorieServiceRepository;
use App\Repository\CategorieMaterielRepository;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\VarDumper\VarDumper;


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

        $annonces = $entityManager->getRepository(Annonce::class)->findBy(['posteur' => $this->getUser()]);
        $transactionsClient = $entityManager->getRepository(Transaction::class)->findBy(['client' => $this->getUser()]);
        $transactionsPosteur = $entityManager->getRepository(Transaction::class)->findBy(['posteur' => $this->getUser()]);
        // Fonction de comparaison personnalisée pour trier par date de publication
        usort($annonces, function($a, $b) {
            return $b->getDatePublication() <=> $a->getDatePublication();
        });

        $abonnements = $entityManager->getRepository(Abonnement::class)->findAll();
        foreach ($abonnements as $key => $abonnement) {
            if($abonnement->getNom() == 'Admin')
                unset($abonnements[$key]);
        }
        $categoriesService = $entityManager->getRepository(CategorieService::class)->findAll();
        #$entityManager->clear();
        $categoriesMateriel = $entityManager->getRepository(CategorieMateriel::class)->findAll();

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'errors' => $errors,
            'edit_mode' => $edit_mode,
            'annonces' => $annonces,
            'abonnements' => $abonnements,
            'catMat' => $categoriesMateriel,
            'catService' => $categoriesService,
            'transactionsClient' => $transactionsClient,
            'transactionsPosteur' => $transactionsPosteur,
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

        if (!$user->getAbonnement()) {
            $user->setAbonnement($newAbo);
            $user->setNextAbonnement($newAbo);
        }



        if ($user->getNextAbonnement() != $newAbo) {
            // cas Standard, Standard + Premium -> Premium, Premium + Payer x euros (différence)
            if ($user->getNextAbonnement()->getNiveau() == 1) {
                $user->setAbonnement($newAbo);
                $user->setNextAbonnement($newAbo);
                // signaler qu'il doit payer
            } else {
                $user->setNextAbonnement($newAbo);
            }

            if (!$user->getAbonnement()) {
                $user->setAbonnement($newAbo);
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
        $user->setAbonnement($newAbo);

        $user->setNextAbonnement($newAbo);



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
    #[Route('/ajax/desaboForm', name: 'desabo_form')]
    public function checkDesabo(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Marque l'utilisateur comme désabonné
        $this->getUser()->setAbonnement(null);
        $this->getUser()->setNextAbonnement(null);

        $entityManager->persist($this->getUser());
        $entityManager->flush();

        $this->addFlash('notifications', 'Vous êtes bien désabonné !');

        return $this->redirectToRoute('app_profile');

    }
    #[Route('/ajax/modif_annonce', name: 'modif_annonce')]
    public function modifAnnonce(Request $request, EntityManagerInterface $entityManager, CategorieMaterielRepository $cmr, CategorieServiceRepository $csr): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');
        $annonceType = $data->get('annonceType');
        $nouveauPrix = $data->get('nouveauPrix');
        $nouveauTitre = $data->get('nouveauTitre');
        $nouvelleDescription = $data->get('nouvelleDescription');
        $nouvelleCategorie = $data->get('nouvelleCategorie');

        if($annonceType === 'Materiel'){
            $duree_pret_valeur = $data->get('nouvelleDureeValeur');
            $duree_pret = $data->get('nouvelleDureePeriode');
            $duree_pret = $duree_pret_valeur . ' ' . $duree_pret;
            $annonceMateriel = $entityManager->getRepository(AnnonceMateriel::class)->find($annonceId);
            $annonceMateriel->setPrix($nouveauPrix);
            $annonceMateriel->setTitre($nouveauTitre);
            $annonceMateriel->setDuree($duree_pret);
            $annonceMateriel->setDescription($nouvelleDescription);
            $annonceMateriel->setCategorie($cmr->findOneByNom($nouvelleCategorie));
            $entityManager->persist($annonceMateriel);

            $entityManager->flush();

            $this->addFlash('notifications', 'Votre annonce a été modifié avec succès !');
            return new Response("OK");
        }
        else if($annonceType === 'Service'){
            $annonceService = $entityManager->getRepository(AnnonceService::class)->find($annonceId);
            $annonceService->setPrix($nouveauPrix);
            $annonceService->setTitre($nouveauTitre);
            $annonceService->setDescription($nouvelleDescription);
            $annonceService->setCategorie($csr->findOneByNom($nouvelleCategorie));
            $entityManager->persist($annonceService);
            $entityManager->flush();
            $this->addFlash('notifications', 'Votre annonce a été modifié avec succès !');
            return new Response("OK");
        }

        return new Response("Erreur");
    }
    #[Route('/ajax/suppr_annonce', name: 'suppr_annonce')]
    public function supprAnnonce(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');

        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        if($annonce){
            $entityManager->remove($annonce);
            $entityManager->flush();
            $this->addFlash('notifications', 'Votre annonce a été supprimée avec succès !');
            return new Response("OK");
        }

        return new Response("Erreur sur la suppresion de l'annonce");
    }
    #[Route('/ajax/suppr_transaction', name: 'suppr_transaction')]
    public function supprTransaction(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $transactionId = (int) $data->get('transactionId');

        $transaction = $entityManager->getRepository(Transaction::class)->find($transactionId);
        if ($transaction) {
            $transaction->getAnnonce()->removeGensEnAttente($this->getUser());
            $entityManager->remove($transaction);
            $entityManager->flush();
            $this->addFlash('notifications', 'Votre transaction a été annulé avec succès !');
            return new Response("OK");
        }

        return new Response("Erreur sur la suppresion de l'annonce");
    }
}


