<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType,
    PasswordType,
    TextType,
    DateType,
    FileType,
    SubmitType
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('birthdate', DateType::class, [
                'widget'   => 'single_text',
                'html5'    => true,
                'required' => false,
                'label'    => 'Date de naissance',
            ])
            ->add('picture', FileType::class, [
                'mapped'      => false,
                'required'    => false,
                'label'       => 'Photo de profil (JPEG/PNG)',
                'constraints' => [
                    new File([
                        'maxSize'          => '2M',
                        'mimeTypes'        => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Merci de télécharger un JPEG ou PNG valide.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer les modifications'
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
