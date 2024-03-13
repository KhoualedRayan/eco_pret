<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class TestEmprunterServiceTest extends TestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $client->request('POST', '/ajax/emprunt', ['annonceId' => '1', 'annonceType' => 'type_test']);
        $response = $client->getResponse();
       
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }
}
