<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:promote-admin',
    description: 'Promote a user to admin role',
)]
class PromoteAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'User email to promote');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            $io->error('Utilisateur avec l\'email "' . $email . '" introuvable.');
            return Command::FAILURE;
        }

        $currentRoles = $user->getRoles();
        
        if (in_array('ROLE_ADMIN', $currentRoles)) {
            $io->warning('L\'utilisateur a déjà le rôle ADMIN.');
            return Command::SUCCESS;
        }

        // Ajouter ROLE_ADMIN
        $newRoles = array_unique(array_merge($currentRoles, ['ROLE_ADMIN']));
        $user->setRoles($newRoles);

        $this->entityManager->flush();

        $io->success([
            '🎉 Utilisateur promu administrateur !',
            '',
            '📧 Email: ' . $user->getEmail(),
            '👤 Nom: ' . $user->getFirstname() . ' ' . $user->getLastname(),
            '🏢 Type: ' . ucfirst($user->getAccountType()),
            '🔑 Nouveaux rôles: ' . implode(', ', $user->getRoles()),
            '',
            'L\'utilisateur peut maintenant accéder à /admin/payouts'
        ]);

        return Command::SUCCESS;
    }
}
