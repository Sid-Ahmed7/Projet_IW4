<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                'label' => 'Nom de l\'entreprise',
            ])
            ->add('address', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
                'label' => 'Adresse',
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
                'label' => 'Email',
            ])
            ->add('category', null, [
                'label' => 'Catégorie',
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
            ])
            ->add('phoneNumber', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
                'label' => 'Numéro de téléphone',
            ])
            ->add('siretNumber', null, [
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],
                'label' => 'Numéro SIRET',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
            'categories' => [],
        ]);
    }
}
