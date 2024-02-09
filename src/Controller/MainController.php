<?php

namespace App\Controller;


use App\Entity\Notification;
use App\Entity\UserRole;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    #[Route('/invite/user/{companyId}/{userId}/{senderId}', name: 'app_invite_user', methods: ['GET', 'POST'])]
    public function inviteUser(Request $request, EntityManagerInterface $entityManager, $userId, UserRepository $userRepository, $companyId, CompanyRepository $companyRepository): Response
    {

        $usr = $userRepository->findOneBy(['id' => $userId]);
        // $comp = $companyRepository->findOneBy(['id' => $companyId]);

        // Créer un nouveau UserRole avec un état "offline"
        $userRole = new UserRole();
        $userRole->setUsr($userId);
        $userRole->setCompany($companyId); // Assumer que setCompany accepte un ID d'organisation
        $userRole->setRoleName('Votre rôle spécifique');
        $userRole->setCreatedAt(new \DateTimeImmutable());
        $userRole->setCreatedBy($userId);
        $userRole->setState('offline');
        $entityManager->persist($userRole);

        $comp = $companyRepository->findOneBy(['id' => $companyId]);

        // Créer une nouvelle Notification
        $notification = new Notification();
        $notification->setUsers($usr); 
        $notification->setTitle('Invitation à rejoindre l\'organisation');

        if ($comp) {
            $name = $comp->getName(); // Récupérez le nom de la company
            $notification->setMessage("Bonjour, l'entreprise {$name} vous a envoyé une invitation");
        }
        $notification->setIsRead(false);
        $notification->setType('Organization');
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($notification);

        $entityManager->flush();

        return $this->json([
            'message' => 'L\'invitation et la notification ont été créées avec succès.'
        ]);
    }
}
