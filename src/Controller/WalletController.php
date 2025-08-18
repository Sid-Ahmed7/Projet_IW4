<?php

namespace App\Controller;

use App\Entity\PayoutRequest;
use App\Entity\Wallet;
use App\Service\PayoutService;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account/wallet')]
#[IsGranted('ROLE_USER')]
class WalletController extends AbstractController
{
    private WalletService $walletService;
    private PayoutService $payoutService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        WalletService $walletService,
        PayoutService $payoutService,
        EntityManagerInterface $entityManager
    ) {
        $this->walletService = $walletService;
        $this->payoutService = $payoutService;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_wallet_dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        $company = $user->getAccountType() === 'company' ? $user->getCompany() : null;

        if (!$company) {
            $this->addFlash('error', 'Vous devez être connecté en tant qu\'entreprise pour accéder au wallet.');
            return $this->redirectToRoute('app_account');
        }

        // Obtenir ou créer le wallet
        $wallet = $this->walletService->getOrCreateWalletForCompany($company);

        // Obtenir l'historique des transactions (10 dernières)
        $transactions = $this->walletService->getTransactionHistory($wallet, 10);

        // Obtenir les demandes de retrait
        $payoutRequests = $this->payoutService->getPayoutRequestsForWallet($wallet);

        // Calculer les statistiques du mois en cours
        $startOfMonth = new \DateTime('first day of this month 00:00:00');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');
        $monthlyStats = $this->walletService->getWalletStats($wallet, $startOfMonth, $endOfMonth);

        return $this->render('account/wallet/dashboard.html.twig', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'payoutRequests' => $payoutRequests,
            'monthlyStats' => $monthlyStats,
            'company' => $company
        ]);
    }

    #[Route('/transactions', name: 'app_wallet_transactions')]
    public function transactions(): Response
    {
        $user = $this->getUser();
        $company = $user->getAccountType() === 'company' ? $user->getCompany() : null;

        if (!$company) {
            $this->addFlash('error', 'Vous devez être connecté en tant qu\'entreprise pour accéder au wallet.');
            return $this->redirectToRoute('app_account');
        }

        $wallet = $this->walletService->getOrCreateWalletForCompany($company);
        $transactions = $this->walletService->getTransactionHistory($wallet);

        return $this->render('account/wallet/transactions.html.twig', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'company' => $company
        ]);
    }

    #[Route('/payout/request', name: 'app_wallet_payout_request', methods: ['GET', 'POST'])]
    public function requestPayout(Request $request): Response
    {
        $user = $this->getUser();
        $company = $user->getAccountType() === 'company' ? $user->getCompany() : null;

        if (!$company) {
            $this->addFlash('error', 'Vous devez être connecté en tant qu\'entreprise pour demander un retrait.');
            return $this->redirectToRoute('app_account');
        }

        $wallet = $this->walletService->getOrCreateWalletForCompany($company);

        if ($request->isMethod('POST')) {
            $amount = trim($request->request->get('amount', ''));
            $iban = trim($request->request->get('iban', ''));
            $bic = trim($request->request->get('bic', ''));
            $accountHolderName = trim($request->request->get('account_holder_name', ''));
            $notes = trim($request->request->get('notes', ''));

            $errors = [];

            // Validation de base
            if (empty($amount) || !is_numeric($amount)) {
                $errors[] = 'Le montant est requis et doit être numérique';
            } elseif ((float)$amount < 10.00) {
                $errors[] = 'Le montant minimum de retrait est de 10€';
            } elseif (!$wallet->hasEnoughBalance($amount)) {
                $errors[] = 'Solde insuffisant pour effectuer ce retrait';
            }

            if (empty($iban)) {
                $errors[] = 'L\'IBAN est requis';
            }

            if (empty($bic)) {
                $errors[] = 'Le BIC est requis';
            }

            if (empty($accountHolderName)) {
                $errors[] = 'Le nom du titulaire du compte est requis';
            }

            // Validation des données bancaires
            $bankValidationErrors = $this->payoutService->validateBankDetails($iban, $bic);
            $errors = array_merge($errors, $bankValidationErrors);

            if (empty($errors)) {
                try {
                    $payoutRequest = $this->payoutService->createPayoutRequest(
                        $wallet,
                        $amount,
                        $iban,
                        $bic,
                        $accountHolderName,
                        $user,
                        $notes
                    );

                    $this->addFlash('success', 'Votre demande de retrait a été créée avec succès. Elle sera traitée dans les plus brefs délais.');
                    return $this->redirectToRoute('app_wallet_dashboard');
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            return $this->render('account/wallet/payout_request.html.twig', [
                'wallet' => $wallet,
                'company' => $company,
                'errors' => $errors,
                'form_data' => [
                    'amount' => $amount,
                    'iban' => $iban,
                    'bic' => $bic,
                    'account_holder_name' => $accountHolderName,
                    'notes' => $notes
                ]
            ]);
        }

        return $this->render('account/wallet/payout_request.html.twig', [
            'wallet' => $wallet,
            'company' => $company
        ]);
    }

    #[Route('/payout/{id}/cancel', name: 'app_wallet_payout_cancel', methods: ['POST'])]
    public function cancelPayout(PayoutRequest $payoutRequest): Response
    {
        $user = $this->getUser();
        $company = $user->getAccountType() === 'company' ? $user->getCompany() : null;

        if (!$company || $payoutRequest->getWallet()->getCompany() !== $company) {
            throw $this->createAccessDeniedException();
        }

        try {
            $this->payoutService->cancelPayoutRequest($payoutRequest);
            $this->addFlash('success', 'La demande de retrait a été annulée.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_wallet_dashboard');
    }

    #[Route('/stats', name: 'app_wallet_stats')]
    public function stats(Request $request): Response
    {
        $user = $this->getUser();
        $company = $user->getAccountType() === 'company' ? $user->getCompany() : null;

        if (!$company) {
            $this->addFlash('error', 'Vous devez être connecté en tant qu\'entreprise pour accéder aux statistiques.');
            return $this->redirectToRoute('app_account');
        }

        $wallet = $this->walletService->getOrCreateWalletForCompany($company);

        // Périodes prédéfinies
        $period = $request->query->get('period', 'month');
        
        switch ($period) {
            case 'week':
                $startDate = new \DateTime('monday this week 00:00:00');
                $endDate = new \DateTime('sunday this week 23:59:59');
                break;
            case 'month':
                $startDate = new \DateTime('first day of this month 00:00:00');
                $endDate = new \DateTime('last day of this month 23:59:59');
                break;
            case 'year':
                $startDate = new \DateTime('first day of january this year 00:00:00');
                $endDate = new \DateTime('last day of december this year 23:59:59');
                break;
            default:
                $startDate = new \DateTime('first day of this month 00:00:00');
                $endDate = new \DateTime('last day of this month 23:59:59');
        }

        $stats = $this->walletService->getWalletStats($wallet, $startDate, $endDate);

        return $this->render('account/wallet/stats.html.twig', [
            'wallet' => $wallet,
            'company' => $company,
            'stats' => $stats,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
