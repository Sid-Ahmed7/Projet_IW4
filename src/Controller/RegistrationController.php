<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private LoggerInterface $logger;

    public function __construct(EmailVerifier $emailVerifier, LoggerInterface $logger)
    {
        $this->emailVerifier = $emailVerifier;
        $this->logger      = $logger;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $this->logger->info('Début du processus d\'inscription');

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->logger->info('Formulaire soumis', ['data' => $form->getData()]);

            if ($form->isValid()) {
                $this->logger->info('Formulaire valide');

                // --- Unicité de l'email et du username
                if ($entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])) {
                    $this->addFlash('error', 'Cette adresse email est déjà utilisée.');
                    return $this->render('registration/register.html.twig', [
                        'registrationForm' => $form->createView(),
                    ]);
                }
                if ($entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()])) {
                    $this->addFlash('error', 'Ce nom d\'utilisateur est déjà pris.');
                    return $this->render('registration/register.html.twig', [
                        'registrationForm' => $form->createView(),
                    ]);
                }

                // --- Hashage du mot de passe
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                // --- Valeurs par défaut
                $user
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setRoles(['ROLE_USER'])
                    ->setEmailVerificationToken(bin2hex(random_bytes(32)))
                    ->setIsVerified(false)
                    ->setPicture('no-user.jpg') // valeur par défaut si vous en avez besoin
                ;

                // --- Persist + Flush
                $entityManager->persist($user);
                $entityManager->flush();
                $this->logger->info('Utilisateur enregistré', ['id' => $user->getId()]);

                // --- Envoi de l'email de confirmation
                $email = (new TemplatedEmail())
                    ->from(new Address('leonceyopa@gmail.com', 'FactuPro'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'verifyEmailUrl' => $this->generateUrl(
                            'app_verify_email',
                            [
                                'id'    => $user->getId(),
                                'token' => $user->getEmailVerificationToken(),
                            ],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                    ])
                ;
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $email);

                $this->addFlash('success', 'Votre compte a été créé. Veuillez vérifier votre email.');
                $this->logger->info('Email de confirmation envoyé');

                return $this->redirectToRoute('app_login');
            }

            // --- Form invalid: log & flash
            $this->logger->error('Formulaire invalide', [
                'errors' => (string) $form->getErrors(true, false)
            ]);
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('success', 'Votre adresse email a été vérifiée.');
        return $this->redirectToRoute('app_login');
    }
}
