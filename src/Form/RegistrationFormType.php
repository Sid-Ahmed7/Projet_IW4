<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accountType', ChoiceType::class, [
                'label' => 'Type de compte',
                'choices' => User::getAccountTypeChoices(),
                'expanded' => true,
                'multiple' => false,
                'data' => User::ACCOUNT_TYPE_PERSONAL,
                'attr' => ['class' => 'mb-4'],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mb-2'],
            ])
            ->add('isAdminUser', ChoiceType::class, [
                'label' => 'Privilèges administrateur (Entreprise uniquement)',
                'choices' => [
                    'Utilisateur standard' => false,
                    'Administrateur avec accès gestion retraits' => true,
                ],
                'expanded' => true,
                'multiple' => false,
                'data' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'mb-4 admin-privileges',
                    'style' => 'display: none;' // Caché par défaut
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-orange-600 mb-2'],
                'help' => 'Les administrateurs peuvent valider les demandes de retrait des wallets entreprise',
                'help_attr' => ['class' => 'text-xs text-orange-500']
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 8 caractères, au moins une lettre majuscule, un chiffre et un caractère spécial (@ $ ! % * ? &).',
                    ]),
                ],
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
           
            ->add('lastname', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('firstname', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
