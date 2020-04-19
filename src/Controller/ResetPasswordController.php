<?php
namespace Enigma972\UserBundle\Controller;

use App\Entity\User;
use Enigma972\UserBundle\Events;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Enigma972\UserBundle\Entity\ResetPasswordCode;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Enigma972\UserBundle\Repository\ResetPasswordCodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;

/**
 * @Route("/reset/password")
 */
class ResetPasswordController extends Controller
{
    protected EntityManagerInterface $em;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @Route("/request", name="enigma_reset_password_request")
     */
    public function request(Request $request, UserRepository $users, FlashBagInterface $flashBag)
    {
        if ($request->isMethod('POST')) {
            if ($email = $request->request->get('email')) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $user = $users->findOneByEmail($email);

                    if ($user instanceOf User) {
                        $resetPasswordCode = (new ResetPasswordCode())->setUser($user);
                        $timeValidity = $this->getParameter('user.reset_password_code_validity');

                        $expireTime = (new \DateTime())->add(new \DateInterval('PT'.$timeValidity.'S'));
                        $resetPasswordCode->setExpireAt($expireTime);

                        $this->em->persist($resetPasswordCode);
                        $this->em->flush();

                        $event = new GenericEvent($resetPasswordCode);
                        $this->eventDispatcher->dispatch($event, Events::USER_RESET_PASSWORD_REQUEST);

                        $resetLinkIsSended = true;
                    }else {
                        $flashBag->set('info', 'User not found');
                    }
                }else {
                    $flashBag->set('info', 'Is not valid email');
                }
            }else {
                $flashBag->set('info', 'Email field can\'t be empty');
            }
        }


        return $this->render('@User/reset_password/request.html.twig', [
                    'email'             =>  empty($email)? '': $email,
                    'resetLinkIsSended' =>  empty($resetLinkIsSended)? false: $resetLinkIsSended,
            ]);
    }

    /**
     * @Route("/confirm/{token}", name="enigma_reset_password_confirm")
     */
    public function confirm($token, ResetPasswordCodeRepository $resetPasswordCodes, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($token) {
            $rp = $resetPasswordCodes->findOneByToken($token,);

            if ($rp instanceof ResetPasswordCode and $rp->isValide()) {
                if ($request->isMethod('POST')) {
                    //TODO: Add Password Greatest Validator
                    $firstPassword = $request->request->get('password_first');
                    $secondPassword = $request->request->get('password_second');

                    if ($firstPassword === $secondPassword) {
                        $user = $rp->getUser();
                        // encode the plain password
                        $user->setPassword(
                            $passwordEncoder->encodePassword(
                                $user,
                                $firstPassword
                            )
                        );

                        //Invalidate reset password token
                        $rp->setIsUsed(true);


                        $this->em->flush();

                        $event = new GenericEvent($user);
                        $this->eventDispatcher->dispatch($event, Events::USER_PASSWORD_RESETED);


                        return $this->redirectToRoute('enigma_user_security_login');
                    }
                }
                $isValidToken = true;
            } else {
                $isValidToken = false;
            }
        }


        return $this->render('@User/reset_password/confirm.html.twig', [
            'isValidToken' => $isValidToken,
            'token'         => $token,
        ]);
    }

    /**
     * @Route("/reset", name="enigma_reset_password_reset")
     */
    public function reset()
    {
        // TODO : implement this method
    }
}
