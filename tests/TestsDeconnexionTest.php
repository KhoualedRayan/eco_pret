<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestsDeconnexionTest extends WebTestCase
{
    public function testSomething(): void
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test2';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');

        // Déconnectez l'utilisateur
        $client->request('GET', '/logout');

        // Vérifiez si l'utilisateur est déconnecté
        $this->assertResponseRedirects('/'); // Assurez-vous que l'utilisateur est redirigé vers la page de connexion après la déconnexion
    }
}
