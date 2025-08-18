<?php

namespace App\Service;

use App\Entity\PayoutRequest;
use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PayoutService
{
    private EntityManagerInterface $entityManager;
    private WalletService $walletService;
    private NotificationService $notificationService;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        WalletService $walletService,
        NotificationService $notificationService,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->walletService = $walletService;
        $this->notificationService = $notificationService;
        $this->logger = $logger;
    }

    /**
     * Crée une nouvelle demande de retrait
     */
    public function createPayoutRequest(
        Wallet $wallet,
        string $amount,
        string $iban,
        string $bic,
        string $accountHolderName,
        User $requestedBy,
        string $notes = null
    ): PayoutRequest {
        // Vérifier que le wallet a suffisamment de fonds
        if (!$wallet->hasEnoughBalance($amount)) {
            throw new \InvalidArgumentException('Solde insuffisant pour effectuer ce retrait');
        }

        // Vérifier le montant minimum (par exemple 10€)
        if ((float)$amount < 10.00) {
            throw new \InvalidArgumentException('Le montant minimum de retrait est de 10€');
        }

        $payoutRequest = new PayoutRequest();
        $payoutRequest->setWallet($wallet);
        $payoutRequest->setAmount($amount);
        $payoutRequest->setIban($iban);
        $payoutRequest->setBic($bic);
        $payoutRequest->setAccountHolderName($accountHolderName);
        $payoutRequest->setRequestedBy($requestedBy);
        $payoutRequest->setNotes($notes);

        // Créer les détails bancaires formatés
        $bankDetails = sprintf(
            "IBAN: %s\nBIC: %s\nTitulaire: %s",
            $iban,
            $bic,
            $accountHolderName
        );
        $payoutRequest->setBankDetails($bankDetails);

        $this->entityManager->persist($payoutRequest);
        $this->entityManager->flush();

        $this->logger->info('Nouvelle demande de retrait créée', [
            'payout_request_id' => $payoutRequest->getId(),
            'wallet_id' => $wallet->getId(),
            'amount' => $amount,
            'requested_by' => $requestedBy->getEmail()
        ]);

        // Envoyer une notification (optionnel)
        try {
            $this->notificationService->notifyPayoutRequestCreated($payoutRequest);
        } catch (\Exception $e) {
            $this->logger->warning('Échec de l\'envoi de notification de demande de retrait', [
                'payout_request_id' => $payoutRequest->getId(),
                'error' => $e->getMessage()
            ]);
        }

        return $payoutRequest;
    }

    /**
     * Marque une demande de retrait comme en cours de traitement
     */
    public function processPayoutRequest(PayoutRequest $payoutRequest, User $processedBy): PayoutRequest
    {
        if (!$payoutRequest->isPending()) {
            throw new \InvalidArgumentException('Cette demande de retrait ne peut pas être traitée');
        }

        $payoutRequest->markAsProcessing($processedBy);

        $this->entityManager->flush();

        $this->logger->info('Demande de retrait marquée comme en cours de traitement', [
            'payout_request_id' => $payoutRequest->getId(),
            'processed_by' => $processedBy->getEmail()
        ]);

        return $payoutRequest;
    }

    /**
     * Complète une demande de retrait (marque comme effectuée)
     */
    public function completePayoutRequest(PayoutRequest $payoutRequest, string $stripeTransferId = null): PayoutRequest
    {
        if (!$payoutRequest->isProcessing()) {
            throw new \InvalidArgumentException('Cette demande de retrait doit être en cours de traitement');
        }

        // Débiter le wallet
        $this->walletService->debitFromPayout($payoutRequest);

        // Marquer comme complété
        $payoutRequest->markAsCompleted($stripeTransferId);

        $this->entityManager->flush();

        $this->logger->info('Demande de retrait complétée', [
            'payout_request_id' => $payoutRequest->getId(),
            'stripe_transfer_id' => $stripeTransferId,
            'amount' => $payoutRequest->getAmount()
        ]);

        // Envoyer une notification
        try {
            $this->notificationService->notifyPayoutCompleted($payoutRequest);
        } catch (\Exception $e) {
            $this->logger->warning('Échec de l\'envoi de notification de retrait complété', [
                'payout_request_id' => $payoutRequest->getId(),
                'error' => $e->getMessage()
            ]);
        }

        return $payoutRequest;
    }

    /**
     * Marque une demande de retrait comme échouée
     */
    public function failPayoutRequest(PayoutRequest $payoutRequest, string $reason): PayoutRequest
    {
        if ($payoutRequest->isCompleted()) {
            throw new \InvalidArgumentException('Cette demande de retrait est déjà complétée');
        }

        $payoutRequest->markAsFailed($reason);

        $this->entityManager->flush();

        $this->logger->warning('Demande de retrait échouée', [
            'payout_request_id' => $payoutRequest->getId(),
            'reason' => $reason
        ]);

        // Envoyer une notification
        try {
            $this->notificationService->notifyPayoutFailed($payoutRequest);
        } catch (\Exception $e) {
            $this->logger->warning('Échec de l\'envoi de notification de retrait échoué', [
                'payout_request_id' => $payoutRequest->getId(),
                'error' => $e->getMessage()
            ]);
        }

        return $payoutRequest;
    }

    /**
     * Annule une demande de retrait
     */
    public function cancelPayoutRequest(PayoutRequest $payoutRequest): PayoutRequest
    {
        if (!$payoutRequest->canBeCancelled()) {
            throw new \InvalidArgumentException('Cette demande de retrait ne peut pas être annulée');
        }

        $payoutRequest->markAsCancelled();

        $this->entityManager->flush();

        $this->logger->info('Demande de retrait annulée', [
            'payout_request_id' => $payoutRequest->getId()
        ]);

        return $payoutRequest;
    }

    /**
     * Obtient toutes les demandes de retrait en attente
     */
    public function getPendingPayoutRequests(): array
    {
        return $this->entityManager->getRepository(PayoutRequest::class)
            ->findPendingRequests();
    }

    /**
     * Obtient les demandes de retrait pour un wallet
     */
    public function getPayoutRequestsForWallet(Wallet $wallet): array
    {
        return $this->entityManager->getRepository(PayoutRequest::class)
            ->findByWallet($wallet);
    }

    /**
     * Obtient les statistiques des retraits pour une période
     */
    public function getPayoutStats(\DateTime $startDate, \DateTime $endDate): array
    {
        $repo = $this->entityManager->getRepository(PayoutRequest::class);

        $monthlyStats = $repo->getMonthlyPayoutStats($startDate, $endDate);
        
        $stats = [
            'total_pending' => $repo->getTotalAmountByStatus(PayoutRequest::STATUS_PENDING),
            'total_processing' => $repo->getTotalAmountByStatus(PayoutRequest::STATUS_PROCESSING),
            'total_completed' => $repo->getTotalAmountByStatus(PayoutRequest::STATUS_COMPLETED),
            'total_failed' => $repo->getTotalAmountByStatus(PayoutRequest::STATUS_FAILED),
            'monthly_breakdown' => []
        ];

        foreach ($monthlyStats as $stat) {
            $stats['monthly_breakdown'][$stat['status']] = [
                'count' => $stat['count'],
                'total' => $stat['total'] ?? '0.00'
            ];
        }

        return $stats;
    }

    /**
     * Valide les données bancaires
     */
    public function validateBankDetails(string $iban, string $bic): array
    {
        $errors = [];

        // Validation IBAN simple (devrait être plus robuste en production)
        $iban = str_replace(' ', '', strtoupper($iban));
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            $errors[] = 'Le format de l\'IBAN est invalide';
        }

        // Validation BIC simple
        $bic = str_replace(' ', '', strtoupper($bic));
        if (!preg_match('/^[A-Z]{6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3})?$/', $bic)) {
            $errors[] = 'Le format du BIC est invalide';
        }

        return $errors;
    }
}
