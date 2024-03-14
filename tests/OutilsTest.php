<?php

namespace App\Tests;

use App\Entity\User;

// PAS UN VRAI FICHIER DE TEST
// sert à factoriser les méthodes communes aux tests

class OutilsTest
{
    public function createUser($client, string $username, string $email, string $password): void
    {
        $em = $client->getContainer()->get('doctrine')->getManager();
        $pwHasher = $client->getContainer()->get('security.password_hasher');

        $user = new User();
        $user->setusername($username);
        $user->setEmail($email);
        // encode the plain password
        $user->setPassword(
            $pwHasher->hashPassword($user, $password)
        );
        $em->persist($user);
        $em->flush();
    }
    public function removeUser($client, $username, $email): void 
    {
        $em = $client->getContainer()->get('doctrine')->getManager();
        $users = $em->getRepository(User::class)
                    ->createQueryBuilder('e')->where('e.username = :v1')
                    ->orWhere('e.email = :v2')
                    ->setParameters(['v1' => $username, 'v2' => $email])
                    ->getQuery()
                    ->getResult();
        if (count($users)) {  
            $em->remove($users[0]);
            $em->flush();
        }
    }
    public function login($client, $username, $email, $password) {
        $outils->removeUser($client, $username, $email);
        $outils->createUser($client, $username, $email, $password);

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('#login')->form();
        $form['id'] = $username;
        $form['password'] = $password;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertRouteSame('app_home_page');
    }
}