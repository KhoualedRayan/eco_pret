<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class TestEmprunterServiceTest extends TestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        // Accéder à la page de connexion
        $crawler = $client->request('GET', '/login');

        // Assurez-vous que la page de connexion est accessible
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('#login')->form();
        $form['id'] = 'marcheToujours';
        $form['password'] = '098765';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon profil")')->count() > 0);
        
        $crawler = $client->request('GET', '/profile');

        // Vérifier que la connexion a réussi
        $this->assertRouteSame('app_home_page');

        $crawler = $client->request('GET', '/annonces/1');

        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('#emprunterBtn'));

        $client->executeScript("document.querySelector('#emprunterBtn').click();");

        $this->assertTrue($client->getResponse()->isRedirect('/confirmation_emprunt'), 'La redirection vers la page de confirmation d\'emprunt a échoué.');

        $client->followRedirect();

        // Vérifier que la page de confirmation d'emprunt est affichée (adapté selon votre application)
        $this->assertRouteSame('confirmation_emprunt', 'La redirection n\'a pas abouti à la page de confirmation d\'emprunt.');
    }
}
