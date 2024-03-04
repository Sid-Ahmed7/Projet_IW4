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
use App\Entity\User;
use App\Entity\UserPlan;
use App\Repository\DevisRepository;
use App\Repository\PlanRepository;
use App\Repository\UserPlanRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Plan as StripePlan;
use Stripe\Price;
use Stripe\Product;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends AbstractController
{

    #[Route('/stripe/{id}/{devisID}', name: 'stripe')]
    public function stripe(Invoice $invoice, $id, $devisID, DevisRepository $devisRepository): Response
    {
        $devis = $devisRepository->find($devisID);


        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        // Créer la session de paiement Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']); // Remplacer par votre clé privée Stripe
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' =>  $invoice->getStripePaymentID(),
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
    public function createSubscriptionPlan(Plan $plan, $wplanID, PlanRepository $planRepository, EntityManagerInterface $entityManager): Response
    {
        // Configurez votre clé d'API Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $wplanID = (int) $wplanID;
        $planW =  $planRepository->findOneBy(['id' => $wplanID]);

        // Vérifier si le plan existe
        if (!$planW) {
            throw $this->createNotFoundException('Le plan avec l\'ID ' . $wplanID . ' n\'a pas été trouvé.');
        }

        // Créez un produit dans Stripe (si vous ne l'avez pas déjà fait)
        $product = Product::create([
            'name' => 'Abonnement fonctionnalité Wizzard',
        ]);

        // Créez un plan d'abonnement dans Stripe en associant ce plan au produit que vous avez créé
        $wplan = StripePlan::create([
            'amount' => $planW->getPrice() * 100, // Montant en centimes (10.00$ dans cet exemple)
            'currency' => 'eur',
            'interval' => 'month',
            'product' => $product->id, // ID du produit auquel ce plan est associé
            'nickname' => $planW->getName(),
        ]);

        // L'ID du plan nouvellement créé dans Stripe
        $stripePlanID = $wplan->id;

        // Mettre à jour le plan existant avec l'ID du plan Stripe
        $planW->setStripePlanID($stripePlanID);

        // Enregistrer les changements dans la base de données
        $entityManager->persist($planW);
        $entityManager->flush();

        // Vous pouvez retourner une réponse JSON ou rediriger l'utilisateur vers une autre page après la mise à jour du plan

        // Par exemple, retourner une réponse JSON
        // return $this->json(['success' => true, 'message' => 'Plan mis à jour avec succès', 'stripe_plan_id' => $stripePlanID]);
        return $this->redirectToRoute('app_plan_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/subscribe/{userPlanId}', name: 'subscribe')]
    public function subscribe(int $userPlanId, UserPlanRepository $userPlanRepository, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $userPlan = $userPlanRepository->find($userPlanId);

        // Vérifier si le UserPlan existe
        if (!$userPlan) {
            throw $this->createNotFoundException('Le UserPlan avec l\'ID ' . $userPlanId . ' n\'a pas été trouvé.');
        }

        // Créer une souscription dans Stripe
        $subscription = Subscription::create([
            'customer' => $userPlan->getUsr()->getStripeCustomerId(), // ID du client Stripe
            'items' => [['price' => $userPlan->getPlan()->getStripePlanId()]], // ID du plan Stripe
        ]);

        // Mettre à jour le UserPlan avec l'ID de la souscription
        $userPlan->setSubscriptionId($subscription->id);
        $entityManager->persist($userPlan);
        $entityManager->flush();

        return $this->redirectToRoute('newsubscribe', [
            'userId' => $userPlan->getUsr()->getId(),
            'planId' => $userPlan->getPlan()->getId(),
        ]);
    }

    #[Route('/unsubscribe/{userPlanId}', name: 'unsubscribe', methods: ['POST'])]
    public function unsubscribe(int $userPlanId, UserPlanRepository $userPlanRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'entité UserPlan correspondant à l'ID
        $userPlan = $userPlanRepository->find($userPlanId);

        // Vérifier si l'abonnement existe
        if (!$userPlan) {
            throw $this->createNotFoundException('L\'abonnement avec l\'ID ' . $userPlanId . ' n\'a pas été trouvé.');
        }

        // Annuler l'abonnement sur Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $subscriptionId = $userPlan->getSubscriptionId();
        $subscription = Subscription::retrieve($subscriptionId);
        $subscription->cancel();

        // Supprimer l'entité UserPlan de la base de données
        $entityManager->remove($userPlan);
        $entityManager->flush();

        // Rediriger ou retourner une réponse JSON, etc.
        return $this->redirectToRoute('app_test_index'); // Rediriger vers la page de tableau de bord par exemple
    }



    #[Route('/stripe/{planId}/{userId}', name: 'stripe')]
    public function stripePayment(Plan $plan, EntityManagerInterface $entityManager, UserRepository $userRepository, $userId, $planId, PlanRepository $planRepository): Response
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        // Configurez votre clé d'API Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $user = $this->getUser();
        $planStripe =  $planRepository->findOneBy(['id' => $planId]);
        // Initialisez et configurez Stripe ici...
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $price = Price::create([
            'unit_amount' => $planStripe->getPrice() * 100, // Le prix en centimes *100
            'currency' => 'eur', // La devise (ici l'euro)
            'product_data' => [
                'name' => $planStripe->getName(), // Le titre de l'annonce comme nom du produit
            ],
            'recurring' => [
                'interval' => 'month', // Intervalle de facturation (par exemple, chaque mois)
            ],
        ]);
        $planStripe->setStripePaymentID($price->id);
        $entityManager->persist($planStripe);
        $entityManager->flush();


        // Créer la session de paiement Stripe
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' =>  $planStripe->getStripePaymentID(),
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            // 'customer' => $stripeCustomerId, // Ajouter le customer id dans la session de paiement
            'success_url' => $YOUR_DOMAIN . '/stripe/success/'.$planId,
            'cancel_url' => $YOUR_DOMAIN . '/stripe/cancel',
            'metadata' => [
                'plan_id' => $planStripe->getStripePlanID(),
            ],
        ]);

        // Rediriger l'utilisateur vers la page de paiement Stripe
        return $this->redirect($checkout_session->url);
    }

    #[Route('/stripe/success/{planId}', name: 'stripe_success')]
    public function stripeSuccess(Request $request, UserRepository $userRepository, PlanRepository $planRepository, EntityManagerInterface $entityManager,$planId): Response
    {
        dd($planId);

        // Récupérer le plan et l'utilisateur à partir des métadonnées de la session de paiement
        $planId = $request->query->get('plan_id');
        $userId = $this->getUser();
        $plan = $planRepository->find($planId);
        $user = $userRepository->find($userId);

        dd($planId);
        // Vérifier si le plan et l'utilisateur existent
        if (!$plan || !$user) {
            throw $this->createNotFoundException('L\'utilisateur ou le plan n\'a pas été trouvé.');
        }
        // Créer un UserPlan pour l'utilisateur et le plan
        $userPlan = new UserPlan();
        $userPlan->setUsr($user);
        $userPlan->setPlan($plan);

        // Enregistrer le UserPlan dans la base de données
        $entityManager->persist($userPlan);
        $entityManager->flush();

        // Rediriger l'utilisateur vers une page de succès ou une autre page de votre choix
        return $this->redirectToRoute('newsubscribe', [
            'userId' => $userPlan->getId(),
            'planId' => $userPlan->getPlan()->getId(),
        ]);
    }

    #[Route('/stripe/cancel', name: 'stripe_cancel')]
    public function stripeCancel(): Response
    {
        // Rediriger l'utilisateur vers une page d'annulation ou une autre page de votre choix
        return $this->redirectToRoute('homepage');
    }
}
