<?php
namespace Enigma972\UserBundle\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class RegisterControllerTest extends WebTestCase
{
    use FixturesTrait;


    public function testUserRegistration()
    {
        $client = static::createClient();
        $this->loadFixtures();
        
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Send')->form([
            'registration_form[username]'           =>  'jhon',
            'registration_form[email]'              =>  'jhon@gmail.com',
            'registration_form[password][first]'    =>  '123456',
            'registration_form[password][second]'   =>  '123456',
        ]);
        
        $client->submit($form);
        
        $this->assertResponseRedirects('/login');
    }
}
