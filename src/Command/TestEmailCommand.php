<?php

namespace App\Command;

use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[AsCommand(
    name: 'app:test-email',
    description: 'Test email sending functionality'
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Testing email functionality...');

        try {
            $email = (new TemplatedEmail())
                ->from('noreply@factupro.com')
                ->to('test@example.com')
                ->subject('Test email from FactuPro')
                ->html('<h1>Test Email</h1><p>This is a test email to verify email functionality.</p>');

            $this->mailer->send($email);
            
            $output->writeln('✅ Email sent successfully!');
            
            // Afficher l'URL appropriée selon l'environnement
            if ($_ENV['APP_ENV'] === 'dev') {
                $output->writeln('Check http://localhost:8025 to see the email in MailHog.');
            } else {
                $output->writeln('Email sent via production mailer.');
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $output->writeln('❌ Failed to send email: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
