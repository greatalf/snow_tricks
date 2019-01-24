<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function test_security_should_display()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Connection', $crawler->filter('h1')->text());
    }
}
