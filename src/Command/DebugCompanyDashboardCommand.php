<?php

namespace App\Command;

use App\Repository\CompanyRepository;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:debug-company-dashboard',
    description: 'Debug le dashboard de l\'entreprise'
)]
class DebugCompanyDashboardCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private CompanyRepository $companyRepository;
    private DevisRepository $devisRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyRepository $companyRepository,
        DevisRepository $devisRepository
    ) {
        $this->entityManager = $entityManager;
        $this->companyRepository = $companyRepository;
        $this->devisRepository = $devisRepository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Récupérer l'entreprise ESGI
        $company = $this->companyRepository->find(1);
        if (!$company) {
            $io->error('Entreprise ESGI non trouvée');
            return Command::FAILURE;
        }

        $io->info("Entreprise: {$company->getName()}");

        // Récupérer tous les devis de l'entreprise
        $devis = $this->devisRepository->findBy(['company' => $company]);
        $io->info("Total devis pour l'entreprise: " . count($devis));

        foreach ($devis as $devi) {
            $io->info("Devis #{$devi->getId()}: {$devi->getTitle()} - État: {$devi->getState()} - Prix: {$devi->getPrice()}€");
        }

        // Organiser les devis par statut
        $pendingDevis = array_filter($devis, fn($d) => in_array($d->getState(), ['En attente de validation', 'En attente']));
        $acceptedDevis = array_filter($devis, fn($d) => $d->getState() === 'Validé');
        $rejectedDevis = array_filter($devis, fn($d) => $d->getState() === 'Rejeté');
        $finalizedDevis = array_filter($devis, fn($d) => in_array($d->getState(), ['Facturé', 'Payé']));

        $io->section('Répartition par statut:');
        $io->info("En attente: " . count($pendingDevis));
        $io->info("Validés: " . count($acceptedDevis));
        $io->info("Rejetés: " . count($rejectedDevis));
        $io->info("Finalisés: " . count($finalizedDevis));

        return Command::SUCCESS;
    }
}
