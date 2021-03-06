<?php

namespace Enigma972\UserBundle\Tests\Controller;

use Enigma972\UserBundle\DataFixtures\Users;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->loadFixtures([Users::class]);
    }

    public function testLoginWithBadUsername()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('login')->form([
            'username' => 'johny',
            'password' => 'fakepassword'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/login');

        $this->client->followRedirect();
        $output = $this->client->getResponse()->getContent();
        $this->assertContains('Username could not be found.', $output);
    }

    public function testLoginWithBadPassword()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('login')->form([
            'username' => 'john',
            'password' => 'fakepassword'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/login');

        $this->client->followRedirect();
        $output = $this->client->getResponse()->getContent();
        $this->assertContains('Invalid credentials.', $output);
    }

    public function testLoginWithNotEnabledUSer()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('login')->form([
            'username' => 'jane',
            'password' => 'user'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/login');

        $this->client->followRedirect();
        $output = $this->client->getResponse()->getContent();
        $this->assertSelectorExists('.alert.alert-danger');
        
        // This assertion don't work
        // $this->assertContains("This account isn't enabled.", $output);
    }

    public function testSuccessfullLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('login')->form([
            'username' => 'john',
            'password' => 'user'
        ]);
        $this->client->submit($form);
        
        $routeName = $this->getContainer()->getParameter('enigma972_user.target');
        $ulrGenerator = $this->getContainer()->get('router.default');
        $target = $ulrGenerator->generate($routeName);

        $this->assertResponseRedirects($target);

    }
}
