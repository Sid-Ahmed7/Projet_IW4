<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
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
    public function index(InvoiceRepository $invoiceRepository, DevisRepository $devisRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $invoices = $invoiceRepository->findBy(['hubuser' => $user]);
        $devis = $devisRepository->findBy(['hubuser' => $user, 'state' => 'En attente']);

        return $this->render('account/invoice/index.html.twig', [
            'invoices' => $invoices,
            'devis' => $devis,
        ]);
    }

    #[Route('/company/{companyId}/invoices', name: 'app_company_invoices')]
    public function companyInvoices(int $companyId, InvoiceRepository $invoiceRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $invoices = $invoiceRepository->findBy(['hubuser' => $user, 'company' => $companyId]);

        return $this->render('invoice/company.html.twig', [
            'invoices' => $invoices,
            'companyId' => $companyId,
        ]);
    }

    #[Route('/devis/{id}/invoice/new', name: 'app_invoice_new', methods: ['GET'])]
    public function new(EntityManagerInterface $entityManager, DevisRepository $devisRepository, $id, InvoiceRepository $invoice): Response
    {
        $devis = $devisRepository->find($id);

        if (!$devis) {
            throw $this->createNotFoundException('Le devis avec l\'id "' . $id . '" n\'existe pas.');
        }

        $existingInvoice = $invoice->findOneBy(['devis' => $devis]);

        if ($existingInvoice) {
            return $this->redirectToRoute('app_invoice_show', ['id' => $existingInvoice->getId()]);
        }

        $invoice = new Invoice();
        $invoice->setHubuser($this->getUser());
        $invoice->setCompany($devis->getCompany());
        $invoice->setAmount($devis->getPrice());
        $invoice->setNumber(date('YmdHis') . '-' . $devis->getId());
        $invoice->setDescription('Facture pour le devis: ' . $devis->getTitle());
        $invoice->setStatus('pending');
        $invoice->setDevis($devis);
        $invoice->setCreatedAt(new \DateTimeImmutable());

        // Mettre à jour le statut du devis
        $devis->setState('Facturé');
        
        $entityManager->persist($invoice);
        $entityManager->flush();

        return $this->redirectToRoute('app_invoice_show', ['id' => $invoice->getId()]);
    }

    #[Route('/invoice/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/invoice/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
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
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
