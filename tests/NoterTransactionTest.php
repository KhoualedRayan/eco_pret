<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NoterTransactionTest extends WebTestCase
{
    public function testValiderNoteClient(): void
    {
        // ON SE CONNECTE
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = 'test2';
        $form['password'] = '123456';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');

        //ON VA DANS LE PROFIL
        $crawler = $client->request('GET', '/profile');
        $this->assertRouteSame('app_profile');

        //ON VA DANS LES TRANSACTIONS
        $crawler = $client->request('GET', '/profile/transactions');
        $this->assertRouteSame('app_profile_transactions');

        //ON VA DANS LA TRANSACTION TERMINER
        $crawler = $client->request('GET', '/vue/transaction/1');
        $this->assertRouteSame('app_vue_transaction');



        // ON MET LA NOTE

        // Récupérez le bouton de validation par son texte
        $buttonCrawlerNode = $crawler->selectButton('Confirmer et envoyer la note');



    }

}
