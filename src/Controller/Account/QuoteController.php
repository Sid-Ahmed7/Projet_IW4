<?php

namespace App\Controller\Account;

use App\Entity\Quote;
use App\Entity\Company;
use App\Form\QuoteType;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class QuoteController extends AbstractController
{
    #[Route('/quotes', name: 'app_account_quote_index', methods: ['GET'])]
    public function index(QuoteRepository $quoteRepository): Response
    {
        return $this->render('account/quote/index.html.twig', [
            'quotes' => $quoteRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/company/{companyId}/quotes', name: 'app_account_company_quote_index', methods: ['GET'])]
    public function companyQuotes(Company $company, QuoteRepository $quoteRepository): Response
    {
        // Vérifier que l'utilisateur a accès à cette entreprise
        if (!$company->getHubUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('account/quote/index.html.twig', [
            'quotes' => $quoteRepository->findBy(['company' => $company]),
            'company' => $company
        ]);
    }

    #[Route('/company/{companyId}/quotes/new', name: 'app_account_company_quote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if (!$company->getHubUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $quote = new Quote();
        $quote->setCompany($company);
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quote->setCreatedAt(new \DateTimeImmutable());
            $quote->setUser($this->getUser());
            $entityManager->persist($quote);
            $entityManager->flush();

            return $this->redirectToRoute('app_account_company_quote_index', ['companyId' => $company->getId()]);
        }

        return $this->render('account/quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'company' => $company
        ]);
    }

    #[Route('/company/{companyId}/quotes/{id}', name: 'app_account_company_quote_show', methods: ['GET'])]
    public function show(Company $company, Quote $quote): Response
    {
        if (!$company->getHubUsers()->contains($this->getUser()) || $quote->getCompany() !== $company) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('account/quote/show.html.twig', [
            'quote' => $quote,
            'company' => $company
        ]);
    }

    #[Route('/company/{companyId}/quotes/{id}/edit', name: 'app_account_company_quote_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if (!$company->getHubUsers()->contains($this->getUser()) || $quote->getCompany() !== $company) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_account_company_quote_index', ['companyId' => $company->getId()]);
        }

        return $this->render('account/quote/edit.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'company' => $company
        ]);
    }
} 