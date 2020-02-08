<?php
namespace Enigma972\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required'  =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Don\'t be blank'
                    ]),
                    new Length([
                        'min'       =>  3,
                        'minMessage'   =>  'Your password should have {{ limit }} minimum caracteres',
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'required'  =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Don\'t be blank'
                    ]),
                    new Email([
                    'message'   =>  'This {{ value }} is not a valid email address'
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choose your password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should have {{ limit }} minimum caracteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => 'The passwords are differents.',
                'required' => true,
                // 'first_options' => [
                //     // 'label' => 'Mot de passe'
                // ],
                // 'second_options' => [
                //     // 'label' => 'Repetez le mot de passe'
                // ],
            ])
            ;
    }
}
