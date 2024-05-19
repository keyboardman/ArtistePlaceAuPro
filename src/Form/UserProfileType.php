<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'label' => "Adresse email",
                'attr' => [
                    'placeholder' => 'Adresse email'
                ]
            ])
            ->add('firstname', TextType::class,[
                'label' => "Prénom",
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label' => "Nom",
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('file', FileType::class,[
                'label' => "Avatar",
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Avatar'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => [
                    'label' => 'Password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    ],
                ])
            ->add('telephone', TelType::class,[
                'label' => "Téléphone",
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Téléphone"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
