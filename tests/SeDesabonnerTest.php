<?php

namespace App\Tests;
use App\Entity\User;
use App\Entity\Abonnement;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeDesabonnerTest extends WebTestCase
{
    public function testSeDesabonner(): void
    {
        $client = static::createClient();
    
        // Accéder à la page de connexion
        $crawler = $client->request('GET', '/login');
    
        // Assurez-vous que la page de connexion est accessible
        $this->assertResponseIsSuccessful();
    
        // Remplir et soumettre le formulaire de connexion
        $form = $crawler->selectButton('Connexion')->form();
        $form['id'] = 'test2';
        $form['password'] = '123456';
        $client->submit($form);
    
        // Vérifier que la connexion a réussi
        $this->assertRouteSame('app_home_page');
    
        // Accéder à la page de profil
        $crawler = $client->request('GET', '/profil');
    
        // Assurez-vous que la page de profil est accessible
        $this->assertResponseIsSuccessful();
    
        // Vérifier que le bouton de désabonnement est présent sur la page
        $this->assertCount(1, $crawler->filter('#seDesabonnerBtn'));
    
        // Simuler un clic sur le bouton de désabonnement
        $client->executeScript("document.querySelector('#seDesabonnerBtn').click();");
    
        // Vérifier que la redirection vers la page d'accueil s'est effectuée avec succès
        $this->assertTrue($client->getResponse()->isRedirect('/'), 'La redirection vers la page d\'accueil a échoué.');
    
        // Suivre la redirection
        $client->followRedirect();
    
        // Vérifier que la page d'accueil est affichée
        $this->assertRouteSame('app_home_page', 'La redirection n\'a pas abouti à la page d\'accueil.');
    }
    
}
