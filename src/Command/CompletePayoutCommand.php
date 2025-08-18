<?php

namespace App\Command;

use App\Entity\PayoutRequest;
use App\Service\PayoutService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:payout:complete',
    description: 'Complete a payout request for testing',
)]
class CompletePayoutCommand extends Command
{
    private PayoutService $payoutService;
    private EntityManagerInterface $entityManager;

    public function __construct(PayoutService $payoutService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->payoutService = $payoutService;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Payout request ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        $payoutRequest = $this->entityManager->getRepository(PayoutRequest::class)->find($id);
        
        if (!$payoutRequest) {
            $io->error('Payout request not found');
            return Command::FAILURE;
        }

        try {
            $io->info(sprintf('Processing payout request #%d for %s€', $payoutRequest->getId(), $payoutRequest->getAmount()));
            
            // First process the request
            if ($payoutRequest->isPending()) {
                $this->payoutService->processPayoutRequest($payoutRequest, $payoutRequest->getRequestedBy());
                $io->info('Request marked as processing');
            }
            
            // Then complete it
            if ($payoutRequest->isProcessing()) {
                $this->payoutService->completePayoutRequest($payoutRequest, 'TEST_TRANSFER_' . time());
                $io->success('Payout request completed successfully!');
                
                // Show wallet balance after completion
                $wallet = $payoutRequest->getWallet();
                $io->info(sprintf('New wallet balance: %s€', $wallet->getBalance()));
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
