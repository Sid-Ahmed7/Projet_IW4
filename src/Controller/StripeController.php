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
use App\Service\NotificationService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Plan as StripePlan;
use Stripe\Price;
use Stripe\Product;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class StripeController extends AbstractController
{

#[Route('/stripe/invoice/{id}/{devisID}', name: 'stripe_invoice')]
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
            'success_url' => $YOUR_DOMAIN . '/newsubsciber.html',
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
        //  clé d'API Stripe, elle est dans le .env
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $wplanID = (int) $wplanID;
        $planW =  $planRepository->findOneBy(['id' => $wplanID]);

        // Vérifie si le plan existe
        if (!$planW) {
            throw $this->createNotFoundException('Le plan avec l\'ID ' . $wplanID . ' n\'a pas été trouvé.');
        }

        //cree le produit dans stripe 
        $product = Product::create([
            'name' => 'Abonnement fonctionnalité Wizzard',
        ]);

        $wplan = StripePlan::create([
            'amount' => $planW->getPrice() * 100, 
            'currency' => 'eur',
            'interval' => 'month',
            'product' => $product->id, // ID du produit auquel ce plan est associé
            'nickname' => $planW->getName(),
        ]);
        $stripePlanID = $wplan->id;

        // Mettre à jour le plan existant avec l'ID du plan Stripe
        $planW->setStripePlanID($stripePlanID);

        $entityManager->persist($planW);
        $entityManager->flush();

    
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



#[Route('/stripe/subscription/{planId}/{userId}', name: 'stripe2')]
    public function stripePayment(Plan $plan, EntityManagerInterface $entityManager, UserRepository $userRepository, $userId, $planId, PlanRepository $planRepository): Response
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        // clé d'API Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $user = $this->getUser();
        $planStripe =  $planRepository->findOneBy(['id' => $planId]);
       
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $price = Price::create([
            'unit_amount' => $planStripe->getPrice() * 100, // Le prix est en centimes *100
            'currency' => 'eur', // La devise (ici l'euro)
            'product_data' => [
                'name' => $planStripe->getName(), // Le titre du Plan comme nom du produit
            ],
            'recurring' => [
                'interval' => 'month', //  chaque mois ducoup 
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
            'success_url' => $YOUR_DOMAIN . '/stripe/valide/'.$planId,
            'cancel_url' => $YOUR_DOMAIN . '/stripe/cancel',
            'metadata' => [
                'plan_id' => $planStripe->getStripePlanID(),
            ],
        ]);

        // Rediriger l'utilisateur vers la page de paiement Stripe
        return $this->redirect($checkout_session->url);
    }

    #[Route('/stripe/valide/{planId}', name: 'stripe_success')]
    public function stripeSuccess(Request $request, UserRepository $userRepository, PlanRepository $planRepository, EntityManagerInterface $entityManager,$planId): Response
    {
        // Récupérer le plan et l'utilisateur à partir des métadonnées de la session de paiement
        $planId = $request->query->get('plan_id');
        $userId = $this->getUser();
        $plan = $planRepository->find($planId);
        $user = $userRepository->find($userId);

        dd($planId);
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

    // #[Route('/stripe/cancel', name: 'stripe_cancel')]
    // public function stripeCancel(): Response
    // {
    //     // Rediriger l'utilisateur vers une page d'annulation ou une autre page de votre choix
    //     return $this->redirectToRoute('homepage');
    // }

    #[Route('/stripe/pay/devis/{id}', name: 'stripe_devis_payment')]
public function payDevis(
    Devis $devis, 
    EntityManagerInterface $em
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    // Vérifier l'accès au devis (même logique que DevisController)
    $hasAccess = false;
    
    // 1. L'utilisateur est le créateur du devis
    if ($devis->getHubuser() === $user) {
        $hasAccess = true;
    }
    
    // 2. L'utilisateur fait partie de l'entreprise destinataire
    if ($devis->getCompany() && $user->getCompany() === $devis->getCompany()) {
        $hasAccess = true;
    }
    
    // 3. L'utilisateur est le créateur de l'entreprise destinataire
    if ($devis->getCompany() && $devis->getCompany()->getCreatedBy() === $user->getId()) {
        $hasAccess = true;
    }
    
    if (!$hasAccess) {
        throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
    }
    
    // Vérifier que le devis est validé avant de permettre le paiement
    if ($devis->getState() !== 'Validé') {
        $this->addFlash('error', 'Ce devis doit être validé par l\'entreprise avant de pouvoir être payé.');
        return $this->redirectToRoute('app_devis_show', ['id' => $devis->getId()]);
    }

    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Paiement devis : ' . $devis->getTitle(),
                ],
                'unit_amount' => intval($devis->getPrice() * 100),
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $this->generateUrl('stripe_devis_success', [
            'id' => $devis->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateUrl('app_devis_show', [
            'id' => $devis->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL),
    ]);

    return $this->redirect($session->url, 303);
}

#[Route('/stripe/success/devis/{id}', name: 'stripe_devis_success')]
public function successDevis(
    Devis $devis,
    EntityManagerInterface $em,
    NotificationService $notificationService
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    $user = $this->getUser();

    // Marquer le devis comme payé
    $devis->setState('Payé');

    // Générer une facture
    $invoice = new Invoice();
    $invoice->setHubuser($user);
    $invoice->setCompany($devis->getCompany());
    $invoice->setDevis($devis);
    $invoice->setAmount((float)$devis->getPrice());
    $invoice->setDescription($devis->getContent());
    $invoice->setNumber(date('Ym') . '-' . uniqid());
    $invoice->setStatus('generated'); // Facture générée automatiquement après paiement du devis
    $invoice->setCreatedAt(new \DateTimeImmutable());

    $em->persist($invoice);
    $em->flush();

    // Envoi des notifications par email
    $notificationService->notifyInvoiceCreated($invoice);
    $notificationService->sendInvoicePaidNotification($invoice);

    $this->addFlash('success', 'Paiement validé et facture générée.');

    return $this->redirectToRoute('app_invoice_show', ['id' => $invoice->getId()]);
}

#[Route('/stripe/link/generate/{id}', name: 'stripe_devis_generate_link')]
public function generatePublicStripeLink(
    Devis $devis,
    EntityManagerInterface $em
): Response {
    // Génère un token unique s'il n'existe pas encore
    if (!$devis->getPaymentToken()) {
        $devis->setPaymentToken(Uuid::v4());
        $em->flush();
    }

    $this->addFlash('success', 'Lien de paiement généré.');

    return $this->redirectToRoute('app_devis_show', [
        'id' => $devis->getId(),
        'context' => 'company',
    ]);
}

#[Route('/pay/devis/public/{token}', name: 'stripe_public_payment')]
public function publicDevisPayment(
    string $token,
    DevisRepository $devisRepository,
    EntityManagerInterface $em
): Response {
    $devis = $devisRepository->findOneBy(['paymentToken' => $token]);

    if (!$devis) {
        throw $this->createNotFoundException('Lien invalide ou expiré.');
    }

    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Paiement devis : ' . $devis->getTitle(),
                ],
                'unit_amount' => intval($devis->getPrice() * 100),
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $this->generateUrl('stripe_devis_success', [
            'id' => $devis->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateUrl('app_devis_show', [
            'id' => $devis->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL),
    ]);

    return $this->redirect($session->url, 303);
}

#[Route('/stripe/send-link/{id}', name: 'stripe_send_payment_link')]
public function sendStripeLinkByEmail(Devis $devis, MailerInterface $mailer): Response
{
    if (!$devis->getCompany() || !$devis->getPaymentToken()) {
        throw $this->createNotFoundException('Devis sans entreprise ou lien de paiement introuvable.');
    }

    $link = $this->generateUrl('stripe_public_payment', [
        'token' => $devis->getPaymentToken()
    ], UrlGeneratorInterface::ABSOLUTE_URL);

    $email = (new Email())
        ->from(new Address('ibrahim60200@gmail.com', 'FactuPro'))
        ->to($devis->getCompany()->getEmail())
        ->subject('Paiement de votre devis #' . $devis->getId())
        ->html("<p>Bonjour,<br>Voici votre lien pour procéder au paiement du devis :<br><a href=\"$link\">$link</a></p>");

    $mailer->send($email);

    $this->addFlash('success', 'Lien Stripe envoyé à l\'entreprise.');
    return $this->redirectToRoute('app_devis_show', ['id' => $devis->getId()]);
}

    #[Route('/stripe/invoice-payment/{id}', name: 'stripe_invoice_payment')]
    public function invoicePayment(Invoice $invoice): Response
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        // Créer la session de paiement Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Facture ' . $invoice->getNumber(),
                        'description' => $invoice->getDescription() ?: 'Paiement de facture',
                    ],
                    'unit_amount' => $invoice->getAmount() * 100, // Montant en centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/stripe/invoice-success/' . $invoice->getId(),
            'cancel_url' => $YOUR_DOMAIN . '/account/invoices',
            'metadata' => [
                'invoice_id' => $invoice->getId(),
            ]
        ]);

        return $this->redirect($checkout_session->url, 303);
    }

    #[Route('/stripe/invoice-success/{id}', name: 'stripe_invoice_success')]
    public function invoiceSuccess(Invoice $invoice, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        // Marquer la facture comme payée
        $invoice->setStatus('paid');
        $entityManager->flush();

        // Envoyer une notification de paiement
        $notificationService->sendInvoicePaidNotification($invoice);

        $this->addFlash('success', 'Facture payée avec succès !');
        
        return $this->redirectToRoute('app_account_invoices');
    }


}
