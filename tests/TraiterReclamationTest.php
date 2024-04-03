<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TraiterReclamationTest extends WebTestCase
{
    public function testValiderNoteClient(): void
    {
        // ON SE CONNECTE
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'rayan';
        $form['password'] = 'password123';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');

        //ON VA DANS La page admin
        $crawler = $client->request('GET', '/admin');
        $this->assertRouteSame('app_admin');
        //ON VA DANS Les reclamations
        $crawler = $client->request('GET', '/ajax/reclamation/1');
        $this->assertRouteSame('app_reclamation');




    }

}
