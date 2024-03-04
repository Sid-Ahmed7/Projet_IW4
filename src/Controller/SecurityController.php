<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Util\Exception\XmlParsingException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface; // Ajoutez cette ligne
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('voirMonProfile_app');
        }
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        if ($lastUsername) {
            $user = $userRepository->findOneByEmail($lastUsername); // Utilisez la méthode appropriée pour obtenir l'utilisateur par email
            if ($user && !$user->isVerified()) {
                // L'utilisateur n'a pas vérifié son e-mail
                $error = new CustomUserMessageAuthenticationException('Please verify your email address before logging in.');
            }
        }
    
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    // #[Route(path: '/reset', name: 'app_reset')]
    // public function reset(AuthenticationUtils $authenticationUtils): Response
    // {
    //     if ($this->getUser()) {
    //         return $this->redirectToRoute('app_reset');
    //     }
    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('security/reset.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    // }

    #[Route(path: '/reset', name: 'app_reset')]
public function reset(Request $request, MailerInterface $mailer, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);
        if ($user) {
            // Générer le token de réinitialisation
            $resetToken = Uuid::v4()->toRfc4122();
            $user->setResetToken($resetToken);
            $entityManager->persist($user);
            $entityManager->flush();

            // Créer l'email
            $email = (new Email())
                ->from('ibrahim60200@gmail.com')
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->html('<p>To reset your password, please click the link below</p><a href="http://localhost:8000/reset-password?token=' . $resetToken . '">Reset Password</a>');

            // Envoyer l'email
            $mailer->send($email);

            // Rediriger ou afficher un message de succès
            $this->addFlash('success', 'A password reset link has been sent to your email.');
            return $this->redirectToRoute('app_login');
        } else {
            $this->addFlash('error', 'No account found for this email.');
        }
    }

    return $this->render('security/reset.html.twig');
}


#[Route('/reset-password', name: 'app_reset_password')]
public function resetPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
{
    $token = $request->query->get('token');
    // Vérifier le token et obtenir l'utilisateur correspondant
    $user = $userRepository->findOneBy(['resetToken' => $token]);

    if (!$user) {
        // Gérer l'erreur si le token n'est pas valide
        $this->addFlash('error', 'This reset token is invalid.');
        return $this->redirectToRoute('app_login');
    }

    if ($request->isMethod('POST')) {
        $password = $request->request->get('password');
        $confirmPassword = $request->request->get('confirm_password');

        if ($password !== $confirmPassword) {
            $this->addFlash('error', 'Passwords do not match.');
            return $this->redirectToRoute('app_reset_password', ['token' => $token]);
        }

        // Réinitialiser le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setResetToken(null);
        $entityManager->persist($user);
        $entityManager->flush();


        // Envoyer un e-mail de confirmation
        $email = (new Email())
        ->from('ibrahim60200@gmail.com')
        ->to($user->getEmail())
        ->subject('Confirmation de changement de mot de passe')
        ->text('Votre mot de passe a été changé avec succès.')
        ->html('<p>Votre mot de passe a été changé avec succès.</p>');
    
        $mailer->send($email);
    
        // Ajouter un message flash pour indiquer que le mot de passe a été changé avec succès
        $this->addFlash('success', 'Your password has been successfully changed.');

        // Rediriger vers la page de connexion avec le message flash
        return $this->redirectToRoute('app_login');
    }

    // Afficher le formulaire de réinitialisation du mot de passe
    return $this->render('security/reset_password.html.twig', [
        'token' => $token,
    ]); 
}



    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
