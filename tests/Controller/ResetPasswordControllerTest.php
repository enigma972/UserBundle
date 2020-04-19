<?php
namespace Enigma972\UserBundle\Tests\Controller;

use Enigma972\UserBundle\DataFixtures\Users;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ResetPasswordControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function setUp()
    {
        $this->loadFixtures([Users::class]);
    }

    public function testRequestWithBabEmail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset/password/request');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Send code')->form([
            'email' =>  'fake@mail.com',
        ]);
        $client->submit($form);

        $output = $client->getResponse()->getContent();
        $this->assertContains('User not found', $output);
    }

    public function testRequestWithInvalidEmail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset/password/request');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Send code')->form([
            'email' =>  'fake@mail',
        ]);
        $client->submit($form);
        
        $output = $client->getResponse()->getContent();
        $this->assertContains('Is not valid email', $output);
    }

    public function testResetCodeRequest()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset/password/request');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Send code')->form([
            'email' =>  'john@gmail.com',
        ]);
        $client->submit($form);
        
        $output = $client->getResponse()->getContent();
        $this->assertContains('Reset link is sended', $output);
    }


}
