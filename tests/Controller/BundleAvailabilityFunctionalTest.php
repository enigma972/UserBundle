<?php
namespace Enigma972\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BundleAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);
        
        $this->assertResponseIsSuccessful($url);
    }

    public function urlProvider(): ?\Generator 
    {
        yield['/login'];
        yield['/register'];
    }
}
