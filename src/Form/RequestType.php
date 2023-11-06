<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category')
            ->add('description')
            ->add('status')
            ->add('uuid')
            ->add('createdAt')
            ->add('evenDate')
            ->add('response')
            ->add('devisAmont')
            ->add('eventLocation')
            ->add('maxBudget')
            ->add('slug')
            ->add('users')
            ->add('companie')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}
