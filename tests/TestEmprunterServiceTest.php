<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestEmprunterServiceTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        // Accéder à la page de connexion
        $crawler = $client->request('GET', '/login');

        // Assurez-vous que la page de connexion est accessible
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon profil")')->count() > 0);
        
        $crawler = $client->request('GET', '/profile');

        // Vérifier que la connexion a réussi
        $this->assertRouteSame('app_home_page');

        $crawler = $client->request('GET', '/annonces');

        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('#emprunter'));

        $client->executeScript("document.querySelector('#emprunter').click();");


    }
}
