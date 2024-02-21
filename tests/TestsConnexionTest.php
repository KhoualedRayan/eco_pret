<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestsConnexionTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
        #test pour savoir si l'utilisateur est bien sur la page de connexion
    }
    public function testLogin()
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

    }
}