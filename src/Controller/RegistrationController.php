<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Filesystem;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

use App\Repository\UserRepository;
use Stripe\Customer;
use Stripe\Stripe;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setSignupDate(new \DateTime());
            $user->setRoles(['ROLE_USER']);


            // Gestion du picture
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile instanceof UploadedFile) {
                $filesystem = new Filesystem();
                $pictureFileName = md5(uniqid()) . '.' . $pictureFile->guessExtension();
                $pictureFile->move($this->getParameter('profilePicture_directory'), $pictureFileName);
                $user->setPicture($pictureFileName);
            } else {
                // si l'utilisateur ne souhaite pas mettre de pp cela rest à voir !!
                $defaultPictureFileName = $this->getParameter('profilePicture_directory') . '/no-user.jpg';
                $user->setPicture($defaultPictureFileName);
            }

            $entityManager->persist($user);

            // Création de l'identifiant client dans Stripe
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $stripeCustomer = Customer::create([
                'email' => $user->getEmail(),
                'name' => $user->getFirstName() . ' ' . $user->getLastName(),

            ]);

            // Associez l'identifiant client à l'utilisateur dans votre application
            $user->setStripeCustomerId($stripeCustomer->id);
            $entityManager->persist($user);
            $entityManager->flush();

            // Dans votre méthode register du RegistrationController EMAIL
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('ibrahim60200@gmail.Com', 'Haze'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'verifyEmailUrl' => $this->generateUrl(
                            'app_verify_email',
                            ['id' => $user->getId(), 'token' => $user->getEmailVerificationToken()],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                    ])
            );

            // do anything else you need here, like send an email
            $this->addFlash('success', 'Your account has been created. Please check your email to verify your account before logging in.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
