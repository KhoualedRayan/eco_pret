<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class DesabonnerTest extends PantherTestCase
{
    public function testSomething(): void
{
    $client = static::createPantherClient();

    // Accéder à la page de connexion
    $client->request('GET', '/login');

    // Remplir et soumettre le formulaire de connexion
    $form = $client->getCrawler()->filter('#login')->form();
    $form['id'] = 'test';
    $form['password'] = '123456';
    $client->submit($form);

    // Vérifier que la connexion a réussi
    $this->assertRouteSame('app_home_page');

    // Accéder à la page de profil
    $client->request('GET', '/profile');

    // Assurez-vous que la page de profil est accessible
    $this->assertResponseIsSuccessful();

    // Attendre que le bouton de désabonnement soit présent sur la page
    $client->waitForSelector('#seDesabonnerBtn');

    // Simuler un clic sur le bouton de désabonnement
    $client->executeScript("document.querySelector('#seDesabonnerBtn').click();");

    // Attendre la notification
    $client->waitFor('.notification');

    // Surcharge de la fonction window.confirm pour toujours retourner true
    $client->executeScript("window.confirm = function(){return true;}");

    // Simuler un clic sur le bouton "Oui" dans la boîte de dialogue de confirmation
    $client->executeScript("document.querySelector('.notification .bouton-oui').click();");

    // Attendre un court délai pour que la redirection se fasse
    sleep(1);

    // Vérifier que la redirection vers la page de profil s'est effectuée avec succès
    $this->assertStringContainsString('/profile', $client->getCurrentURL());
}

}
