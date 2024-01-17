<?php

namespace App\Form;

use App\Entity\Plan;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'currency' => 'USD', // Définissez votre devise ici
            ])
            ->add('features', TextareaType::class, [
                'label' => 'Features',
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'State',
                'choices' => [
                    'Active' => 'active',
                    'Inactive' => 'inactive',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plan::class,
        ]);
    }
}
