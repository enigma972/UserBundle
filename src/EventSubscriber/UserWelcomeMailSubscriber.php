<?php

namespace Enigma972\UserBundle\EventSubscriber;

use Enigma972\UserBundle\Events;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class UserWelcomeMailSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $urlGenerator;
    private $twig;
    private $parameterBag;

    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $urlGenerator, Environment $twig, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->parameterBag = $parameterBag;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::USER_REGISTERED => 'onUserRegistered',
        ];
    }

    public function onUserRegistered($event)
    {
        if ($this->parameterBag->get('enigma972_user.not_send_welcome_mail')) {
            return;
        }
        /** @var Comment $comment */
        $user = $event->getSubject();

        //$linkToPost = $this->urlGenerator->generate('home');

        /*$subject = $this->translator->trans('notification.comment_created');
        $body = $this->translator->trans('notification.comment_created.description', [
            '%title%' => $post->getTitle(),
            '%link%' => $linkToPost,
        ]);*/

        // Symfony uses a library called SwiftMailer to send emails. That's why
        // email messages are created instantiating a Swift_Message class.
        // See https://symfony.com/doc/current/email.html#sending-emails

        $messageContent = $this->twig->render('@Enigma972User/mail/welcome_mail.html.twig');

        $message = (new \Swift_Message())
            ->setSubject("Welcome !")
            ->setTo($user->getEmail())
            ->setFrom($this->parameterBag->get('enigma972_user.no_reply_mail'))
            ->setBody($messageContent, 'text/html');

        // In config/packages/dev/swiftmailer.yaml the 'disable_delivery' option is set to 'true'.
        // That's why in the development environment you won't actually receive any email.
        // However, you can inspect the contents of those unsent emails using the debug toolbar.
        // See https://symfony.com/doc/current/email/dev_environment.html#viewing-from-the-web-debug-toolbar
        $this->mailer->send($message);
    }
}
