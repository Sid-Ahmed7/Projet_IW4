<?php

namespace App\Controller;

use App\Entity\Notification;
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
            $user->setState('online');


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

            $usr= $this->getUser();

            // Notification
            $notification = new Notification();
            $now = new \DateTimeImmutable();

            $notification->setUsers($usr); // ID de l'utilisateur
            $notification->setNotificationTemplate(1); // template de bienvenu ici donc 
            $notification->setType('Systeme'); // Quel type de notification c'est 
            $notification->setTitle('Bienvenu !'); // ducoup le titre mais je pense que je vais enlever la table notification template ce sera moins compliqué 
            $notification->setMessage('Bienvenu sur WeEvent !'); 
            $notification->setIsRead(false);
            $notification->setCreatedAt($now); 




            $entityManager->persist($notification);

            $entityManager->flush();



            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('ibrahim60200@gmail.Com', 'Haze'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_company_index');
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

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
