<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;
class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else if ($this->getUser()->isSleepMode()) {
            return $this->redirectToRoute('app_sleep_mode');
        }

        $notifications = $entityManager->getRepository(Notification::class)->findBy(['user' => $this->getUser()]);
        $notifsDecryptees = [];
        foreach ($notifications as $notification) {
            $contenuDecrypte = $this->decrypterMessage($notification->getContenu());
            $notifsDecryptees[] = [
                'id' => $notification->getId(),
                'contenu' => $contenuDecrypte,
                'dateEnvoi' => $notification->getDateEnvoi(),
                'aEteLu' => $notification->isAEteLu(),
                // Ajoutez ici d'autres propriétés si nécessaire
            ];
        }

        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifsDecryptees,
        ]);
    }
    #[Route('/notification/marquer_comme_lu/{id}', name: 'marquer_comme_lu_route')]
    public function marquerCommeLu(int $id, EntityManagerInterface $entityManager): Response
    {
        $notification = $entityManager->getRepository(Notification::class)->find($id);
        if ($notification && $notification->getUser() === $this->getUser()) {
            $notification->setAEteLu(true);
            $entityManager->flush();

            $this->addFlash('success', 'Notification marquée comme lue.');
        } else {
            $this->addFlash('error', 'Notification non trouvée ou accès refusé.');
        }

        return $this->redirectToRoute('app_notification');
    }
    #[Route('/notification/supprimer_la_notif/{id}', name: 'supprimer_la_notif_route')]
    public function supprimerUneNotif(int $id, EntityManagerInterface $entityManager): Response
    {
        $notification = $entityManager->getRepository(Notification::class)->find($id);
        if ($notification && $notification->getUser() === $this->getUser()) {
            $entityManager->remove($notification);
            $entityManager->flush();

            $this->addFlash('success', 'Notification supprimée.');
        } else {
            $this->addFlash('error', 'Notification non trouvée ou accès refusé.');
        }

        return $this->redirectToRoute('app_notification');
    }
    function decrypterMessage($messageCrypte)
    {
        $cleSecrete = $_ENV['APP_CLE_CRYPTAGE'];
        $messageCrypte = base64_decode($messageCrypte);
        $iv = substr($messageCrypte, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $messageCrypte = substr($messageCrypte, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($messageCrypte, 'aes-256-cbc', $cleSecrete, 0, $iv);
    }
}
