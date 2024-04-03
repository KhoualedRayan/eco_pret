<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\PantherTestCase;


class RechercherAnnonceTest extends PantherTestCase
{
    public function testSearchBar()
    {
        $client = static::createPantherClient();

        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('form[action="/"]')->form();

        // Remplis le champ de texte de la recherche avec la valeur "test"
        $form['texte'] = 'test';

        // Soumet le formulaire
        $crawler = $client->submit($form);

        $currentUrl = $crawler->getUri();

        $expectedUrl = 'http://127.0.0.1:9080/?texte=test&type=tout&duree_min=&duree_max=&du=&au=&prix_min=&prix_max=&note=0&avecClient=tout';
        $this->assertSame($expectedUrl, $currentUrl);
    }
}
