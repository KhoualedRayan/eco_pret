<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Abonnement;
use PHPUnit\Framework\TestCase;

class TestChangerAboTest extends TestCase
{
    public function testChangerAbonnement(): void
    {
        // Créer un utilisateur avec un abonnement existant
        $user = new User();
        $abonnementActuel = new Abonnement();
        $abonnementActuel->setNom('Standard');
        $abonnementActuel->setPrix(20);
        $abonnementActuel->setNiveau(1);
        $user->setAbonnement($abonnementActuel);

        // Changer l'abonnement de l'utilisateur
        $nouvelAbonnement = new Abonnement();
        $nouvelAbonnement->setNom('Premium');
        $nouvelAbonnement->setPrix(25);
        $nouvelAbonnement->setNiveau(2);
        $user->setAbonnement($nouvelAbonnement);

        // Vérifier si l'abonnement a été correctement modifié
        $this->assertSame($nouvelAbonnement, $user->getAbonnement());
    }
}
