<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            /** @var User $user */
            $user = $this->getUser();
            
            // Redirection selon le type de compte
            if ($user->getAccountType() === 'company') {
                return $this->redirectToRoute('app_company_dashboard');
            } else {
                // Compte personnel - redirection vers le dashboard personnel
                return $this->redirectToRoute('app_account');
            }
        }
        return $this->render('home/home.html.twig');
    }

    #[Route('/entreprise', name: 'home_entreprise_app', methods: ['GET'])]
    public function index1(UserRepository $userRepository): Response
    {
        return $this->render('/home/entreprise.html.twig');
    }

    #[Route('/request', name: 'home_request_app', methods: ['GET'])]
    public function index2(): Response
    {
        return $this->render('home/request.html.twig');
    }

    #[Route('/admin', name: 'app_admin_dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function adminDashboard(
        UserRepository $userRepository, 
        CompanyRepository $companyRepository, 
        DevisRepository $devisRepository, 
        InvoiceRepository $invoiceRepository
    ): Response
    {
        // Statistiques de base
        $usersCount = $userRepository->count([]);
        $companiesCount = $companyRepository->count([]);
        $devisCount = $devisRepository->count([]);
        $invoicesCount = $invoiceRepository->count([]);

        // Devis par statut
        $devisEnAttente = $devisRepository->count(['state' => 'en_attente']);
        $devisAcceptes = $devisRepository->count(['state' => 'accepte']);
        $devisRefuses = $devisRepository->count(['state' => 'refuse']);
        $devisFinalises = $devisRepository->count(['state' => 'finalise']);

        // Factures par statut
        $facturesEnAttente = $invoiceRepository->count(['status' => 'pending']);
        $facturesPayees = $invoiceRepository->count(['status' => 'paid']);
        $facturesEchues = $invoiceRepository->count(['status' => 'overdue']);

        // Top entreprises par nombre de devis
        $topCompanies = $companyRepository->findTopCompaniesByDevisCount(10);

        return $this->render('admin/dashboard.html.twig', [
            'users_count' => $usersCount,
            'companies_count' => $companiesCount,
            'devis_count' => $devisCount,
            'invoices_count' => $invoicesCount,
            'devis_en_attente' => $devisEnAttente,
            'devis_acceptes' => $devisAcceptes,
            'devis_refuses' => $devisRefuses,
            'devis_finalises' => $devisFinalises,
            'factures_en_attente' => $facturesEnAttente,
            'factures_payees' => $facturesPayees,
            'factures_echues' => $facturesEchues,
            'top_companies' => $topCompanies,
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users', methods: ['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function adminUsers(UserRepository $userRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/company/dashboard', name: 'app_company_dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function companyDashboard(CompanyRepository $companyRepository, DevisRepository $devisRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            return $this->redirectToRoute('app_account_company_new', ['id' => $user->getId()]);
        }

        // Récupérer tous les devis de l'entreprise
        $devis = $devisRepository->findBy(['company' => $company]);
        
        // Organiser les devis par statut
        $pendingDevis = array_filter($devis, fn($d) => $d->getState() === 'en_attente');
        $acceptedDevis = array_filter($devis, fn($d) => $d->getState() === 'accepte');
        $rejectedDevis = array_filter($devis, fn($d) => $d->getState() === 'refuse');
        $finalizedDevis = array_filter($devis, fn($d) => $d->getState() === 'finalise');

        // Récupérer toutes les factures liées aux devis de l'entreprise
        $invoices = [];
        foreach ($devis as $devi) {
            $invoices = array_merge($invoices, $devi->getInvoices()->toArray());
        }

        // Calculer les revenus potentiels (devis acceptés et finalisés)
        $potentialRevenue = 0;
        foreach (array_merge($acceptedDevis, $finalizedDevis) as $devi) {
            $potentialRevenue += (float)$devi->getPrice();
        }

        // Calculer le taux de conversion
        $totalDevis = count($devis);
        $convertedDevis = count($finalizedDevis);
        $conversionRate = $totalDevis > 0 ? round(($convertedDevis / $totalDevis) * 100, 1) : 0;

        // Grouper par entreprise pour les stats
        $devisByCompany = [$company->getName() => [
            'count' => count($devis),
            'total' => array_sum(array_map(fn($d) => (float)$d->getPrice(), $devis))
        ]];

        $topCompanies = [$company->getName() => [
            'count' => count($devis),
            'total' => array_sum(array_map(fn($d) => (float)$d->getPrice(), $devis))
        ]];

        // Activités récentes (derniers devis et factures)
        $recentActivities = [];
        foreach (array_slice(array_reverse($devis), 0, 5) as $devi) {
            $recentActivities[] = [
                'type' => 'devis',
                'title' => "Devis #{$devi->getId()}",
                'description' => "Statut: {$devi->getState()} - {$devi->getPrice()}€",
                'date' => $devi->getCreatedAt()
            ];
        }

        // Données pour le graphique (mois et montants)
        $months = [];
        $monthlyAmounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-$i month");
            $months[] = $date->format('M Y');
            
            $monthInvoices = array_filter($invoices, function($invoice) use ($date) {
                return $invoice->getCreatedAt()->format('Y-m') === $date->format('Y-m');
            });
            
            $monthlyAmounts[] = array_sum(array_map(fn($inv) => $inv->getAmount(), $monthInvoices));
        }

        return $this->render('company/dashboard.html.twig', [
            'company' => $company,
            'devis' => $devis,
            'invoices' => $invoices,
            'pending_devis' => $pendingDevis,
            'accepted_devis' => $acceptedDevis,
            'rejected_devis' => $rejectedDevis,
            'finalized_devis' => $finalizedDevis,
            'potential_revenue' => $potentialRevenue,
            'conversion_rate' => $conversionRate,
            'devis_by_company' => $devisByCompany,
            'top_companies' => $topCompanies,
            'recent_activities' => $recentActivities,
            'months' => $months,
            'monthly_amounts' => $monthlyAmounts,
        ]);
    }

    #[Route('/company/team', name: 'app_company_team', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function companyTeam(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            throw $this->createAccessDeniedException('Vous devez être rattaché à une entreprise pour accéder à cette page.');
        }

        return $this->render('company/team.html.twig', [
            'company' => $company,
            'team_members' => $company->gethubUsers(),
        ]);
    }

    #[Route('/company/settings', name: 'app_company_settings', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function companySettings(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            throw $this->createAccessDeniedException('Vous devez être rattaché à une entreprise pour accéder à cette page.');
        }

        return $this->render('company/settings.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/company/requests', name: 'app_company_requests', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function companyRequests(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            throw $this->createAccessDeniedException('Vous devez être rattaché à une entreprise pour accéder à cette page.');
        }

        return $this->render('company/requests.html.twig', [
            'company' => $company,
            'requests' => $company->getDevis(),
        ]);
    }
}
