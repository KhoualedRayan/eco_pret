<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModifierAnnonceTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'nouveau_nom_utilisateur';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
        
         // Accéder à la page de profil
        $crawler = $client->request('GET', '/profile');

        // Assurez-vous que la page de profil est accessible
        $this->assertResponseIsSuccessful();
    }
}
