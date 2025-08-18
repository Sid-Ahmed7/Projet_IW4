<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\CompanyRepository;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Price;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $invoices = $invoiceRepository->findBy(['hubuser' => $user]);

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/company/{companyId}/invoice', name: 'app_company_invoice')]
    public function companyInvoices(int $companyId, InvoiceRepository $invoiceRepository, CompanyRepository $companyRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Récupérer l'objet Company
        $company = $companyRepository->find($companyId);
        if (!$company) {
            throw $this->createNotFoundException('Company not found');
        }
        
        // Chercher les factures par company (objet entité)
        $invoices = $invoiceRepository->findBy(['company' => $company]);

        return $this->render('invoice/company.html.twig', [
            'invoices' => $invoices,
            'company' => $company,
        ]);
    }

    #[Route('/devis/{id}/invoice/new', name: 'app_invoice_new_from_devis', methods: ['GET', 'POST'])]
    public function newFromDevis(Devis $devis, Request $request, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vérifier que l'utilisateur est propriétaire du devis
        if ($devis->getHubuser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }

        // Vérifier que le devis n'est pas déjà facturé (par état)
        if ($devis->getState() === 'Facturé') {
            $this->addFlash('error', 'Ce devis a déjà été facturé.');
            return $this->redirectToRoute('app_devis_show', ['id' => $devis->getId()]);
        }

        // Vérification supplémentaire : s'assurer qu'aucune facture n'existe déjà pour ce devis
        $existingInvoices = $entityManager->getRepository(Invoice::class)->findBy(['devis' => $devis]);
        if (count($existingInvoices) > 0) {
            $this->addFlash('error', 'Une facture existe déjà pour ce devis.');
            return $this->redirectToRoute('app_devis_show', ['id' => $devis->getId()]);
        }

        $invoice = new Invoice();
        $invoice->setHubuser($user);
        $invoice->setCompany($devis->getCompany());
        $invoice->setDevis($devis);
        $invoice->setAmount((float)$devis->getPrice());
        $invoice->setDescription($devis->getContent());
        $invoice->setStatus('paid'); // Facture payée et finalisée depuis un devis payé
        
        // Générer un numéro unique pour la facture (année + mois + ID)
        $invoice->setNumber(date('Ym') . '-' . uniqid());
        
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le statut du devis
            $devis->setState('Facturé');
            
            $entityManager->persist($invoice);
            $entityManager->flush();

            // Envoi de la notification par email
            $notificationService->notifyInvoiceCreated($invoice);

            $this->addFlash('success', 'La facture a été créée avec succès.');
            return $this->redirectToRoute('app_invoice_show', ['id' => $invoice->getId()]);
        }

        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'devis' => $devis,
        ]);
    }

    #[Route('/invoice/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($invoice->getHubuser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette facture.');
        }

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/invoice/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($invoice->getHubuser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette facture.');
        }

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/invoice/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($invoice->getHubuser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette facture.');
        }

        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
