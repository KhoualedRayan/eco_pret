<?php

namespace App\Tests;

use App\Entity\User;

// PAS UN VRAI FICHIER DE TEST
// sert à factoriser les méthodes communes aux tests

class OutilsTest
{
    public function createUser($client, string $username, string $email, string $password): User
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
        return $user;
    }
    public function removeUser($client, $username, $email) {
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
}