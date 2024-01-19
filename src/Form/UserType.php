<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('lastname')
            ->add('firstname')
            ->add('username')
            ->add('uuid')
            ->add('picture', FileType::class, [
                'label' => 'Banner (image)',
                'required' => false, // Le logo n'est pas obligatoire lors de la creation
            ])
            ->add('signupDate')
            ->add('updateAt')
            ->add('deletedAt')
            ->add('slug')
            ->add('company')
            ->add('userPlan')
            ->add('requests')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
