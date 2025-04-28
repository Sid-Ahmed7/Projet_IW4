<?php

namespace App\Form;

use App\Entity\Reque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('eventdate', DateType::class, [
        'widget' => 'single_text',
        'attr' => [
            'class' => 'text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 shadow-md focus:border-gray-600 focus:outline-none',
            'placeholder' => 'Date de commande',
        ],
        'label' => 'Date de commande',
        'label_attr' => ['class' => 'px-1 text-sm text-gray-600'],
    ])
    ->add('eventCountry', TextType::class, [
        'attr' => [
            'class' => 'text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 shadow-md focus:border-gray-600 focus:outline-none',
            'placeholder' => 'Pays',
        ],
        'label' => 'Pays',
        'label_attr' => ['class' => 'px-1 text-sm text-gray-600'],
    ])
    ->add('firstname', TextType::class, [
        'attr' => [
            'class' => 'text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 shadow-md focus:border-gray-600 focus:outline-none',
            'placeholder' => 'Prénom',
        ],
        'label' => 'Prénom',
        'label_attr' => ['class' => 'px-1 text-sm text-gray-600'],
    ])
   
    ->add('mail', EmailType::class, [
        'attr' => [
            'class' => 'text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none',
            'placeholder' => 'Votre email',
        ],
        'label' => 'Email',
        'label_attr' => ['class' => 'px-1 text-sm text-gray-600'],
    ])
    ->add('description', TextareaType::class, [
        'attr' => [
            'class' => 'text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 shadow-md focus:border-gray-600 focus:outline-none h-20',
            'placeholder' => 'Description de l\'événement',
        ],
        'label' => 'Description',
        'label_attr' => ['class' => 'px-1 text-sm text-gray-600'],
    
    ])
    ->add('pic1', FileType::class, [
        'label' => 'Logo 1',
        'mapped' => false, // si les champs ne sont pas directement mappés à des propriétés de l'entité
        'required' => false, // rendre le téléchargement facultatif
        'attr' => [
            'class' => 'hidden', // utilisez la classe hidden pour l'input
        ],
        'label_attr' => [
            'class' => 'w-32 flex flex-col items-center px-4 py-2 bg-white text-blue rounded-lg shadow-lg cursor-pointer hover:bg-black hover:text-white',
            'style' => 'background-image: none;', // Ajoutez ceci si vous souhaitez supprimer le fond par défaut des boutons de formulaire
        ],
        // Ajoutez des data attributes si nécessaire pour intégrer votre SVG ou autres éléments HTML spécifiques
    ])
    ->add('pic2', FileType::class, [
        'label' => 'Logo 2',
        'mapped' => false,
        'required' => false,
        'attr' => [
            'class' => 'hidden',
        ],
        'label_attr' => [
            'class' => 'w-32 flex flex-col items-center px-4 py-2 bg-white text-blue rounded-lg shadow-lg cursor-pointer hover:bg-black hover:text-white',
            'style' => 'background-image: none;',
        ],
    ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reque::class,
        ]);
    }
}
