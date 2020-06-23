<?php
namespace Enigma972\UserBundle\Tests\Controller;

use Enigma972\UserBundle\DataFixtures\Users;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ResetPasswordControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->loadFixtures([Users::class]);
    }

    public function testRequestWithBabEmail()
    {
        $crawler = $this->client->request('GET', '/reset/password/request');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Send code')->form([
            'email' =>  'fake@mail.com',
        ]);
        $this->client->submit($form);

        $output = $this->client->getResponse()->getContent();
        $this->assertContains('User not found', $output);
    }

    public function testRequestWithInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/reset/password/request');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Send code')->form([
            'email' =>  'fake@mail',
        ]);
        $this->client->submit($form);
        
        $output = $this->client->getResponse()->getContent();
        $this->assertContains('Is not valid email', $output);
    }

    public function testResetCodeRequest()
    {
        $crawler = $this->client->request('GET', '/reset/password/request');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Send code')->form([
            'email' =>  'john@gmail.com',
        ]);
        $this->client->submit($form);
        
        $output = $this->client->getResponse()->getContent();
        $this->assertContains('Reset link is sended', $output);
    }


}
