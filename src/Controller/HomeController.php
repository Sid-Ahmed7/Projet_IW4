<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
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
            return $this->redirectToRoute('app_company_dashboard');
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
    public function adminDashboard(UserRepository $userRepository, CompanyRepository $companyRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users_count' => $userRepository->count([]),
            'companies_count' => $companyRepository->count([]),
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
    public function companyDashboard(CompanyRepository $companyRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            return $this->redirectToRoute('app_account_company_new', ['id' => $user->getId()]);
        }

        $invoices = [];
        foreach ($company->getDevis() as $devi) {
            $invoices = array_merge($invoices, $devi->getInvoices()->toArray());
        }

        return $this->render('company/dashboard.html.twig', [
            'company' => $company,
            'devis' => $company->getDevis(),
            'invoices' => $invoices,
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
