<?php
namespace Enigma972\UserBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class RegisterControllerTest extends WebTestCase
{
    // public function testRegisterPageIsUp()
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/register');

    //     $this->assertResponseIsSuccessful();
    // }

    public function testUserRegistration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        //$csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');

        $form = $crawler->selectButton('Send')->form([
            'registration_form[username]'           =>  'enigma',
            'registration_form[email]'              =>  'enigma@test.com',
            'registration_form[password][first]'    =>  'enigma',
            'registration_form[password][second]'   =>  'enigma',
            //'registration_form[_token]'       =>  $csrfToken,
        ]);
        $client->submit($form);
        
        $target = $client->getContainer()->getParameter('user.target');
        $this->assertResponseRedirects($target);
    }
}
