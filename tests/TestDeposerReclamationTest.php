<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class TestDeposerReclamationTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
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
        
        $crawler = $client->request('GET', '/faire/reclamation');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#reclamation')->form();
        $form['titre'] = 'Test Reclamation';
        $form['description'] = 'Ceci est une réclamation de test.';

        // Soumettez le formulaire de réclamation
        $client->submit($form);

    }
}
