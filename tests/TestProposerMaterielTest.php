<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class TestProposerMaterielTest extends WebTestCase
{
    public function testProposerMateriel(): void
    {
        
        $client = static::createClient();

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

        // ENSUITE ON CREE L'ANNONCE
        $crawler = $client->request('GET', '/pret/materiel'); 

        // Remplir le formulaire
        $form = $crawler->selectButton('Valider')->form();
        $form['titre'] = 'Titre de mon annonce';
        $form['materiel'] = 'Nom du matériel'; 
        $form['duree_pret_valeur'] = '24'; 
        $form['duree_pret'] = 'heures'; 
        $form['prix'] = '50'; 
        $form['description'] = 'Description de mon annonce';

        // Envoyer le formulaire
        $crawler = $client->submit($form);

        // Vérifier que l'utilisateur est redirigé vers la page d'accueil après la création de l'annonce
        $this->assertRouteSame('app_home_page');

        // Vérifier que le message de confirmation est affiché sur la page
        $this->assertTrue($client->getCrawler()->filter('html:contains("Félicitations, votre annonce à été publiée")')->count() > 0);

        $this->assertTrue($client->getCrawler()->filter('html:contains("Titre de mon annonce")')->count() > 0);
    }

    public function testNotLogin(): void 
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/pret/materiel'); 
        // on vérifie qu'on est redirigé vers la page d'accueil
        $this->assertRouteSame('app_login');
    }
}
