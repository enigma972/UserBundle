<?php

namespace Enigma972\UserBundle\Controller;

use App\Entity\User;
use Enigma972\UserBundle\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Enigma972\UserBundle\Form\RegistrationFormType;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/register")
 */
class RegisterController extends Controller
{
    private $em;
    private $eventDispatcher;

    
    public function __construct(EntityManagerInterface $entityManagerInterface, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $entityManagerInterface;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("", name="enigma_user_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!null === $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user  = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

			$this->em->persist($user);
            $this->em->flush();

			if (false === $this->getParameter('user.check_mail')) {
                // Enable user account if check mail process is desabled
                $user->setEnabled(true);
                $this->em->flush();

                // When triggering an event, you can optionally pass some information.
                // For simple applications, use the GenericEvent object provided by Symfony
                // to pass some PHP variables. For more complex applications, define your
                // own event object classes.
                // See https://symfony.com/doc/current/components/event_dispatcher/generic_event.html
                $event = new GenericEvent($user);

                // When an event is dispatched, Symfony notifies it to all the listeners
                // and subscribers registered to it. Listeners can modify the information
                // passed in the event and they can even modify the execution flow, so
                // there's no guarantee that the rest of this controller will be executed.
                // See https://symfony.com/doc/current/components/event_dispatcher.html
                $this->eventDispatcher->dispatch($event, Events::USER_REGISTERED);
                
				return $this->redirectToRoute('enigma_user_security_login');
            }
            
            return $this->redirectToRoute('enigma_user_register_check_mail');
        }
        
        return $this->render('@User/register/register.html.twig',[
            'registrationForm'  =>  $form->createView(),
        ]);
    }

    /**
     * @Route("/check/mail", name="enigma_user_register_check_mail")
     */
    public function checkMail(Request $request)
    {
        // TODO : implement this method
    }

    /**
     * @Route("/confirm", name="enigma_user_register_confirm")
     */
    public function confirm()
    {
        // TODO : implement this method
    }
}
