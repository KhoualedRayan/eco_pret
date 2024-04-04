<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConsulterProfilPublicTest extends WebTestCase
{
    public function testSomething(): void
    {
        // ON SE CONNECTE
        $client = static::createClient();
        $client->followRedirects(true);
        //ON VA DANS LE PROFILE PUBLIC DE L'USER 16
        $crawler = $client->request('GET', '/profile/public/16');
        $this->assertRouteSame('app_profile_public');
        // Vérifier que la page contient le texte "Aucune notes"
        $this->assertEquals(
            0,
            $crawler->filter('h2:contains("Aucune notes")')->count(),
            'Le texte "Aucune notes" n\'a pas été trouvé dans un élément h2.'
        );
        //ON VA DANS LE PROFILE PUBLIC ANNONCE DE L'USER 16
        $crawler = $client->request('GET', '/profile/public/annonces/16');
        $this->assertRouteSame('app_profile_public_annonces');

        // Vérifier s'il y a au moins une div avec la classe "blocAnnonce"
        $this->assertGreaterThanOrEqual(
            0,
            $crawler->filter('div.blocAnnonce')->count(),
            'Il n\'y a pas de div avec la classe "blocAnnonce".'
        );
    }
}
