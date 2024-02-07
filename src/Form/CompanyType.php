<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('address')

            ->add('logo', FileType::class, [
                'label' => 'Logo (image)',
                'required' => false, // Le logo n'est pas obligatoire lors de la creation
            ])
            ->add('banner', FileType::class, [
                'label' => 'Banner (image)',
                'required' => false, // Le logo n'est pas obligatoire lors de la creation
            ])
        
            ->add('email')
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
