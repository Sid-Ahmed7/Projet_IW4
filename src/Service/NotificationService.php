<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Devis;
use App\Entity\Invoice;
use App\Entity\Company;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Psr\Log\LoggerInterface;

class NotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private UserRepository $userRepository,
        private string $fromEmail = 'noreply@factupro.com'
    ) {}

    /**
     * Notification pour nouveau devis
     */
    public function notifyNewQuote(Devis $devis): void
    {
        try {
            $company = $devis->getCompany();
            $user = $devis->getHubuser();
            
            if (!$company || !$user) {
                $this->logger->warning('Cannot send notification: missing company or user', [
                    'devis_id' => $devis->getId()
                ]);
                return;
            }

            if (!$user->getEmail()) {
                $this->logger->warning('Cannot send notification: user has no email', [
                    'devis_id' => $devis->getId(),
                    'user_id' => $user->getId()
                ]);
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($user->getEmail())
                ->subject('Nouveau devis créé - ' . $devis->getTitle())
                ->htmlTemplate('emails/devis/new_quote.html.twig')
                ->context([
                    'devis' => $devis,
                    'company' => $company,
                    'user' => $user,
                    'subject' => 'Nouveau devis créé - ' . $devis->getTitle()
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('New quote notification sent', [
                'devis_id' => $devis->getId(),
                'user_email' => $user->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send new quote notification', [
                'devis_id' => $devis->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e; // Re-throw to see the actual error
        }
    }

    /**
     * Notification pour devis accepté
     */
    public function notifyQuoteAccepted(Devis $devis): void
    {
        try {
            $company = $devis->getCompany();
            $user = $devis->getHubuser();
            
            if (!$company || !$user) {
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($user->getEmail())
                ->subject('Devis accepté - ' . $devis->getTitle())
                ->htmlTemplate('emails/devis/quote_accepted.html.twig')
                ->context([
                    'devis' => $devis,
                    'company' => $company,
                    'user' => $user
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Quote accepted notification sent', [
                'devis_id' => $devis->getId(),
                'user_email' => $user->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send quote accepted notification', [
                'devis_id' => $devis->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notification pour devis refusé
     */
    public function notifyQuoteRejected(Devis $devis, string $reason = ''): void
    {
        try {
            $company = $devis->getCompany();
            $user = $devis->getHubuser();
            
            if (!$company || !$user) {
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($user->getEmail())
                ->subject('Devis refusé - ' . $devis->getTitle())
                ->htmlTemplate('emails/devis/quote_rejected.html.twig')
                ->context([
                    'devis' => $devis,
                    'company' => $company,
                    'user' => $user,
                    'reason' => $reason
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Quote rejected notification sent', [
                'devis_id' => $devis->getId(),
                'user_email' => $user->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send quote rejected notification', [
                'devis_id' => $devis->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notification pour facture créée
     */
    public function notifyInvoiceCreated(Invoice $invoice): void
    {
        try {
            $company = $invoice->getCompany();
            $user = $invoice->getHubuser();
            
            if (!$company || !$user) {
                $this->logger->warning('Cannot send notification: missing company or user', [
                    'invoice_id' => $invoice->getId()
                ]);
                return;
            }

            if (!$user->getEmail()) {
                $this->logger->warning('Cannot send notification: user has no email', [
                    'invoice_id' => $invoice->getId(),
                    'user_id' => $user->getId()
                ]);
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($user->getEmail())
                ->subject('Nouvelle facture - ' . $invoice->getNumber())
                ->htmlTemplate('emails/invoice/invoice_created.html.twig')
                ->context([
                    'invoice' => $invoice,
                    'company' => $company,
                    'user' => $user,
                    'subject' => 'Nouvelle facture - ' . $invoice->getNumber()
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Invoice created notification sent', [
                'invoice_id' => $invoice->getId(),
                'user_email' => $user->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send invoice created notification', [
                'invoice_id' => $invoice->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Notification pour facture payée
     */
    public function notifyInvoicePaid(Invoice $invoice): void
    {
        try {
            $company = $invoice->getCompany();
            $user = $invoice->getHubuser();
            
            if (!$company || !$user) {
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($user->getEmail())
                ->subject('Facture payée - ' . $invoice->getNumber())
                ->htmlTemplate('emails/invoice/invoice_paid.html.twig')
                ->context([
                    'invoice' => $invoice,
                    'company' => $company,
                    'user' => $user
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Invoice paid notification sent', [
                'invoice_id' => $invoice->getId(),
                'user_email' => $user->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send invoice paid notification', [
                'invoice_id' => $invoice->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notification pour facture en retard
     */
    public function notifyInvoiceOverdue(Invoice $invoice): void
    {
        try {
            $company = $invoice->getCompany();
            $user = $invoice->getHubuser();
            
            if (!$company || !$user) {
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($user->getEmail())
                ->subject('Facture en retard - ' . $invoice->getNumber())
                ->htmlTemplate('emails/invoice/invoice_overdue.html.twig')
                ->context([
                    'invoice' => $invoice,
                    'company' => $company,
                    'user' => $user
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Invoice overdue notification sent', [
                'invoice_id' => $invoice->getId(),
                'user_email' => $user->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send invoice overdue notification', [
                'invoice_id' => $invoice->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notification générique pour équipe
     */
    public function notifyTeam(Company $company, string $subject, string $message, array $context = []): void
    {
        try {
            $teamEmails = [];
            foreach ($company->getHubUsers() as $user) {
                if ($user->getEmail()) {
                    $teamEmails[] = $user->getEmail();
                }
            }

            if (empty($teamEmails)) {
                return;
            }

            $email = (new Email())
                ->from($this->fromEmail)
                ->to(...$teamEmails)
                ->subject($subject)
                ->text($message);

            $this->mailer->send($email);
            
            $this->logger->info('Team notification sent', [
                'company_id' => $company->getId(),
                'recipients_count' => count($teamEmails)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send team notification', [
                'company_id' => $company->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendInvoicePaidNotification(Invoice $invoice): void
    {
        try {
            $company = $invoice->getCompany();
            $user = $invoice->getHubuser();
            
            if (!$company || !$user) {
                return;
            }

            // Email au client
            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($user->getEmail())
                ->subject('Facture payée - ' . $invoice->getNumber())
                ->htmlTemplate('emails/invoice/invoice_paid.html.twig')
                ->context([
                    'invoice' => $invoice,
                    'company' => $company,
                    'user' => $user
                ]);

            $this->mailer->send($email);

            // Email à l'entreprise
            $companyUsers = $this->userRepository->findBy(['company' => $company, 'accountType' => 'company']);
            foreach ($companyUsers as $companyUser) {
                $companyEmail = (new TemplatedEmail())
                    ->from($this->fromEmail)
                    ->to($companyUser->getEmail())
                    ->subject('Facture payée par le client - ' . $invoice->getNumber())
                    ->htmlTemplate('emails/invoice/invoice_paid_company.html.twig')
                    ->context([
                        'invoice' => $invoice,
                        'company' => $company,
                        'user' => $user,
                        'companyUser' => $companyUser
                    ]);

                $this->mailer->send($companyEmail);
            }
            
            $this->logger->info('Invoice paid notification sent', [
                'invoice_id' => $invoice->getId(),
                'user_email' => $user->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send invoice paid notification', [
                'invoice_id' => $invoice->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notification pour nouvelle demande de retrait
     */
    public function notifyPayoutRequestCreated(\App\Entity\PayoutRequest $payoutRequest): void
    {
        try {
            $wallet = $payoutRequest->getWallet();
            $company = $wallet->getCompany();
            $requestedBy = $payoutRequest->getRequestedBy();
            
            if (!$requestedBy || !$requestedBy->getEmail()) {
                $this->logger->warning('Cannot send payout request notification: missing user email', [
                    'payout_request_id' => $payoutRequest->getId()
                ]);
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($requestedBy->getEmail())
                ->subject('Demande de retrait créée - ' . number_format(floatval($payoutRequest->getAmount()), 2, ',', ' ') . ' €')
                ->htmlTemplate('emails/wallet/payout_request_created.html.twig')
                ->context([
                    'payoutRequest' => $payoutRequest,
                    'wallet' => $wallet,
                    'company' => $company,
                    'user' => $requestedBy
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Payout request notification sent', [
                'payout_request_id' => $payoutRequest->getId(),
                'user_email' => $requestedBy->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send payout request notification', [
                'payout_request_id' => $payoutRequest->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notification pour retrait complété
     */
    public function notifyPayoutCompleted(\App\Entity\PayoutRequest $payoutRequest): void
    {
        try {
            $wallet = $payoutRequest->getWallet();
            $company = $wallet->getCompany();
            $requestedBy = $payoutRequest->getRequestedBy();
            
            if (!$requestedBy || !$requestedBy->getEmail()) {
                $this->logger->warning('Cannot send payout completed notification: missing user email', [
                    'payout_request_id' => $payoutRequest->getId()
                ]);
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($requestedBy->getEmail())
                ->subject('Retrait effectué - ' . number_format(floatval($payoutRequest->getAmount()), 2, ',', ' ') . ' €')
                ->htmlTemplate('emails/wallet/payout_completed.html.twig')
                ->context([
                    'payoutRequest' => $payoutRequest,
                    'wallet' => $wallet,
                    'company' => $company,
                    'user' => $requestedBy
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Payout completed notification sent', [
                'payout_request_id' => $payoutRequest->getId(),
                'user_email' => $requestedBy->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send payout completed notification', [
                'payout_request_id' => $payoutRequest->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notification pour retrait échoué
     */
    public function notifyPayoutFailed(\App\Entity\PayoutRequest $payoutRequest): void
    {
        try {
            $wallet = $payoutRequest->getWallet();
            $company = $wallet->getCompany();
            $requestedBy = $payoutRequest->getRequestedBy();
            
            if (!$requestedBy || !$requestedBy->getEmail()) {
                $this->logger->warning('Cannot send payout failed notification: missing user email', [
                    'payout_request_id' => $payoutRequest->getId()
                ]);
                return;
            }

            $email = (new TemplatedEmail())
                ->from($this->fromEmail)
                ->to($requestedBy->getEmail())
                ->subject('Problème avec votre retrait - ' . number_format(floatval($payoutRequest->getAmount()), 2, ',', ' ') . ' €')
                ->htmlTemplate('emails/wallet/payout_failed.html.twig')
                ->context([
                    'payoutRequest' => $payoutRequest,
                    'wallet' => $wallet,
                    'company' => $company,
                    'user' => $requestedBy
                ]);

            $this->mailer->send($email);
            
            $this->logger->info('Payout failed notification sent', [
                'payout_request_id' => $payoutRequest->getId(),
                'user_email' => $requestedBy->getEmail()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send payout failed notification', [
                'payout_request_id' => $payoutRequest->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
