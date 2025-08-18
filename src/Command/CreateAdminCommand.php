<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user for FactuPro',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🔐 Création d\'un Administrateur FactuPro');

        // Vérifier s'il y a déjà un admin
        $existingAdmin = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getOneOrNullResult();

        if ($existingAdmin) {
            $io->warning(sprintf('Un administrateur existe déjà: %s', $existingAdmin->getEmail()));
            if (!$io->confirm('Voulez-vous créer un autre administrateur ?')) {
                return Command::SUCCESS;
            }
        }

        $email = $io->ask('Email de l\'administrateur', 'admin@factupro.com');
        $firstname = $io->ask('Prénom', 'Admin');
        $lastname = $io->ask('Nom', 'FactuPro');
        $password = $io->askHidden('Mot de passe (laissez vide pour "admin123")', null, 'admin123');

        // Vérifier que l'email n'existe pas déjà
        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($existingUser) {
            $io->error('Cet email est déjà utilisé');
            return Command::FAILURE;
        }

        $admin = new User();
        $admin->setEmail($email);
        $admin->setFirstname($firstname);
        $admin->setLastname($lastname);
        $admin->setAccountType('personal'); // Type de compte par défaut
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);
        $admin->setIsVerified(true); // Admin directement vérifié

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('✅ Administrateur créé avec succès !');
        $io->definitionList(
            ['Email' => $email],
            ['Nom complet' => $firstname . ' ' . $lastname],
            ['Rôles' => 'ROLE_ADMIN, ROLE_USER'],
            ['URL de connexion' => 'http://localhost:8000/login']
        );

        $io->note('L\'administrateur peut maintenant se connecter et accéder à l\'interface d\'administration des retraits.');

        return Command::SUCCESS;
    }
}
