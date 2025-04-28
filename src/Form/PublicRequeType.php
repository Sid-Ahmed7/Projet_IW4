<?php

namespace App\Form;

use App\Entity\Reque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicRequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom'
                ]
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre email'
                ]
            ])
            ->add('eventCountry', TextType::class, [
                'label' => 'Pays de l\'événement',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Pays où se déroulera l\'événement'
                ]
            ])
            ->add('eventdate', DateType::class, [
                'label' => 'Date de l\'événement',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('maxBudget', IntegerType::class, [
                'label' => 'Budget maximum',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre budget maximum en euros'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de votre événement',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Décrivez votre événement en détail...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reque::class,
        ]);
    }
} 