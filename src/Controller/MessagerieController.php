<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Message;

class MessagerieController extends AbstractController
{
    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        #$transactions = $entityManager->getRepository(Transaction::class)->findBy(['client' => $this->getUser()]);
        return $this->render('messagerie/index.html.twig', [
            'controller_name' => 'MessagerieController',
        ]);
    }
}
