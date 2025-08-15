<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvoiceSignatureService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationService $notificationService,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    /**
     * Préparer une facture pour signature et envoyer l'email
     */
    public function sendForSignature(Invoice $invoice, string $signerEmail, string $signerName = null): string
    {
        // Générer un token unique pour la signature
        $signatureToken = bin2hex(random_bytes(32));
        
        // Mettre à jour la facture
        $invoice->setSignatureToken($signatureToken);
        $invoice->setSignatureRequestedAt(new \DateTimeImmutable());
        $invoice->setStatus('awaiting_signature');
        $invoice->setSignerEmail($signerEmail);
        
        if ($signerName) {
            $invoice->setSignerName($signerName);
        }
        
        $this->entityManager->flush();
        
        // Générer l'URL de signature
        $signatureUrl = $this->urlGenerator->generate(
            'app_invoice_sign', 
            ['token' => $signatureToken], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        // Envoyer l'email de demande de signature
        $this->notificationService->notifySignatureRequest($invoice, $signatureUrl);
        
        return $signatureUrl;
    }

    /**
     * Valider et enregistrer la signature
     */
    public function signInvoice(Invoice $invoice, string $signerName, string $signerEmail): void
    {
        if ($invoice->getStatus() !== 'awaiting_signature') {
            throw new \Exception('Cette facture n\'est pas en attente de signature.');
        }

        if ($invoice->getSignerEmail() !== $signerEmail) {
            throw new \Exception('L\'email du signataire ne correspond pas.');
        }

        // Enregistrer la signature
        $invoice->setSignedAt(new \DateTimeImmutable());
        $invoice->setSignerName($signerName);
        $invoice->setStatus('signed');
        
        // Optionnel: invalider le token pour éviter les re-signatures
        $invoice->setSignatureToken(null);
        
        $this->entityManager->flush();
        
        // Notifier l'émetteur de la facture
        $this->notificationService->notifyInvoiceSigned($invoice);
    }

    /**
     * Vérifier si un token de signature est valide
     */
    public function isValidSignatureToken(string $token): ?Invoice
    {
        return $this->entityManager->getRepository(Invoice::class)
            ->findOneBy(['signatureToken' => $token, 'status' => 'awaiting_signature']);
    }

    /**
     * Vérifier si une facture peut être envoyée pour signature
     */
    public function canBeSentForSignature(Invoice $invoice): bool
    {
        return in_array($invoice->getStatus(), ['pending', 'draft']);
    }
}
