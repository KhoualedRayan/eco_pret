<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\AnnonceService;
use DateTime;
use App\Entity\User;
use App\Entity\Abonnement;
use App\Entity\DatePonctuelleService;
use App\Repository\CategorieServiceRepository;
use App\Entity\Recurrence;


class OffreServiceController extends AbstractController
{
    #[Route('/offre/service', name: 'app_offre_service')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // redirige l'utilisateur vers la page de connexion si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        // s'il n'a pas d'abonnement
        if ($this->getUser()->getAbonnement() == null) {
            $this->addFlash('notifications', 'Vous devez posséder un abonnement pour pouvoir publier des annonces !');
            return $this->redirectToRoute('app_profile');
        }

        $categories = $entityManager->getRepository(CategorieService::class)->findAll();
        return $this->render('offre_service/index.html.twig', [
            'controller_name' => 'OffreServiceController',
            'categories' => $categories,
        ]);
    }

    #[Route('/handle_form_service', name: 'handle_form_service')]
    public function handleFormService(EntityManagerInterface $entityManager,Request $request, CategorieServiceRepository $csr): Response
    {
        $titre = $request->request->get('titre');
        $date = new DateTime();
        $prix = $request->request->get('prix');
        $description = $request->request->get('description');
        $categorie = $request->request->get('service');

        $annonce = new AnnonceService();
        $annonce->setTitre($titre);
        $annonce->setDescription($description);
        $annonce->setDatePublication($date);
        $annonce->setPrix($prix);
        $annonce->setCategorie($csr->findOneByNom($categorie));
        $annonce->setPosteur($this->getUser());
        $annonce->setStatut("Disponible");

        $additionalDates = $request->request->all()['additional_date'] ?? null;
        $additionalRecu = $request->request->all()['additional_recurrence'] ?? null;
        $additionalEnds = $request->request->all()['additional_ends'] ?? null;

        $init_date = $request->request->get('date_pret');
        $init_reccu = $request->request->get('recurrence');

        #FIRST DATE
        if($init_reccu == ""){#DATE PONCTUELLE
            $first_date = new DatePonctuelleService() ;
            $first_date->setDate(new DateTime($init_date));
            $entityManager->persist($first_date);
            $annonce->addDatePonct($first_date);
        }else{#RECCURENCE
            $first_reccu = new Recurrence();
            $first_date_debut = new DateTime($init_date);
            $first_reccu->setDateDebut($first_date_debut);
            $first_date_fin = new DateTime($additionalEnds[2]);
            $first_reccu->setDateFin($first_date_fin);
            $first_reccu->setTypeRecurrence($init_reccu);
            $entityManager->persist($first_reccu);
            $annonce->addRecurrence($first_reccu);
        }

        
        dump($request->request->all());

        $index = 0;
        $index_ends = 3;
        if(is_array($additionalDates)) {
            foreach ($additionalDates as $date) {
                if($additionalRecu[$index]==""){
                    $dateponct = new DatePonctuelleService();
                    $dateponct->setDate(new DateTime($date));
                    $entityManager->persist($dateponct);
                    $annonce->addDatePonct($dateponct);
                }else{
                    $reccu = new Recurrence();
                    $date_debut = new DateTime($date);
                    $reccu->setDateDebut($date_debut);
                    $date_fin = new DateTime($additionalEnds[$index_ends]);
                    $reccu->setDateFin($date_fin);
                    $reccu->setTypeRecurrence($additionalRecu[$index]);
                    $entityManager->persist($reccu);
                    $annonce->addRecurrence($reccu);
                    $index_ends++;
                }
                $index++;
                
            }
        }
        $entityManager->persist($annonce);
        $entityManager->flush();

        $this->addFlash('notifications', 'Félicitations, votre annonce a été publiée !');
        return $this->redirectToRoute('app_home_page');
    }
}
