<?php

namespace App\Form;

use App\Entity\Reque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eventdate')
            ->add('eventLocation')
            ->add('eventCountry')
            ->add('eventCity')
            ->add('eventCode')
            ->add('lastame')
            ->add('firstname')
            ->add('phoneNumber')
            ->add('maxBudget')
            ->add('mail')
            ->add('object')
            ->add('description')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reque::class,
        ]);
    }
}
