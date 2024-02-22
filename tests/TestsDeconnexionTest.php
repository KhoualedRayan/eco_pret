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

        // D�connectez l'utilisateur
        $client->request('GET', '/logout');

        // V�rifiez si l'utilisateur est d�connect�
        $this->assertResponseRedirects('/'); // Assurez-vous que l'utilisateur est redirig� vers la page de connexion apr�s la d�connexion
    }
}
