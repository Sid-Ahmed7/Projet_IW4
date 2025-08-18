<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\NotificationService;
use App\Entity\PayoutRequest;
use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:test-admin-notifications',
    description: 'Test les notifications admin pour les demandes de retrait',
)]
class TestAdminNotificationsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationService $notificationService,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test des notifications admin');

        // Récupérer les admins
        $admins = $this->userRepository->findByRole('ROLE_ADMIN');
        $io->info(sprintf('Nombre d\'admins trouvés: %d', count($admins)));

        if (empty($admins)) {
            $io->error('Aucun admin trouvé. Créez d\'abord un admin avec: php bin/console app:create-admin');
            return Command::FAILURE;
        }

        foreach ($admins as $admin) {
            $io->text(sprintf('Admin: %s (%s)', $admin->getEmail(), $admin->getFirstName() . ' ' . $admin->getLastName()));
        }

        // Créer une demande de retrait factice pour le test
        $testCompany = $this->entityManager->getRepository(Company::class)->findOneBy([]);
        
        if (!$testCompany) {
            $io->error('Aucune entreprise trouvée pour le test');
            return Command::FAILURE;
        }

        $testPayoutRequest = new PayoutRequest();
        $testPayoutRequest->setCompany($testCompany);
        $testPayoutRequest->setAmount(100.00);
        $testPayoutRequest->setIban('FR1420041010050500013M02606');
        $testPayoutRequest->setBic('CCBPFRPPXXX');
        $testPayoutRequest->setStatus('pending');
        $testPayoutRequest->setCreatedAt(new \DateTime());

        $io->info('Envoi des notifications de test...');

        try {
            $this->notificationService->notifyAdminsNewPayoutRequest($testPayoutRequest);
            $io->success('Notifications envoyées avec succès !');
            $io->note('Vérifiez MailHog sur http://localhost:8025 pour voir les emails');
        } catch (\Exception $e) {
            $io->error('Erreur lors de l\'envoi: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
