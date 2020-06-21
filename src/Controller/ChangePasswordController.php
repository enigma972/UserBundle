<?php
namespace Enigma972\UserBundle\Controller;

use App\Entity\User;
use Enigma972\UserBundle\Events;
use Doctrine\ORM\EntityManagerInterface;
use Enigma972\UserBundle\Entity\ResetPasswordCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ChangePasswordController extends Controller
{
    /**
     * @Route("/change/password", name="enigma_user_change_password")
     */
    public function change(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher, FlashBagInterface $flashBag)
    {
        if ($request->isMethod('POST')) {
            $oldPassword = $request->request->get('password_old');
            $newPassword = $request->request->get('password_first');
            $repeatPassword = $request->request->get('password_second');

            /** @var User $user */
            if ($user = $this->getUser()) {
                $isPasswordValid = $passwordEncoder->isPasswordValid($user, $oldPassword);

                if ($isPasswordValid) {
                    if ($newPassword === $repeatPassword) {
                        $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
                        $user->setPassword($encodedPassword);

                        // for security reason, we generate a new resetPasswordCode
                        $resetPasswordCode = new ResetPasswordCode;
                        $resetPasswordCode->setUser($user);

                        $em->persist($resetPasswordCode);
                        $em->flush();

                        $event = new GenericEvent($resetPasswordCode);
                        $eventDispatcher->dispatch($event, Events::USER_PASSWORD_CHANGED);
                    } else {
                        $flashBag->set('info', "News password isn't same.");
                    }
                } else {
                    $flashBag->set('info', 'Invalid password.');
                }
            }
        }

        return $this->render('@Enigma972User/profile/change_password.html.twig');
    }
}
