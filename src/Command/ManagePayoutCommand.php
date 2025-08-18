<?php

namespace App\Command;

use App\Entity\PayoutRequest;
use App\Service\PayoutService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;

#[AsCommand(
    name: 'app:payout:manage',
    description: 'Manage payout requests (list, process, complete, fail)',
)]
class ManagePayoutCommand extends Command
{
    private PayoutService $payoutService;
    private EntityManagerInterface $entityManager;

    public function __construct(PayoutService $payoutService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->payoutService = $payoutService;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🏦 Gestion des Demandes de Retrait FactuPro');

        while (true) {
            $this->displayPendingRequests($io);
            
            $choice = $io->choice(
                'Que souhaitez-vous faire ?',
                [
                    '1' => 'Voir toutes les demandes en attente',
                    '2' => 'Traiter une demande (pending → processing)',
                    '3' => 'Compléter une demande (processing → completed)',
                    '4' => 'Marquer une demande comme échouée',
                    '5' => 'Voir l\'historique complet',
                    '6' => 'Quitter'
                ],
                '1'
            );

            switch ($choice) {
                case '1':
                    $this->listPendingRequests($io);
                    break;
                case '2':
                    $this->processRequest($io);
                    break;
                case '3':
                    $this->completeRequest($io);
                    break;
                case '4':
                    $this->failRequest($io);
                    break;
                case '5':
                    $this->showHistory($io);
                    break;
                case '6':
                    $io->success('Au revoir !');
                    return Command::SUCCESS;
            }

            $io->newLine();
            if (!$io->confirm('Continuer ?', true)) {
                break;
            }
        }

        return Command::SUCCESS;
    }

    private function displayPendingRequests(SymfonyStyle $io): void
    {
        $pendingRequests = $this->payoutService->getPendingPayoutRequests();
        
        if (empty($pendingRequests)) {
            $io->info('✅ Aucune demande en attente');
            return;
        }

        $io->section(sprintf('📋 %d demande(s) en attente', count($pendingRequests)));
        
        $table = new Table($io);
        $table->setHeaders(['ID', 'Entreprise', 'Montant', 'Date', 'Solde Wallet']);
        
        foreach ($pendingRequests as $request) {
            $table->addRow([
                '#' . $request->getId(),
                $request->getWallet()->getCompany()->getName(),
                $request->getAmount() . ' €',
                $request->getRequestedAt()->format('d/m/Y H:i'),
                $request->getWallet()->getBalance() . ' €'
            ]);
        }
        
        $table->render();
    }

    private function listPendingRequests(SymfonyStyle $io): void
    {
        $requests = $this->payoutService->getPendingPayoutRequests();
        
        if (empty($requests)) {
            $io->warning('Aucune demande en attente');
            return;
        }

        foreach ($requests as $request) {
            $io->section('Demande #' . $request->getId());
            $io->definitionList(
                ['Entreprise' => $request->getWallet()->getCompany()->getName()],
                ['Montant' => $request->getAmount() . ' €'],
                ['Demandé par' => $request->getRequestedBy()->getEmail()],
                ['Date' => $request->getRequestedAt()->format('d/m/Y à H:i:s')],
                ['IBAN' => $request->getIban()],
                ['Titulaire' => $request->getAccountHolderName()],
                ['Solde wallet' => $request->getWallet()->getBalance() . ' €'],
                ['Notes' => $request->getNotes() ?: 'Aucune']
            );
        }
    }

    private function processRequest(SymfonyStyle $io): void
    {
        $pendingRequests = $this->payoutService->getPendingPayoutRequests();
        
        if (empty($pendingRequests)) {
            $io->warning('Aucune demande en attente');
            return;
        }

        $choices = [];
        foreach ($pendingRequests as $request) {
            $choices[$request->getId()] = sprintf(
                'Demande #%d - %s - %s €',
                $request->getId(),
                $request->getWallet()->getCompany()->getName(),
                $request->getAmount()
            );
        }

        $requestId = $io->choice('Quelle demande traiter ?', $choices);
        $request = $this->entityManager->getRepository(PayoutRequest::class)->find($requestId);

        if (!$request) {
            $io->error('Demande introuvable');
            return;
        }

        try {
            $this->payoutService->processPayoutRequest($request, $request->getRequestedBy());
            $io->success(sprintf('✅ Demande #%d marquée comme "en cours de traitement"', $request->getId()));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }
    }

    private function completeRequest(SymfonyStyle $io): void
    {
        $processingRequests = $this->entityManager->getRepository(PayoutRequest::class)
            ->findBy(['status' => 'processing']);
        
        if (empty($processingRequests)) {
            $io->warning('Aucune demande en cours de traitement');
            return;
        }

        $choices = [];
        foreach ($processingRequests as $request) {
            $choices[$request->getId()] = sprintf(
                'Demande #%d - %s - %s €',
                $request->getId(),
                $request->getWallet()->getCompany()->getName(),
                $request->getAmount()
            );
        }

        $requestId = $io->choice('Quelle demande compléter ?', $choices);
        $request = $this->entityManager->getRepository(PayoutRequest::class)->find($requestId);

        if (!$request) {
            $io->error('Demande introuvable');
            return;
        }

        $stripeId = $io->ask('ID de transfert Stripe (optionnel)', 'MANUAL_TRANSFER_' . time());

        try {
            $this->payoutService->completePayoutRequest($request, $stripeId);
            $io->success(sprintf('🎉 Demande #%d complétée ! Solde débité: %s €', 
                $request->getId(), 
                $request->getAmount()
            ));
            $io->info(sprintf('Nouveau solde du wallet: %s €', $request->getWallet()->getBalance()));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }
    }

    private function failRequest(SymfonyStyle $io): void
    {
        $nonFailedRequests = $this->entityManager->getRepository(PayoutRequest::class)
            ->createQueryBuilder('pr')
            ->where('pr.status IN (:statuses)')
            ->setParameter('statuses', ['pending', 'processing'])
            ->getQuery()
            ->getResult();
        
        if (empty($nonFailedRequests)) {
            $io->warning('Aucune demande à marquer comme échouée');
            return;
        }

        $choices = [];
        foreach ($nonFailedRequests as $request) {
            $choices[$request->getId()] = sprintf(
                'Demande #%d - %s - %s € (%s)',
                $request->getId(),
                $request->getWallet()->getCompany()->getName(),
                $request->getAmount(),
                $request->getStatus()
            );
        }

        $requestId = $io->choice('Quelle demande marquer comme échouée ?', $choices);
        $request = $this->entityManager->getRepository(PayoutRequest::class)->find($requestId);

        if (!$request) {
            $io->error('Demande introuvable');
            return;
        }

        $reason = $io->ask('Raison de l\'échec', 'Informations bancaires invalides');

        try {
            $this->payoutService->failPayoutRequest($request, $reason);
            $io->success(sprintf('❌ Demande #%d marquée comme échouée', $request->getId()));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }
    }

    private function showHistory(SymfonyStyle $io): void
    {
        $allRequests = $this->entityManager->getRepository(PayoutRequest::class)
            ->findBy([], ['requestedAt' => 'DESC'], 10);

        if (empty($allRequests)) {
            $io->warning('Aucun historique');
            return;
        }

        $table = new Table($io);
        $table->setHeaders(['ID', 'Statut', 'Entreprise', 'Montant', 'Date', 'Complété le']);
        
        foreach ($allRequests as $request) {
            $statusEmoji = match($request->getStatus()) {
                'pending' => '🟡',
                'processing' => '🔵',
                'completed' => '🟢',
                'failed' => '🔴',
                default => '⚪'
            };
            
            $table->addRow([
                '#' . $request->getId(),
                $statusEmoji . ' ' . ucfirst($request->getStatus()),
                $request->getWallet()->getCompany()->getName(),
                $request->getAmount() . ' €',
                $request->getRequestedAt()->format('d/m/Y H:i'),
                $request->getCompletedAt() ? $request->getCompletedAt()->format('d/m/Y H:i') : '-'
            ]);
        }
        
        $table->render();
    }
}
