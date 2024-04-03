<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Abonnement;
use App\Repository\AbonnementRepository;
use Symfony\Component\Form\FormError;
use DateTime;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Si vous voulez créer un admin
        // décommentez ces lignes, allez dans la page d'inscription et recommentez-les
        // $admin = new User();
        // $admin->setUsername('NOM');
        // $admin->setEmail('EMAIL@EMAIL.ML');
        // $admin->setPassword($userPasswordHasher->hashPassword(
        //     $admin,
        //     'MOT DE PASSE'
        // ));
        // $admin->setAbonnement($entityManager->getRepository(Abonnement::class)->findOneBy(['nom' => 'Admin']));
        // $entityManager->persist($admin);
        // $entityManager->flush();

        // empeche un utilisateur déjà connecté (admin ou pas) d'accéder à cette page
        $user = $this->getUser();
        if ($user) {
            if ($user->getAbonnement() && $user->getAbonnement()->getNom() == "Admin") {
                return $this->redirectToRoute("app_admin");
            } else {
                if ($user->isSleepMode()) {
                    return $this->redirectToRoute("app_sleep_mode");
                } else {
                   return $this->redirectToRoute("app_home_page"); 
                }
            }
        }

        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setAbonnement($form->get('abonnement')->getData());
            $user->setNextAbonnement($form->get('abonnement')->getData());
            $date = new DateTime();
            $user->setDateAbonnement($date);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setFirstName($form->get('first_name')->getData());
            $user->setSurname($form->get('surname')->getData());

            $user->setNbFlorains(1000);
            $user->setSleepMode(false);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash('notifications', 'Félicitations, ' . $user->getUsername() . ', votre compte à été créé !');

            return $this->redirectToRoute('app_login');
           
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

