<?php

namespace App\Form;

use App\Entity\DevisAsset;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisAssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                null,
                [
                    'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],

                ]
            )
            ->add('size', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])

            ->add('description', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('unitPrice', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('picture1', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('picture2', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('picture3', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisAsset::class,
        ]);
    }
}
