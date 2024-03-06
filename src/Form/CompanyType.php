<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('address', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo (image)',
                'required' => false,
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('banner', FileType::class, [
                'label' => 'Banner (image)',
                'required' => false,
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('email', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])

            ->add('email')
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Événement' => 'EVENEMENT',
                    'Anniversaire' => 'ANNIVERSAIRE',
                    'Mariage' => 'MARIAGE', 
                    'Concert' => 'CONCERT', 
                ]
            ])
            ->add('phoneNumber')
            ->add('taxNumber')
            ->add('siretNumber');




            // Il faudrait voire comment faire le 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
