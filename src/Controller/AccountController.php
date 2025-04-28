<?php

namespace App\Controller;

use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use App\Repository\CompanyRepository;
use App\Form\ProfileEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RequeRepository;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(RequeRepository $requeRepository, Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $security->getUser();
        $reques = $requeRepository->findBy(['usr' => $user]);

        return $this->render('account/dashboard.html.twig', [
            'user' => $user,
            'reques' => $reques,
        ]);
    }

    #[Route('/devis', name: 'app_account_devis')]
    public function devis(DevisRepository $devisRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $devis = $devisRepository->findBy(['hubuser' => $user]);

        return $this->render('account/devis/index.html.twig', [
            'devis' => $devis,
        ]);
    }

    #[Route('/invoices', name: 'app_account_invoices')]
    public function invoices(InvoiceRepository $invoiceRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $invoices = $invoiceRepository->findBy(['hubuser' => $user]);

        return $this->render('account/invoices/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/organization/{companyId}/devis', name: 'app_account_organization_devis')]
    public function organizationDevis(
        int $companyId, 
        DevisRepository $devisRepository,
        CompanyRepository $companyRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $company = $companyRepository->find($companyId);

        if (!$company || $user->getCompany() !== $company) {
            throw $this->createNotFoundException('Organization not found');
        }

        $devis = $devisRepository->findBy([
            'hubuser' => $user,
            'company' => $company
        ]);

        return $this->render('account/organization/devis.html.twig', [
            'devis' => $devis,
            'company' => $company
        ]);
    }

    #[Route('/organization/{companyId}/invoices', name: 'app_account_organization_invoices')]
    public function organizationInvoices(
        int $companyId, 
        InvoiceRepository $invoiceRepository,
        CompanyRepository $companyRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $company = $companyRepository->find($companyId);

        if (!$company || $user->getCompany() !== $company) {
            throw $this->createNotFoundException('Organization not found');
        }

        $invoices = $invoiceRepository->findBy([
            'hubuser' => $user,
            'company' => $company
        ]);

        return $this->render('account/organization/invoices.html.twig', [
            'invoices' => $invoices,
            'company' => $company
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 