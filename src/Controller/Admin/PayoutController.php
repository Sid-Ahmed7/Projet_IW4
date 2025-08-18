<?php

namespace App\Controller\Admin;

use App\Entity\PayoutRequest;
use App\Service\PayoutService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/payouts')]
#[IsGranted('ROLE_ADMIN')]
class PayoutController extends AbstractController
{
    private PayoutService $payoutService;
    private EntityManagerInterface $entityManager;

    public function __construct(PayoutService $payoutService, EntityManagerInterface $entityManager)
    {
        $this->payoutService = $payoutService;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'admin_payouts')]
    public function index(): Response
    {
        $pendingRequests = $this->payoutService->getPendingPayoutRequests();
        
        $allRequests = $this->entityManager->getRepository(PayoutRequest::class)
            ->findBy([], ['requestedAt' => 'DESC'], 20);

        return $this->render('admin/payouts/index.html.twig', [
            'pendingRequests' => $pendingRequests,
            'allRequests' => $allRequests
        ]);
    }

    #[Route('/{id}/process', name: 'admin_payout_process', methods: ['POST'])]
    public function process(PayoutRequest $payoutRequest): Response
    {
        try {
            $this->payoutService->processPayoutRequest($payoutRequest, $this->getUser());
            $this->addFlash('success', 'La demande de retrait est maintenant en cours de traitement.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_payouts');
    }

    #[Route('/{id}/complete', name: 'admin_payout_complete', methods: ['POST'])]
    public function complete(PayoutRequest $payoutRequest, Request $request): Response
    {
        $stripeTransferId = $request->request->get('stripe_transfer_id');
        
        try {
            $this->payoutService->completePayoutRequest($payoutRequest, $stripeTransferId);
            $this->addFlash('success', 'La demande de retrait a été complétée avec succès. Le solde a été débité.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_payouts');
    }

    #[Route('/{id}/fail', name: 'admin_payout_fail', methods: ['POST'])]
    public function fail(PayoutRequest $payoutRequest, Request $request): Response
    {
        $reason = $request->request->get('failure_reason', 'Échec du traitement');
        
        try {
            $this->payoutService->failPayoutRequest($payoutRequest, $reason);
            $this->addFlash('success', 'La demande de retrait a été marquée comme échouée.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_payouts');
    }
}
