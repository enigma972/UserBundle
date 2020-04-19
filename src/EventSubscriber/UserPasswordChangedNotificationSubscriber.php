<?php
namespace Enigma972\UserBundle\EventSubscriber;

use Twig\Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class UserPasswordChangedNotificationSubscriber implements EventSubscriberInterface
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
            'user.password.changed' => 'onUserPasswordIsChanged'
        ];
    }

    public function onUserPasswordIsChanged($event)
    {
        /** @var ResetPasswordCode $resetPasswordCode */
        $resetPasswordCode = $event->getSubject();

        $resetLink = $this->urlGenerator->generate(
            'enigma_reset_password_confirm',
            [
                'token' =>  $resetPasswordCode->getToken(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        /*$subject = $this->translator->trans('notification.comment_created');
        $body = $this->translator->trans('notification.comment_created.description', [
            '%title%' => $post->getTitle(),
            '%link%' => $linkToPost,
        ]);*/

        // Symfony uses a library called SwiftMailer to send emails. That's why
        // email messages are created instantiating a Swift_Message class.
        // See https://symfony.com/doc/current/email.html#sending-emails

        $messageContent = $this->twig->render(
            '@User/mail/password_chamged_notification.html.twig',
            [
                'resetPasswordCode' =>  $resetPasswordCode,
                'resetLink'         =>  $resetLink,
            ]
        );

        $message = (new \Swift_Message())
            ->setSubject($resetPasswordCode->getUser()->getUsername() . ', votre mot de passe a été changé.')
            ->setTo($resetPasswordCode->getUser()->getEmail())
            ->setFrom($this->parameterBag->get('user.no_reply_mail'))
            ->setBody($messageContent, 'text/html');

        // In config/packages/dev/swiftmailer.yaml the 'disable_delivery' option is set to 'true'.
        // That's why in the development environment you won't actually receive any email.
        // However, you can inspect the contents of those unsent emails using the debug toolbar.
        // See https://symfony.com/doc/current/email/dev_environment.html#viewing-from-the-web-debug-toolbar
        $this->mailer->send($message);
    }

}