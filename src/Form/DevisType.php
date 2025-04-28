<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2']
            ])
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Sélectionnez une organisation (optionnel)',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->join('c.hubusers', 'u')
                        ->where('u = :user')
                        ->setParameter('user', $options['user']);
                },
                'label' => 'Organisation',
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2']
            ])
            ->add('isNegotiable', CheckboxType::class, [
                'label' => 'Négociable',
                'required' => false,
                'attr' => ['class' => 'border border-gray-300 rounded-md p-2 mb-2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
            'user' => null,
        ]);
    }
}
