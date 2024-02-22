<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestGererProfilTest extends WebTestCase
{
    public function testSomething(): void
    {
        // on se connecte
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test2';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        $this->assertTrue($client->getCrawler()->filter('html:contains("Mon profil")')->count() > 0);
        
        $crawler = $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();

        // Cliquez sur le bouton pour permettre l'édition du formulaire
        $crawler->selectButton('edit');

        // Récupérez le formulaire
        $form = $crawler->selectButton('Valider les modifications')->form();

        // Remplissez le formulaire avec les données appropriées
        $form['username'] = 'nouveau_nom_utilisateur';
        $form['email'] = 'nouvel_email@example.com';
        // Ajoutez d'autres champs si nécessaire

        // Soumettez le formulaire
        $client->submit($form);

        // Vérifiez que la redirection est effectuée après la soumission du formulaire
        $this->assertResponseRedirects('/profile');

        // Suivez la redirection
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getCrawler()->filter('html:contains("nouveau_nom_utilisateur")')->count() > 0);

    }
}
