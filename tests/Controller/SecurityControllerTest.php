<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @test
     * @dataProvider provideUrlH1
     */
    public function urls_and_h1s_are_successful($h1, $url)
    {
        $client = self::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //max h1 = 1 for SEO good pratic
        $this->assertLessThan(2, $crawler->filter('h1')->count());

        $this->assertContains($h1, $crawler->filter('h1')->text());
    }

    public function provideUrlH1()
    {
        return [
            ['Inscription', '/registration'],
            ['Changer mon mot de passe', '/forgotten-password'],
            ['Connection', '/connexion'],
        ];
    }

    /**
     * @test
     * @dataProvider provideSecurityAccess
     */
    public function redirect_on_denied_pages($url)
    {
        $client = self::createClient();
        $crawler = $client->request('GET', $url);

        //redirect if disconnected user
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function provideSecurityAccess()
    {
        return [
            ['/admin/categories'],
            ['/admin/profil/edit'],
            ['/admin/account/password-update'],
            ['/reset-password'],
            ['/user'],
            ['/user/update-pass'],
            ['/deconnexion'],
            ['/tricks/new'],
        ];
    }


}
