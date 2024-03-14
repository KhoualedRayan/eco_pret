<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use OutilsTests;

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
        $outils = new OutilsTest();
        $username = 'test2';
        $email = 'test2@test2.com';
        $client = static::createClient();

        $outils->removeUser($client, $username, $email);
        $outils->createUser($client, $username, $email, '123456');

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon Profil")')->count() > 0);

    }
    public function testLoginMail()
    {
        $outils = new OutilsTest();
        $client = static::createClient();
        $username = 'test3';
        $email = 'test2@test2.fr';

        $outils->removeUser($client, $username, $email);
        $outils->createUser($client, $username, $email, '123456');

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = $email;
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon Profil")')->count() > 0);

    }
    // l'utilisateur n'existe pas
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
