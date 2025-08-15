<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Service\InvoiceSignatureService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceSignatureController extends AbstractController
{
    private $entityManager;
    private $signatureService;
    private $notificationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        InvoiceSignatureService $signatureService,
        NotificationService $notificationService
    ) {
        $this->entityManager = $entityManager;
        $this->signatureService = $signatureService;
        $this->notificationService = $notificationService;
    }

    #[Route('/invoice/sign/{token}', name: 'invoice_sign_public', methods: ['GET', 'POST'])]
    public function signInvoice(string $token, Request $request): Response
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)
            ->findOneBy(['signatureToken' => $token]);

        // Token invalide
        if (!$invoice) {
            return $this->render('public/invoice/signature_error.html.twig', [
                'error_type' => 'invalid'
            ]);
        }

        // Vérifier si la facture est déjà signée
        if ($invoice->isSigned()) {
            return $this->render('public/invoice/already_signed.html.twig', [
                'invoice' => $invoice
            ]);
        }

        // Vérifier si le token a expiré (7 jours)
        if ($invoice->getSignatureRequestedAt() < new \DateTime('-7 days')) {
            return $this->render('public/invoice/signature_error.html.twig', [
                'error_type' => 'expired'
            ]);
        }

        if ($request->isMethod('POST')) {
            $signerName = trim($request->request->get('signer_name', ''));
            $signerEmail = trim($request->request->get('signer_email', ''));

            // Validation des données
            $errors = [];
            
            if (empty($signerName)) {
                $errors[] = 'Le nom du signataire est requis';
            }
            
            if (empty($signerEmail)) {
                $errors[] = 'L\'adresse email est requise';
            } elseif (!filter_var($signerEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Veuillez saisir une adresse email valide';
            }

            if (!empty($errors)) {
                return $this->render('public/invoice/sign.html.twig', [
                    'invoice' => $invoice,
                    'errors' => $errors,
                    'signer_name' => $signerName,
                    'signer_email' => $signerEmail
                ]);
            }

            try {
                // Signer la facture
                $this->signatureService->signInvoice($invoice, $signerName, $signerEmail);

                // Envoyer les notifications
                $this->notificationService->notifyInvoiceSigned($invoice);

                return $this->render('public/invoice/signature_success.html.twig', [
                    'invoice' => $invoice
                ]);
            } catch (\Exception $e) {
                return $this->render('public/invoice/sign.html.twig', [
                    'invoice' => $invoice,
                    'errors' => ['Une erreur est survenue lors de la signature. Veuillez réessayer.'],
                    'signer_name' => $signerName,
                    'signer_email' => $signerEmail
                ]);
            }
        }

        return $this->render('public/invoice/sign.html.twig', [
            'invoice' => $invoice
        ]);
    }

    #[Route('/invoice/signature-status/{token}', name: 'invoice_signature_status', methods: ['GET'])]
    public function signatureStatus(string $token): Response
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)
            ->findOneBy(['signatureToken' => $token]);

        if (!$invoice) {
            return $this->json(['error' => 'Facture non trouvée'], 404);
        }

        return $this->json([
            'signed' => $invoice->isSigned(),
            'signedAt' => $invoice->getSignedAt() ? $invoice->getSignedAt()->format('Y-m-d H:i:s') : null,
            'signerName' => $invoice->getSignerName(),
            'signerEmail' => $invoice->getSignerEmail()
        ]);
    }
}
