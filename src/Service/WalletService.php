<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\PayoutRequest;
use App\Entity\Wallet;
use App\Entity\WalletTransaction;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class WalletService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Crée un wallet pour une entreprise si elle n'en a pas déjà un
     */
    public function createWalletForCompany(Company $company): Wallet
    {
        $existingWallet = $this->entityManager->getRepository(Wallet::class)
            ->findOneBy(['company' => $company]);

        if ($existingWallet) {
            return $existingWallet;
        }

        $wallet = new Wallet();
        $wallet->setCompany($company);

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        $this->logger->info('Wallet créé pour l\'entreprise', [
            'company_id' => $company->getId(),
            'company_name' => $company->getName(),
            'wallet_id' => $wallet->getId()
        ]);

        return $wallet;
    }

    /**
     * Obtient ou crée le wallet d'une entreprise
     */
    public function getOrCreateWalletForCompany(Company $company): Wallet
    {
        $wallet = $company->getWallet();
        
        if (!$wallet) {
            $wallet = $this->createWalletForCompany($company);
            $company->setWallet($wallet);
        }

        return $wallet;
    }

    /**
     * Crédite le wallet suite au paiement d'une facture
     */
    public function creditFromInvoicePayment(Invoice $invoice, string $amount, string $stripePaymentIntentId = null): WalletTransaction
    {
        $wallet = $this->getOrCreateWalletForCompany($invoice->getCompany());

        // Créer la transaction
        $transaction = new WalletTransaction();
        $transaction->setWallet($wallet);
        $transaction->setType(WalletTransaction::TYPE_CREDIT);
        $transaction->setAmount($amount);
        $transaction->setSource(WalletTransaction::SOURCE_INVOICE_PAYMENT);
        $transaction->setDescription("Paiement de la facture #{$invoice->getNumber()}");
        $transaction->setInvoice($invoice);
        
        if ($stripePaymentIntentId) {
            $transaction->setExternalReference($stripePaymentIntentId);
        }

        // Ajouter des métadonnées
        $transaction->addMetadata('invoice_id', $invoice->getId());
        $transaction->addMetadata('invoice_number', $invoice->getNumber());
        
        if ($stripePaymentIntentId) {
            $transaction->addMetadata('stripe_payment_intent_id', $stripePaymentIntentId);
        }

        // Mettre à jour le solde du wallet
        $wallet->addToBalance($amount);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $this->logger->info('Wallet crédité suite au paiement d\'une facture', [
            'wallet_id' => $wallet->getId(),
            'invoice_id' => $invoice->getId(),
            'amount' => $amount,
            'new_balance' => $wallet->getBalance()
        ]);

        return $transaction;
    }

    /**
     * Débite le wallet suite à un retrait
     */
    public function debitFromPayout(PayoutRequest $payoutRequest): WalletTransaction
    {
        $wallet = $payoutRequest->getWallet();
        $amount = $payoutRequest->getAmount();

        if (!$wallet->hasEnoughBalance($amount)) {
            throw new \InvalidArgumentException('Solde insuffisant pour effectuer ce retrait');
        }

        // Créer la transaction
        $transaction = new WalletTransaction();
        $transaction->setWallet($wallet);
        $transaction->setType(WalletTransaction::TYPE_DEBIT);
        $transaction->setAmount($amount);
        $transaction->setSource(WalletTransaction::SOURCE_PAYOUT);
        $transaction->setDescription("Retrait demandé le " . $payoutRequest->getRequestedAt()->format('d/m/Y'));
        $transaction->setPayoutRequest($payoutRequest);

        // Ajouter des métadonnées
        $transaction->addMetadata('payout_request_id', $payoutRequest->getId());
        $transaction->addMetadata('account_holder_name', $payoutRequest->getAccountHolderName());
        $transaction->addMetadata('iban', $payoutRequest->getIban());

        // Mettre à jour le solde du wallet
        $wallet->subtractFromBalance($amount);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $this->logger->info('Wallet débité suite à un retrait', [
            'wallet_id' => $wallet->getId(),
            'payout_request_id' => $payoutRequest->getId(),
            'amount' => $amount,
            'new_balance' => $wallet->getBalance()
        ]);

        return $transaction;
    }

    /**
     * Effectue un ajustement manuel du wallet (pour les corrections)
     */
    public function adjustBalance(Wallet $wallet, string $amount, string $description, array $metadata = []): WalletTransaction
    {
        $isCredit = (float)$amount > 0;
        $absoluteAmount = $isCredit ? $amount : number_format(abs((float)$amount), 2, '.', '');

        $transaction = new WalletTransaction();
        $transaction->setWallet($wallet);
        $transaction->setType($isCredit ? WalletTransaction::TYPE_CREDIT : WalletTransaction::TYPE_DEBIT);
        $transaction->setAmount($absoluteAmount);
        $transaction->setSource(WalletTransaction::SOURCE_ADJUSTMENT);
        $transaction->setDescription($description);
        $transaction->setMetadata($metadata);

        // Mettre à jour le solde
        if ($isCredit) {
            $wallet->addToBalance($absoluteAmount);
        } else {
            $wallet->subtractFromBalance($absoluteAmount);
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $this->logger->info('Ajustement manuel du wallet', [
            'wallet_id' => $wallet->getId(),
            'amount' => $amount,
            'type' => $isCredit ? 'credit' : 'debit',
            'description' => $description,
            'new_balance' => $wallet->getBalance()
        ]);

        return $transaction;
    }

    /**
     * Obtient l'historique des transactions d'un wallet
     */
    public function getTransactionHistory(Wallet $wallet, int $limit = null): array
    {
        return $this->entityManager->getRepository(WalletTransaction::class)
            ->findByWallet($wallet, $limit);
    }

    /**
     * Calcule les statistiques d'un wallet pour une période donnée
     */
    public function getWalletStats(Wallet $wallet, \DateTime $startDate, \DateTime $endDate): array
    {
        $repo = $this->entityManager->getRepository(WalletTransaction::class);

        $totalCredits = $repo->getTotalCredits($wallet, $startDate, $endDate);
        $totalDebits = $repo->getTotalDebits($wallet, $startDate, $endDate);
        $transactionStats = $repo->getTransactionStats($wallet, $startDate, $endDate);

        return [
            'total_credits' => $totalCredits,
            'total_debits' => $totalDebits,
            'net_amount' => number_format((float)$totalCredits - (float)$totalDebits, 2, '.', ''),
            'transaction_stats' => $transactionStats
        ];
    }

    /**
     * Vérifie si un wallet peut effectuer un retrait
     */
    public function canMakePayout(Wallet $wallet, string $amount): bool
    {
        return $wallet->hasEnoughBalance($amount);
    }

    /**
     * Obtient le solde total de tous les wallets
     */
    public function getTotalSystemBalance(): array
    {
        $repo = $this->entityManager->getRepository(Wallet::class);

        return [
            'total_balance' => $repo->getTotalBalanceAcrossAllWallets(),
            'total_pending_balance' => $repo->getTotalPendingBalanceAcrossAllWallets()
        ];
    }
}
