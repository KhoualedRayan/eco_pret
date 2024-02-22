<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProposerServiceTest extends WebTestCase
{
    public function testSomething(): void
    {
        // ON SE CONNECTE DEJA

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test2';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon profil")')->count() > 0);

        // on cree l'annonce

        $client = static::createClient();
        $crawler = $client->request('GET', '/offre/service');

        // Remplir le formulaire
        $form = $crawler->selectButton('Valider')->form();
        $form['titre'] = 'Titre de mon annonce';
        $form['date_pret'] = '2024-02-25T12:00'; 
        $form['recurrence'] = 'hebdomadaire'; 
        $form['additional_ends[0]'] = '2024-03-25';
        $form['service'] = 'Nom du service';
        $form['prix'] = '50'; 
        $form['description'] = 'Description de mon annonce';

        // Envoyer le formulaire
        $crawler = $client->submit($form);

        // Vérifier que l'utilisateur est redirigé vers la page d'accueil après la création de l'annonce
        $this->assertRouteSame('app_home_page');

        // Vérifier que le message de confirmation est affiché sur la page
        $this->assertTrue($client->getCrawler()->filter('html:contains("Félicitations, votre annonce à été publiée")')->count() > 0);

        // on vérifie que l'annonce est affichée sur la page d'accueil
        $this->assertTrue($client->getCrawler()->filter('html:contains("Titre de mon annonce")')->count() > 0);
    }

    public function testNotLogin(): void 
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/offre/services'); 
        // on vérifie qu'on est redirigé vers la page d'accueil
        $this->assertRouteSame('app_login');
    }
}
