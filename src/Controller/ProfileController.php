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
use App\Entity\FileAttente;
use DateTime;
use App\Entity\Abonnement;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Entity\CategorieMateriel;
use App\Entity\CategorieService;
use App\Repository\CategorieServiceRepository;
use App\Repository\CategorieMaterielRepository;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Notification;


class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
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
        $filesDattentes = $this->getUser()->getAnnoncesOuJattends();
        $annoncesEnAttente = []; // Pour stocker les annonces où l'utilisateur n'est pas premier

        foreach ($filesDattentes as $fileDattente) {
            // Vérifiez si l'utilisateur n'est pas le premier de la file
            // Cela dépend de la structure de votre entité FileAttente, par exemple :
            if ( $fileDattente != $fileDattente->getAnnonce()->getAttentes()->first() ) {
                 $annonce = $fileDattente->getAnnonce();
                 $annoncesEnAttente[] = $annonce;
            }
        }

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
            'annoncesEnAttente' => $annoncesEnAttente,
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
        $annonce = $transaction->getAnnonce();
        if ($transaction) {
            $files = $transaction->getAnnonce()->getAttentes();
            if($files && !$files->isEmpty()){
                //On supprime l'utilisateur de la file d'attente
                $transaction->getAnnonce()->removeAttente($files->first());
                $entityManager->flush();

                //On met le prochain utilisateur si il y'en a en transaction
                $nouvelleFile = $transaction->getAnnonce()->getAttentes()->first();
                if($nouvelleFile){
                    //Nouvelle transaction avec le premier de la file d'attente
                    $nouveauUser = $nouvelleFile->getUser();
                    $nouvelleTransaction = $this->creationTransaction($transaction->getAnnonce(),$entityManager,$nouveauUser);
                    $this->getUser()->removeDemande($transaction);
                    $annonce->setTransaction($nouvelleTransaction);
                    $entityManager->flush();
                    $contenuNotif1 = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : " . $annonce->getTitre() .".";
                    $this->envoieNotificationA($entityManager, $contenuNotif1, $annonce->getPosteur());
                    $contenuNotif2 = "Vous avez une nouvelle transaction avec ". $nouvelleFile->getUser()->getUsername() . ". Allez dans votre profil pour finaliser la transaction !";
                    $this->envoieNotificationA($entityManager, $contenuNotif2, $annonce->getPosteur());
                    $contenuNotif3 = "Vous avez une nouvelle transaction avec " . $annonce->getPosteur()->getUsername() . ". Allez dans votre profil pour finaliser la transaction !";
                    $this->envoieNotificationA($entityManager, $contenuNotif3, $nouvelleFile->getUser());
                }else{
                    //Personne dans la file d'attente
                    $contenuNotif = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : ". $annonce->getTitre(). ".";
                    $this->envoieNotificationA($entityManager,$contenuNotif,$annonce->getPosteur());
                    $this->getUser()->removeDemande($transaction);
                    $annonce->setTransaction(null);
                    $entityManager->flush();
                }

                $this->addFlash('notifications', 'Vous avez désisté la trannsaction en succès !');
                return new Response("OK");
            }

        }

        return new Response("Erreur sur la suppresion de l'annonce");
    }

    #[Route('/ajax/se_desister', name: 'se_desister')]
    public function seDesister(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');

        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        $annoncesOuJattends = $this->getUser()->getAnnoncesOuJattends();
        foreach($annoncesOuJattends as $file){
            if($file->getAnnonce() == $annonce){
                $entityManager->remove($file);
                $entityManager->flush();
                $this->addFlash('notifications', "Vous avez quitté la file d'attente avec succès !");
                return new Response("OK");
            }
        }
        $this->addFlash('notifications', "Erreur sur la désistation.");
        return new Response("Erreur sur la désistation.");
    }
    #[Route('/ajax/annul_transaction', name: 'annul_transaction')]
    public function annulerTransactionAvecClient(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $transactionId = (int) $data->get('transactionId');
        $transaction = $entityManager->getRepository(Transaction::class)->find($transactionId);
        $annonce = $transaction->getAnnonce();
        if ($transaction) {
            $files = $transaction->getAnnonce()->getAttentes();
            if ($files && !$files->isEmpty()) {
                //On supprime l'utilisateur de la file d'attente
                $ancienUser = $annonce->getAttentes()->first()->getUser();
                $transaction->getAnnonce()->removeAttente($files->first());
                $entityManager->flush();

                //On met le prochain utilisateur si il y'en a en transaction
                $nouvelleFile = $transaction->getAnnonce()->getAttentes()->first();
                if ($nouvelleFile) {
                    //Nouvelle transaction avec le premier de la file d'attente
                    $nouveauUser = $nouvelleFile->getUser();
                    $nouvelleTransaction = $this->creationTransaction($transaction->getAnnonce(), $entityManager, $nouveauUser);
                    $ancienUser->removeDemande($transaction);
                    $annonce->setTransaction($nouvelleTransaction);
                    $entityManager->flush();
                    $contenuNotif1 = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : " . $annonce->getTitre() . ".";
                    $this->envoieNotificationA($entityManager, $contenuNotif1, $ancienUser);
                    $contenuNotif2 = "Vous avez une nouvelle transaction avec " . $this->getUser()->getUsername() . ". Allez dans votre profil pour finaliser la transaction !";
                    $this->envoieNotificationA($entityManager, $contenuNotif2, $nouvelleFile->getUser());
                } else {
                    //Personne dans la file d'attente
                    $contenuNotif1 = $this->getUser()->getUsername() . " a annulé la transaction avec vous concernant l'annonce : " . $annonce->getTitre() . ".";
                    $this->envoieNotificationA($entityManager, $contenuNotif1, $ancienUser);
                    $ancienUser->removeDemande($transaction);
                    $annonce->setTransaction(null);
                    $entityManager->flush();
                }

                $this->addFlash('notifications', 'Vous avez annuler la transaction avec le client avec succès !');
                return new Response("OK");
            }
        }
        $this->addFlash('notifications', "Erreur lors de l'annulation de la transaction avec le client !");
        return new Response("Erreur lors de l'annulation de la transaction avec le client ...");
    }

    #[Route('/ajax/activeSleepMode', name: 'activeSleepMode')]
    public function activeSleepMode(Request $request, EntityManagerInterface $entityManager): Response
    {   
        $this->getUser()->setSleepMode(true);
        $entityManager->flush();
        $this->addFlash('notifications', 'Compte en mode sommeil');
        return new Response("OK");
    }

    public function creationTransaction(Annonce $annonce, EntityManagerInterface $entityManagerInterface,User $user)
    {
        $transaction = new Transaction();
        $date = new DateTime();
        $transaction->setStatutTransaction("En cours");
        $transaction->setAnnonce($annonce);
        $transaction->setClient($user);
        $transaction->setPosteur($annonce->getPosteur());
        $transaction->setDateTransaction($date);
        $entityManagerInterface->persist($transaction);
        $entityManagerInterface->flush();
        return $transaction;
    }
    public function envoieNotificationA(EntityManagerInterface $entityManagerInterface,string $contenu,User $user)
    {
        $notif = new Notification();
        $date = new DateTime();

        $notif->setAEteLu(false);
        $notif->setContenu($contenu);
        $notif->setDateEnvoi($date);
        $notif->setUser($user);
        $entityManagerInterface->persist($notif);
        $entityManagerInterface->flush();
    }
}


