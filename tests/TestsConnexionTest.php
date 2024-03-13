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
    public function testLoginUsername()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon profil")')->count() > 0);

    }
    public function testLoginMail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test2@test.fr';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon profil")')->count() > 0);

    }
    public function testFailLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test112';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_login');

    }
}
