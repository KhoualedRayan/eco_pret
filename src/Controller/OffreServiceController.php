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


class OffreServiceController extends AbstractController
{
    #[Route('/offre/service', name: 'app_offre_service')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // redirige l'utilisateur vers la page de connexion si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $categories = $entityManager->getRepository(CategorieService::class)->findAll();
        return $this->render('offre_service/index.html.twig', [
            'controller_name' => 'OffreServiceController',
            'categories' => $categories,
        ]);
    }

    #[Route('/handle_form_service', name: 'handle_form_service')]
    public function handleFormService(EntityManagerInterface $entityManager,Request $request): Response
    {
        $titre = $request->request->get('titre');
        $date = new DateTime();
        $prix = $request->request->get('prix');
        $description = $request->request->get('description');

        $annonce = new AnnonceService();
        $annonce->setTitre($titre);
        $annonce->setDescription($description);
        $annonce->setDatePublication($date);
        $annonce->setPrix($prix);
        $annonce->setPosteur($this->getUser());
        $annonce->setStatut("Disponible");

        

        $additionalDates = $request->request->all()['additional_date'] ?? null;
        echo "TESSSSSSSSSSSSSSSSSSSSSSSSSSST";
    echo "TE";
    dump($request->request->all());

    if(is_array($additionalDates)) {
        foreach ($additionalDates as $date) {
            echo("toto" . $date);
            $dateponct = new DatePonctuelleService();
            $dateponct->setDate(new DateTime($date));
            $entityManager->persist($dateponct);
            $annonce->addDatePonct($dateponct);
        }
    } else {
    }
        $entityManager->persist($annonce);
        $entityManager->flush();

        $this->addFlash('notificationAnnonces', 'Félicitations, votre annonce a été publiée !');
        return null;
    }
}
