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
    public function index(
        RequeRepository $requeRepository,
        Security $security,
        InvoiceRepository $invoiceRepository,
        DevisRepository $devisRepository,
        CompanyRepository $companyRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $security->getUser();
        $reques = $requeRepository->findBy(['usr' => $user]);
        $companies = [];
        if ($user) {
            $companies = $companyRepository->findBy(['createdBy' => $user->getId()]);
        }
        
        // Récupérer les devis et factures selon le type de compte
        if ($user->getAccountType() === 'company' && $user->getCompany()) {
            // Pour un compte entreprise, récupérer tous les devis de l'entreprise
            $devis = $devisRepository->findBy(['company' => $user->getCompany()]);
            $invoices = $invoiceRepository->findBy(['company' => $user->getCompany()]);
        } else {
            // Pour un compte personnel, récupérer les devis de l'utilisateur
            $devis = $devisRepository->findBy(['hubuser' => $user]);
            $invoices = $invoiceRepository->findBy(['hubuser' => $user]);
        }
        $paid_invoices = array_filter($invoices, fn($i) => $i->getStatus() === 'paid');
        $total_paid = array_sum(array_map(fn($i) => $i->getAmount(), $paid_invoices));
        $finalized_devis = array_filter($devis, fn($d) => $d->getState() === 'finalise');
        $conversion_rate = count($devis) > 0 ? round((count($finalized_devis) / count($devis)) * 100, 1) : 0;

        // Nouvelles statistiques détaillées
        $pending_devis = array_filter($devis, fn($d) => $d->getState() === 'en_attente');
        $accepted_devis = array_filter($devis, fn($d) => $d->getState() === 'accepte');
        $rejected_devis = array_filter($devis, fn($d) => $d->getState() === 'refuse');
        
        // Devis par entreprise
        $devis_by_company = [];
        foreach ($devis as $d) {
            if ($d->getCompany()) {
                $companyName = $d->getCompany()->getName();
                if (!isset($devis_by_company[$companyName])) {
                    $devis_by_company[$companyName] = ['count' => 0, 'total' => 0];
                }
                $devis_by_company[$companyName]['count']++;
                $devis_by_company[$companyName]['total'] += floatval($d->getPrice());
            }
        }
        
        // Trier par nombre de devis (décroissant)
        uasort($devis_by_company, fn($a, $b) => $b['count'] <=> $a['count']);
        
        // Revenus potentiels (total des devis en attente et acceptés)
        $potential_revenue = 0;
        foreach ($devis as $d) {
            if (in_array($d->getState(), ['en_attente', 'accepte'])) {
                $potential_revenue += floatval($d->getPrice());
            }
        }
        
        // Entreprises les plus actives (celles avec le plus de devis)
        $top_companies = array_slice($devis_by_company, 0, 5, true);

        // Préparer les données pour le graphique
        $monthly_data = [];
        $current_year = (new \DateTime())->format('Y');
        
        // Initialiser les montants mensuels à 0
        for ($i = 1; $i <= 12; $i++) {
            $monthly_data[date('F', mktime(0, 0, 0, $i, 1))] = 0;
        }

        // Calculer les montants mensuels
        foreach ($invoices as $invoice) {
            if ($invoice->getCreatedAt()->format('Y') === $current_year) {
                $month = $invoice->getCreatedAt()->format('F');
                $monthly_data[$month] += $invoice->getAmount();
            }
        }

        // Générer les activités récentes
        $recent_activities = [];

        // Ajouter les devis récents
        foreach ($devis as $d) {
            $recent_activities[] = [
                'title' => 'Devis ' . $d->getTitle(),
                'description' => 'État : ' . $d->getState() . ' - Montant : ' . $d->getPrice() . '€',
                'date' => $d->getCreatedAt(),
                'type' => 'devis'
            ];
        }

        // Ajouter les factures récentes
        foreach ($invoices as $i) {
            $recent_activities[] = [
                'title' => 'Facture ' . $i->getNumber(),
                'description' => 'État : ' . $i->getStatus() . ' - Montant : ' . $i->getAmount() . '€',
                'date' => $i->getCreatedAt(),
                'type' => 'invoice'
            ];
        }

        // Trier les activités par date (les plus récentes d'abord)
        usort($recent_activities, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return $this->render('account/dashboard.html.twig', [
            'user' => $user,
            'reques' => $reques,
            'devis' => $devis,
            'invoices' => $invoices,
            'paid_invoices' => $paid_invoices,
            'total_paid' => $total_paid,
            'finalized_devis' => $finalized_devis,
            'conversion_rate' => $conversion_rate,
            'months' => array_keys($monthly_data),
            'monthly_amounts' => array_values($monthly_data),
            'recent_activities' => $recent_activities,
            'companies' => $companies,
            // Nouvelles statistiques
            'pending_devis' => $pending_devis,
            'accepted_devis' => $accepted_devis,
            'rejected_devis' => $rejected_devis,
            'devis_by_company' => $devis_by_company,
            'top_companies' => $top_companies,
            'potential_revenue' => $potential_revenue,
        ]);
    }

    #[Route('/devis', name: 'app_account_devis')]
    public function devis(DevisRepository $devisRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        if ($user->getAccountType() === 'company' && $user->getCompany()) {
            // Pour un compte entreprise, récupérer tous les devis de l'entreprise
            $devis = $devisRepository->findBy(['company' => $user->getCompany()]);
        } else {
            // Pour un compte personnel, récupérer les devis de l'utilisateur
            $devis = $devisRepository->findBy(['hubuser' => $user]);
        }

        return $this->render('account/devis/index.html.twig', [
            'devis' => $devis,
        ]);
    }

    #[Route('/invoices', name: 'app_account_invoices')]
    public function invoices(InvoiceRepository $invoiceRepository, CompanyRepository $companyRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Debug
        error_log('User ID: ' . $user->getId());
        error_log('Account Type: ' . $user->getAccountType());
        
        // Pour une entreprise, récupérer TOUTES les factures de cette entreprise
        if ($user->getAccountType() === 'company') {
            // Approche directe avec SQL brut
            $connection = $this->getDoctrine()->getConnection();
            $sql = 'SELECT i.* FROM invoice i 
                    INNER JOIN company c ON i.company_id = c.id 
                    WHERE c.user_id = :userId 
                    ORDER BY i.created_at DESC';
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery(['userId' => $user->getId()]);
            $invoiceData = $result->fetchAllAssociative();
            
            error_log('SQL Result count: ' . count($invoiceData));
            error_log('SQL: ' . $sql);
            
            // Convertir les résultats en entités Invoice
            $invoices = [];
            foreach ($invoiceData as $data) {
                $invoice = $invoiceRepository->find($data['id']);
                if ($invoice) {
                    $invoices[] = $invoice;
                }
            }
            
            error_log('Final invoices count: ' . count($invoices));
        } else {
            // Pour un compte personnel, récupérer les factures de l'utilisateur
            $invoices = $invoiceRepository->findBy(['hubuser' => $user], ['createdAt' => 'DESC']);
        }

        return $this->render('account/invoice/index.html.twig', [
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