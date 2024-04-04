<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use OutilsTests;

class TestProfileAvisControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);

        // Crée un client pour simuler une requête HTTP
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'sami_54';
        $form['password'] = 'ecopret';
        $client->submit($form);
        $crawler = $client->followRedirect();
        //$this->assertRouteSame('app_home_page');
        //$this->assertTrue($client->getCrawler()->filter('html:contains("Mon profil")')->count() > 0);
        
        $crawler = $client->request('GET', '/profile/avis');

        // Vérifie que la réponse HTTP a le bon code de statut (200 OK)
        $this->assertResponseIsSuccessful();

        

        // Vérifie que la réponse HTTP contient le bon contenu (par exemple, le titre de la page)
        $this->assertSelectorTextContains('h1', 'Avis reçus');
    }
}
