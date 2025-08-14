<?php

namespace App\Command;

use App\Entity\Devis;
use App\Entity\Invoice;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-notification',
    description: 'Test notification system with real data',
)]
class TestNotificationCommand extends Command
{
    public function __construct(
        private NotificationService $notificationService,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'Type of notification (quote-new, quote-accepted, quote-rejected, invoice-created, invoice-paid)')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the devis or invoice')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');

        $io->title('Testing Notification System');

        try {
            switch ($type) {
                case 'quote-new':
                    $devis = $this->entityManager->getRepository(Devis::class)->find($id);
                    if (!$devis) {
                        $io->error("Devis with ID $id not found");
                        return Command::FAILURE;
                    }
                    $io->info("Sending new quote notification for: {$devis->getTitle()}");
                    $this->notificationService->notifyNewQuote($devis);
                    break;

                case 'quote-accepted':
                    $devis = $this->entityManager->getRepository(Devis::class)->find($id);
                    if (!$devis) {
                        $io->error("Devis with ID $id not found");
                        return Command::FAILURE;
                    }
                    $io->info("Sending quote accepted notification for: {$devis->getTitle()}");
                    $this->notificationService->notifyQuoteAccepted($devis);
                    break;

                case 'quote-rejected':
                    $devis = $this->entityManager->getRepository(Devis::class)->find($id);
                    if (!$devis) {
                        $io->error("Devis with ID $id not found");
                        return Command::FAILURE;
                    }
                    $io->info("Sending quote rejected notification for: {$devis->getTitle()}");
                    $this->notificationService->notifyQuoteRejected($devis, 'Test de refus pour démonstration');
                    break;

                case 'invoice-created':
                    $invoice = $this->entityManager->getRepository(Invoice::class)->find($id);
                    if (!$invoice) {
                        $io->error("Invoice with ID $id not found");
                        return Command::FAILURE;
                    }
                    $io->info("Sending invoice created notification for: {$invoice->getNumber()}");
                    $this->notificationService->notifyInvoiceCreated($invoice);
                    break;

                case 'invoice-paid':
                    $invoice = $this->entityManager->getRepository(Invoice::class)->find($id);
                    if (!$invoice) {
                        $io->error("Invoice with ID $id not found");
                        return Command::FAILURE;
                    }
                    $io->info("Sending invoice paid notification for: {$invoice->getNumber()}");
                    $this->notificationService->sendInvoicePaidNotification($invoice);
                    break;

                default:
                    $io->error("Invalid notification type: $type");
                    $io->note('Available types: quote-new, quote-accepted, quote-rejected, invoice-created, invoice-paid');
                    return Command::FAILURE;
            }

            $io->success('Notification sent successfully!');
            
            // Afficher l'URL appropriée selon l'environnement
            if ($_ENV['APP_ENV'] === 'dev') {
                $io->note('Check MailHog at http://localhost:8025 to see the email');
            } else {
                $io->note('Email sent via production mailer');
            }

        } catch (\Exception $e) {
            $io->error('Failed to send notification: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
