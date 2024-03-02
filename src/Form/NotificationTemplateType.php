<?php

namespace App\Form;

use App\Entity\NotificationTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',null,
            [  'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add('description',null,
            [  'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add('read',null,
            [  'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add('createdAt',null,
            [  'attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NotificationTemplate::class,
        ]);
    }
}
