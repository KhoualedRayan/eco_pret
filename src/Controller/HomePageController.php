<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AnnonceService;
use App\Entity\AnnonceMateriel;
use App\Entity\Annonce;
use App\Entity\CategorieMateriel;
use App\Entity\CategorieService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Transaction;
use App\Entity\FileAttente;
use App\Entity\DatePonctuelleService;
use App\Entity\Recurrence;
use DateTime;
use App\Entity\Notification;
use App\Service\Outils;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomePageController extends AbstractController
{
    private $outils;

    public function __construct(Outils $outils)
    {
        $this->outils = $outils;
    }

    #[Route('', name: 'app_home_page')]
    public function index(EntityManagerInterface $entityManager,Request $request): Response
    {
        if($this->getUser()){
            if ($this->getUser()->isSleepMode()) {
                return $this->redirectToRoute('app_sleep_mode');
            }
        }

        $qb = null;
        $du = "";
        $au = "";
        $duree_min = "";
        $duree_max = "";
        $categories = [];
        $categs = [];
        $filtered = false;
        # obligé de passer par tout pour récupérer les catégories
        # merci symfony
        $params = $request->query->all();
        $type = $request->query->get('type', 'tout');

        if ($type == 'materiel') {
            $filtered = true;
            $qb = $entityManager->getRepository(AnnonceMateriel::class)->createQueryBuilder('a');
            $categories = isset($params['categorie']) && is_array($params['categorie']) ? $params['categorie'] : [];
            
            # CATEGORIES MATERIEL
            if ($categories != []) {
                $categs = [];
                foreach ($categories as $x) {
                    $v = intval(substr($x, 5));
                    if ($v != 0) { $categs[] = $v; }
                }
                $qb = $qb->leftJoin('a.categorie', 'c')->andWhere($qb->expr()->in('c.id', $categs));
            }

            # DUREE MINIMUM
            $duree_min = $request->query->get('duree_min', '');
            if (preg_match("/^[0-9]+ ((jour|heure)s?)?$/", $duree_min)) {
                $val = intval(preg_replace("/[^0-9]/", "", $duree_min));
                if (strpos($duree_min, "jour")) {
                    $val = 24*$val;
                }
                $qb = $qb->andWhere("a.dureeH >= :val1")->setParameter(':val1', $val);
            } else {
                $duree_min = "";
            }

            # DUREE MAXIMUM
            $duree_max = $request->query->get('duree_max', '');
            if (preg_match("/^[0-9]+ ((jour|heure)s?)?$/", $duree_max)) {
                $val = intval(preg_replace("/[^0-9]/", "", $duree_max));
                if (strpos($duree_max, "jour")) {
                    $val = 24*$val;
                }
                $qb = $qb->andWhere("a.dureeH <= :val2")->setParameter(':val2', $val);
            } else {
                $duree_max = "";
            }
        } else if ($type == 'service') {
            $filtered = true;
            $qb = $entityManager->getRepository(AnnonceService::class)->createQueryBuilder('a');

            # CATEGORIES SERVICE
            $categories = isset($params['categorie']) && is_array($params['categorie']) ? $params['categorie'] : [];
            if ($categories != []) {
                $categs = [];
                foreach ($categories as $x) {
                    $v = intval(substr($x, 5));
                    if ($v != 0) { $categs[] = $v; }
                }
                $qb = $qb->leftJoin('a.categorie', 'c')->andWhere($qb->expr()->in('c.id', $categs));
            }

            # DATE MINIMUM
            $du = $request->query->get('du', '');
            $du = \DateTime::createFromFormat('Y-m-d\TH:i', $du);
            if ($du) {
                $du = $du->format('Y-m-d H:i');
                $subQuery = $entityManager->getRepository(DatePonctuelleService::class)
                            ->createQueryBuilder('dps')->select('min(dps.date)')->andWhere('dps.dateponcts = a');
                $subQuery2 = $entityManager->getRepository(Recurrence::class)
                            ->createQueryBuilder('rec')->select('min(rec.date_debut)')->andWhere('rec.annonceServ = a');
                $qb = $qb->andWhere($qb->expr()->gte('('.$subQuery->getDQL().')',':dateDu'))
                        ->andWhere($qb->expr()->gte('('.$subQuery2->getDQL().')',':dateDu'))
                        ->setParameter('dateDu', $du);
            } else {
                $du = "";
            }

            # DATE MAXIMUM
            $au = $request->query->get('au', '');
            $au = \DateTime::createFromFormat('Y-m-d\TH:i', $au);
            if ($au) {
                $au = $au->format('Y-m-d H:i');
                $subQuery = $entityManager->getRepository(DatePonctuelleService::class)
                            ->createQueryBuilder('dps')->select('max(dps.date)')->andWhere('dps.dateponcts = a');
                $subQuery2 = $entityManager->getRepository(Recurrence::class)
                            ->createQueryBuilder('rec')->select('max(rec.date_debut)')->andWhere('rec.annonceServ = a');
                $qb = $qb->andWhere($qb->expr()->lte('('.$subQuery->getDQL().')',':dateAu'))
                        ->andWhere($qb->expr()->lte('('.$subQuery2->getDQL().')',':dateAu'))
                        ->setParameter('dateAu', $au);
            } else {
                $au = "";
            }
        } else {
            $type = "tout";
            $qb = $entityManager->getRepository(Annonce::class)->createQueryBuilder('a');
        }
        
        # texte de recherche
        $text = $request->query->get('texte', '');;
        if ($text != "") {
            $filtered = true;
            $qb = $qb->andWhere($qb->expr()->orX('a.description LIKE :text', 'a.titre LIKE :text'))
            ->setParameter('text', '%'.$text.'%');
        }

        # filter par prix min
        $prix_min = $request->query->get('prix_min', '');
        if (preg_match("/^\d+$/", $prix_min)) {
            $filtered = true;
            $qb = $qb->andWhere("a.prix >= :prixMin")->setParameter('prixMin', intval($prix_min));
        } else {
            $prix_min = "";
        }

        # filtrer par prix max
        $prix_max = $request->query->get('prix_max', '');
        # s'il n'y a que des chiffres
        if (preg_match("/^\d+$/", $prix_max)) {
            $filtered = true;
            $qb = $qb->andWhere("a.prix <= :prixMax")->setParameter('prixMax', intval($prix_max));
        } else {
            $prix_max = "";
        }

        # filtrer par les notes des posteurs
        $noteMin = $request->query->get('note', '');
        $note = intval($noteMin);
        if ($note > 0 && $note <= 5) {
            $filtered = true;
            $qb = $qb->leftJoin('a.posteur', 'p')->andWhere("p.note >= :note")
            ->setParameter('note', $note);
        } else {
            $note = 0;
        }

        # Filtre les annonces selon si elles ont déjà des clients en attente / transaction en cours
        $avecClient = $request->query->get('avecClient', "tout");
        if ($avecClient == "oui") {
            $filtered = true;
            $qb = $qb->andWhere("a.transaction IS NOT NULL");
        } else if ($avecClient == "non") {
            $filtered = true;
            $qb = $qb->andWhere("a.transaction IS NULL");
        } else {
            $avecClient = "tout";
        }

        $qb = $qb->andWhere("a.statut = 'Disponible'")->orderBy('a.date_publication', 'DESC');
        $qbC = clone $qb;
        $totalAnnonces = $qbC->select("COUNT(a.id)")->getQuery()->getResult()[0][1];

        $page = $request->query->get('page', 1);
        $page = intval($page);
        $limit = 12; // Nombre d'annonces par page

        # si page incorrecte (car modification depuis l'url)
        if ($page == 0 || $page > ceil($totalAnnonces / $limit)) { $page = 1; }
        $offset = ($page - 1) * $limit;
        
        if ($totalAnnonces == 0) {
            $annonces = null;
            $nombrePages = 1;
        } else {
            // Modifier votre requête pour récupérer les annonces avec une limite et un offset
            $annonces = $qb->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult();
            $nombrePages = ceil($totalAnnonces / $limit);
        }
        $categories = $type == "tout" ? "" : $cs = $entityManager->getRepository($type == "materiel" ? CategorieMateriel::class : CategorieService::class)->findAll();;

        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'annonces' => $annonces,
            'pageActuelle' => $page,
            'nombrePages' => $nombrePages,
            'recherche' => $text,
            'type' => $type,
            'duree_min' => $duree_min,
            'duree_max' => $duree_max,
            'categories' => $categories,
            'checked' => $categs,
            'du' => $du,
            'au' => $au,
            'prix_min' => $prix_min,
            'prix_max' => $prix_max,
            'note' => $note,
            'avecClient' => $avecClient,
            'filtered' => $filtered,
        ]);
    }

    #[Route('/ajax/emprunt', name: 'emprunt_annonce')]
    public function emprunterAnnnonce(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->request;
        $annonceId = (int) $data->get('annonceId');
        $annonceType = $data->get('annonceType');
        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        if (!$annonce) {
            return new Response("Erreur l'annonce n'existe plus");
        }
        if ($annonce->getAttentes()->isEmpty()) {
            $file = new FileAttente();
            $file->setUser($this->getUser());
            $file->setAnnonce($annonce);
            $file->setRang(0);
            $entityManager->persist($file);
            $entityManager->flush();
            $transaction = $this->creationTransaction($annonce, $entityManager);
            $annonce->addAttente($file);
            $annonce->setTransaction($transaction);
            $entityManager->flush();
            $this->envoieNotification($annonce, $entityManager);
        }else{
            $derniereFile = $annonce->getAttentes()->last();
            $file = new FileAttente();
            $file->setUser($this->getUser());
            $file->setAnnonce($annonce);
            $file->setRang($derniereFile->getRang()+1);
            $entityManager->persist($file);
            $entityManager->flush();
            $annonce->addAttente($file);
            $entityManager->flush();
        }
        $this->addFlash('notifications', "Vous pouvez désormais communiquer avec le créateur de l'annonce !");
        return new Response("OK");
    }
    public function creationTransaction(Annonce $annonce,EntityManagerInterface $entityManagerInterface){
        $transaction = new Transaction();
        $date = new DateTime();
        $transaction->setStatutTransaction("En cours");
        $transaction->setAnnonce($annonce);
        $transaction->setClient($this->getUser());
        $transaction->setDateTransaction($date);
        $entityManagerInterface->persist($transaction);
        $entityManagerInterface->flush();
        return $transaction;
    }
    public function envoieNotification(Annonce $annonce, EntityManagerInterface $entityManagerInterface){
        $notif = new Notification();
        $date = new DateTime();
        $contenu = "Vous avez une nouvelle transaction ! Allez dans votre profil pour finaliser votre transaction avec ". $this->getUser()->getUsername().".";
        $messageCrypter = $this->outils->crypterMessage($contenu);
        $notif->setAEteLu(false);
        $notif->setContenu($messageCrypter);
        $notif->setDateEnvoi($date);
        $notif->setUser($annonce->getPosteur());
        $entityManagerInterface->persist($notif);
        $entityManagerInterface->flush();
    }

    #[Route('/ajax/getCategories/{type}', name: 'get_categories')]
    public function getCategorie(EntityManagerInterface $em, $type, $checked = []): Response
    {
        $cs = $em->getRepository($type == "materiel" ? CategorieMateriel::class : CategorieService::class)->findAll();
        return $this->render('home_page/categorie.html.twig', [
            'controller_name' => 'HomePageController',
            'categories' => $cs,
            'checked' => $checked,
        ]);
    }
}
