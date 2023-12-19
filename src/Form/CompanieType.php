<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('logo')
            ->add('banner')
            ->add('email')
            ->add('phoneNumber')
            ->add('taxNumber')
            ->add('siretNumber')
            ->add('likes')
            ->add('updatedAt')
            ->add('createdAt')
            ->add('state')
            ->add('uuid')
            ->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
