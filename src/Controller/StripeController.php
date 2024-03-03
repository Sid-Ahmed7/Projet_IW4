<?php

namespace App\Controller;

use App\Entity\Devis;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invoice;
use App\Entity\Plan;
use App\Entity\UserPlan;
use App\Repository\DevisRepository;
use App\Repository\PlanRepository;
use Stripe\Plan as StripePlan;
use Stripe\Product;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

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

    // #[Route('/subscription/unsubscribe/{userPlanId}', name: 'subscription_unsubscribe', methods: ['POST'])]
    // public function unsubscribe(Request $request,  $userPlanId, UserPlan $userPlan): Response
    // {
    //     // Récupérez l'entité UserPlan correspondant à l'ID
    //     $userPlan = $this->find($userPlanId);

    //     // Annuler l'abonnement sur Stripe
    //     Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    //     $subscriptionId = $userPlan->getSubscriptionId(); // Supposons que vous stockiez l'identifiant de l'abonnement Stripe dans votre entité UserPlan
    //     $subscription = Subscription::retrieve($subscriptionId);
    //     $subscription->cancel();

    //     // Supprimez l'entité UserPlan de la base de données
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $entityManager->remove($userPlan);
    //     $entityManager->flush();

    //     // Redirigez l'utilisateur ou retournez une réponse JSON, etc.
    // }

    #[Route('/plans/create/{wplanID}', name: 'create_subscription_plan')]
    public function createSubscriptionPlan( Plan $plan, $wplanID, PlanRepository $planRepository): Response
    {
        // Configurez votre clé d'API Stripe
        Stripe::setApiKey('sk_test_51MzKwrI4CWQS7W9jqgneaMlVfXnj4r76Xc3c3TRBVsbgXE0sqP0sBpGYM3O1IJtRzvPRxiiOJUXNIF6rTCzmF4rB00Lm235aBu');
        $wplanID = (int) $wplanID;
        $planW =  $planRepository->findOneBy(['id' => $wplanID]);

        // Vérifier si le plan existe
        if (!$planW) {
            throw $this->createNotFoundException('Le plan avec l\'ID ' . $wplanID. ' n\'a pas été trouvé.');
        }

        // Créez un produit dans Stripe (si vous ne l'avez pas déjà fait)
        $product = Product::create([
            'name' => 'Abonnement fonctionnalité Wizzard',
        ]);

        // Créez un plan d'abonnement dans Stripe en associant ce plan au produit que vous avez créé
        $wplan = StripePlan::create([
            'amount' => $planW ->getPrice() *100, // Montant en centimes (10.00$ dans cet exemple)
            'currency' => 'eur',
            'interval' => 'month',
            'product' => $product->id, // ID du produit auquel ce plan est associé
            'nickname' => $planW -> getName(),
        ]);

        // L'ID du plan nouvellement créé
        $planId = $wplan->id;

        // Vous pouvez faire d'autres actions ici, comme enregistrer l'ID du plan dans votre base de données, etc.

        return $this->json(['plan_id' => $planId]);
    }
}