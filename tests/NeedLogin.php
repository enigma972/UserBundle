<?php
namespace Enigma972\UserBundle\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * trait for simulate a user authentication session
 * 
 * @author 
 */
trait NeedLogin
{
    public function login(KernelBrowser $client, User $user, string $providerKey)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\SessionInterface */
        $session = $client->getContainer()->get('session');

        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $session->set("_security_$providerKey", serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
