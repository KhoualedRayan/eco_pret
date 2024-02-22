<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestsInscriptionTest extends WebTestCase
{
    public function testSucces(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->filter('form[name="registration_form"]')->form();
        $form['registration_form[username]'] = 'test_user23';
        $form['registration_form[email]'] = 'test@example2.com';
        $form['registration_form[password]'] = 'password123';
        $form['registration_form[first_name]'] = 'John';
        $form['registration_form[surname]'] = 'Doe';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Vérifie si l'utilisateur est redirigé vers la page de profil après l'inscription
        $this->assertRouteSame('app_login');
    }

    public function testEchecArobase(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->filter('form[name="registration_form"]')->form();
        // pas d'arobase possible dans le username
        $form['registration_form[username]'] = 'test_user@2';
        $form['registration_form[email]'] = 'test@example.com';
        $form['registration_form[password]'] = 'password123';
        $form['registration_form[first_name]'] = 'John';
        $form['registration_form[surname]'] = 'Doe';

        $client->submit($form);

        // on vérifie qu'on reste sur la page d'inscription
        $this->assertRouteSame('app_register');
        // on vérifie que le message d'erreurs est apparu
        $this->assertTrue($client->getCrawler()->filter('html:contains("ne doit pas contenir de")')->count() > 0);
    }

    public function testEchecExisteDejaUsername(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->filter('form[name="registration_form"]')->form();
        $form['registration_form[username]'] = 'test_user42';
        $form['registration_form[email]'] = 'test@example99.com';
        $form['registration_form[password]'] = 'password123';
        $form['registration_form[first_name]'] = 'John';
        $form['registration_form[surname]'] = 'Doe';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Vérifie si l'utilisateur est redirigé vers la page de profil après l'inscription
        // donc l'utilisateur est créé
        $this->assertRouteSame('app_login');

        // on essaie de recréer le même utilisateur
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->filter('form[name="registration_form"]')->form();
        $form['registration_form[username]'] = 'test_user42';
        $form['registration_form[email]'] = 'test@example99.com';
        $form['registration_form[password]'] = 'password123';
        $form['registration_form[first_name]'] = 'John';
        $form['registration_form[surname]'] = 'Doe';

        $client->submit($form);

        // Vérifie si l'utilisateur reste sur la page d'inscription
        $this->assertRouteSame('app_register');
        // on vérifie que le message d'erreur est apparu
        $this->assertTrue($client->getCrawler()->filter('html:contains("Ce pseudo est déjà utilisé par un autre utilisateur")')->count() > 0);
    }

    public function testEchecExisteDejaEmail(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->filter('form[name="registration_form"]')->form();
        $form['registration_form[username]'] = 'test_userABC';
        $form['registration_form[email]'] = 'test@exampleQSD.com';
        $form['registration_form[password]'] = 'password123';
        $form['registration_form[first_name]'] = 'John';
        $form['registration_form[surname]'] = 'Doe';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Vérifie si l'utilisateur est redirigé vers la page de profil après l'inscription
        // donc l'utilisateur est créé
        $this->assertRouteSame('app_login');

        // on essaie de recréer le même utilisateur
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->filter('form[name="registration_form"]')->form();
        $form['registration_form[username]'] = 'test_userFEUR';
        $form['registration_form[email]'] = 'test@exampleQSD.com';
        $form['registration_form[password]'] = 'password123';
        $form['registration_form[first_name]'] = 'John';
        $form['registration_form[surname]'] = 'Doe';

        $client->submit($form);

        // Vérifie si l'utilisateur reste sur la page d'inscription
        $this->assertRouteSame('app_register');
        // on vérifie que le message d'erreur est apparu
        $this->assertTrue($client->getCrawler()->filter('html:contains("Il existe déjà un compte associé a cet e-mail")')->count() > 0);
    }
}
