<?php

namespace Enigma972\UserBundle\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'enigma_user_security_login';

    /** @var UrlGeneratorInterface $urlGenerator */
    private $urlGenerator;

    /** @var CsrfTokenManagerInterface $csrfTokenManager */
    private $csrfTokenManager;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /** @var ParameterBagInterface $parameterBag */
    private $parameterBag;

    /** @var User $user */
    private $user = null;


    public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $parameterBag)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->parameterBag = $parameterBag;
    }

    public function supports(Request $request)
    {
        return 'enigma_user_security_login_check' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return $this->user = $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']) && $user->isEnabled();
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->hasSession()) {
            if ($this->user instanceOf UserInterface && !$this->user->isEnabled()) {
                $message = (string) "This account isn't enabled.";
                $exception = new CustomUserMessageAuthenticationException($message);
                $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
            }else {
                $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
            }
        }

        $url = $this->getLoginUrl();

        return new RedirectResponse($url);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /** @var User $user */
        $user = $token->getUser();
        $user->setLastLogin();
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            if (
                $targetPath != $this->urlGenerator->generate('enigma_user_security_login_check', [], UrlGeneratorInterface::ABSOLUTE_URL)
                && $targetPath != $this->urlGenerator->generate('enigma_user_security_login', [], UrlGeneratorInterface::ABSOLUTE_URL)
                ) {
                return new RedirectResponse($targetPath);
            }
        }

        return new RedirectResponse($this->urlGenerator->generate($this->parameterBag->get('enigma972_user.target')));
    }

    /**
     * Override to control what happens when the user hits a secure page
     * but isn't logged in yet.
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->getLoginUrl();

        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {
        return true;
    }

    public function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
