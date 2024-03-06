<?php

namespace App\Form;

use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'stripePaymentID',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add(
                'paymentType',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add(
                'Vat',
                null,

                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]

            )
            ->add(
                'paymentDetails',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add(
                'state',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add(
                'updatedAt',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add(
                'createdAt',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add(
                'deletedAt',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            )
            ->add(
                'slug',
                null,
                ['attr' => ['class' => 'border border-gray-300 rounded-md p-2 w-full mb-2'],]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
