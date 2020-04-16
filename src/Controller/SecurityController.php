<?php
namespace Enigma972\UserBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="enigma_user_security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        
        return $this->render('@User/login/login.html.twig', [
            'error' =>  $error,
            'last_username' =>  $lastUsername,
        ]);
    }

    /**
     * But, this will never be executed. Enigma972\UserBundle\Security\AppAuthenticator will intercept this first
     * 
     * @Route("/login/check", name="enigma_user_security_login_check")
     */
    public function check()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the Enigma972\UserBundle\Security\AppAuthenticator');
    }


    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     * 
     * @Route("/logout", name="enigma_user_security_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
