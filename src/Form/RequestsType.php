<?php

namespace App\Form;

use App\Entity\Companie;
use App\Entity\Requests;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
class RequestsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('companie', EntityType::class, [
            'class' => Companie::class,
            'label' => 'Companie',
            'choice_label' => 'name'
        ])
        ->add('email', TextType::class, [
            'label' => 'Email',
            'mapped' => false, 
            'required' => true
        ])
      
        ->add('firstname', TextType::class, [
            'label' => 'Prénom',
            'mapped' => false,
            'required' => true
        ])
      
        ->add('lastname', TextType::class, [
            'label' => 'Nom',
            'mapped' => false,
            'required' => true
        ])
        ->add('category', TextType::class, [
            'label' => 'Category',
            'required' => true
        ])
        ->add('category', TextType::class, [
            'label' => 'Category',
            'required' => true
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => true
        ])
        ->add('evenDate', DateType::class, [
            'label' => 'Date de l\'évennement',
            'widget' => 'single_text',
            'required' => true
        ])

        ->add('eventLocation', TextType::class, [
            'label' => 'Lieu de l\'évenement',
            'required' => true
        ])
        ->add('maxBudget', MoneyType::class, [
            'label' => 'Budget maximum',
            'currency' => 'EUR',
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Requests::class,
        ]);
    }
}
