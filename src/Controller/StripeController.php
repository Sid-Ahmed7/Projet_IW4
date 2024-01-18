<?php

namespace App\Controller;

use App\Entity\Devis;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invoice;
use App\Repository\DevisRepository;

class StripeController extends AbstractController
{

    #[Route('/stripe/{id}/{devisID}', name: 'stripe')]
    public function stripe(Invoice $invoice,$id,$devisID, DevisRepository $devisRepository): Response
    {
        $devis = $devisRepository->find($devisID);
        

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
    
        // Créer la session de paiement Stripe
        Stripe::setApiKey('sk_test_51MzKwrI4CWQS7W9jqgneaMlVfXnj4r76Xc3c3TRBVsbgXE0sqP0sBpGYM3O1IJtRzvPRxiiOJUXNIF6rTCzmF4rB00Lm235aBu'); // Remplacer par votre clé privée Stripe
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' =>  $invoice->getStripePaymentID() ,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
            'metadata' => [
                'invoice_id' => $invoice->getId(),
            ],
        ]);

        // Rediriger l'utilisateur vers la page de paiement Stripe
        return $this->redirect($checkout_session->url);

       
    }    
}