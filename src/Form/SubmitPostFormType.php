<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;

class SubmitPostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Generate a unique token
        $token = Uuid::v4()->toRfc4122();

        $builder
            ->add('titre', TextType::class,[
                'attr' => [
                    'placeholder' => 'Titre',
                ]
            ])
            ->add('file_path', FileType::class,[
                'attr' => [
                    'placeholder' => 'Post'
                ]
            ])
            ->add('token', HiddenType::class,[
                'data' => $token,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
