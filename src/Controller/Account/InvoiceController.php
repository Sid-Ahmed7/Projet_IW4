<?php

namespace App\Controller\Account;

use App\Entity\Invoice;
use App\Entity\Company;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class InvoiceController extends AbstractController
{
    #[Route('/invoices', name: 'app_account_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('account/invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findBy(['hubuser' => $this->getUser()]),
        ]);
    }

    #[Route('/company/{companyId}/invoices', name: 'app_account_company_invoice_index', methods: ['GET'])]
    public function companyInvoices(Company $company, InvoiceRepository $invoiceRepository): Response
    {
        if (!$company->getHubUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('account/invoice/company.html.twig', [
            'invoices' => $invoiceRepository->findBy([
                'hubuser' => $this->getUser(),
                'company' => $company
            ]),
            'company' => $company,
        ]);
    }

    #[Route('/company/{companyId}/invoices/new', name: 'app_account_company_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if (!$company->getHubUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $invoice = new Invoice();
        $invoice->setCompany($company);
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice->setCreatedAt(new \DateTimeImmutable());
            $invoice->setHubuser($this->getUser());
            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('app_account_company_invoice_index', ['companyId' => $company->getId()]);
        }

        return $this->render('account/invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'company' => $company
        ]);
    }

    #[Route('/company/{companyId}/invoices/{id}', name: 'app_account_company_invoice_show', methods: ['GET'])]
    public function show(Company $company, Invoice $invoice): Response
    {
        if (!$company->getHubUsers()->contains($this->getUser()) || $invoice->getCompany() !== $company) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('account/invoice/show.html.twig', [
            'invoice' => $invoice,
            'company' => $company
        ]);
    }

    #[Route('/company/{companyId}/invoices/{id}/edit', name: 'app_account_company_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if (!$company->getHubUsers()->contains($this->getUser()) || $invoice->getCompany() !== $company) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_account_company_invoice_index', ['companyId' => $company->getId()]);
        }

        return $this->render('account/invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'company' => $company
        ]);
    }
} 