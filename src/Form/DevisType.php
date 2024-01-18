<?php

namespace App\Form;

use App\Entity\Devis;
use IntlChar;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Content',
                // Vous pouvez ajouter d'autres options de validation ici
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'State',
                'choices' => [
                    'Pending' => 'pending',
                    'Accepted' => 'accepted',
                    'Rejected' => 'rejected',
                ],
                'expanded' => true, // Afficher sous forme de boutons radio
            ])
            ->add('isNegotiable', CheckboxType::class, [
                'label' => 'Is Negotiable',
                'required' => false, // Le champ n'est pas requis
            ])
            
            ->add('price', TextType::class, [
                'label' => 'Prix',
                'required' => true, // Le champ n'est pas requis
            ])
            ->add('title', TextType::class, [
                'label' => 'title',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
