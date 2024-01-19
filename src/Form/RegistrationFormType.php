<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\UserPlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(), // Rend l'email obligatoire
            ],
        ])
        ->add('password', PasswordType::class, [
            'constraints' => [
                new NotBlank(), // Rend le mot de passe obligatoire
                new Regex([
                    'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                    'message' => 'Le mot de passe doit contenir au moins 8 caractères, au moins une lettre majuscule, un chiffre et un caractère spécial (@ $ ! % * ? &).',
                ]),
            ],
        ])
        ->add('username', TextType::class, [
            'constraints' => [
                new NotBlank(), // Rend l'username obligatoire
            ],
        ])
        ->add('picture', FileType::class, [
            'label' => 'Photo de profil',
            'required' => false, // Le logo n'est pas obligatoire lors de la création
        ])
        ->add('lastname', TextType::class, ['required' => false])
        ->add('firstname', TextType::class, ['required' => false]);       
            

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
