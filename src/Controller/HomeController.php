<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accueil')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_test_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('accueil.html.twig');
    }

    #[Route('/reserver', name: 'reserver', methods: ['GET'])]
    public function index2(UserRepository $userRepository): Response
    {
        return $this->render('reservation/reserver.html.twig');
    }
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index3(UserRepository $userRepository): Response
    {
        return $this->render('sidebars/dashboard.html.twig');
    }
    #[Route('/Accueil_company', name: 'Accueil_company', methods: ['GET'])]
    public function index4(UserRepository $userRepository): Response
    {
        return $this->render('company_user/Accueil_company.html.twig');
    }
    #[Route('/Mesfactures_company', name: 'Mesfactures_company', methods: ['GET'])]
    public function index5(UserRepository $userRepository): Response
    {
        return $this->render('company_user/VoirMesfactures.html.twig');
    }
    #[Route('/MonProfil_company', name: 'MonProfil_company', methods: ['GET'])]
    public function index6(UserRepository $userRepository): Response
    {
        return $this->render('company_user/VoirMonProfil.html.twig');
    }
    #[Route('/VoirMespaiements', name: 'VoirMespaiements', methods: ['GET'])]
    public function index7(UserRepository $userRepository): Response
    {
        return $this->render('company_user/VoirLespaiements.html.twig');
    }

    #[Route('/VoirMesdevis_company', name: 'VoirMesdevis', methods: ['GET'])]
    public function index8(UserRepository $userRepository): Response
    {
        return $this->render('company_user/VoirMesdevis.html.twig');
    }

    #[Route('/GererMesClients_company', name: 'GererMesClients', methods: ['GET'])]
    public function index9(UserRepository $userRepository): Response
    {
        return $this->render('company_user/GererMesClients.html.twig');
    }

    #[Route('/Notifications', name: 'Notifications', methods: ['GET'])]
    public function index10(UserRepository $userRepository): Response
    {
        return $this->render('company_user/Notifications.html.twig');
    }

    // ************
    // espace user 

    // ************
   
    #[Route('/Mesfactures_user', name: 'Mesfactures_user', methods: ['GET'])]
    public function index12(UserRepository $userRepository): Response
    {
        return $this->render('company_user/user/Mesfactures_user.html.twig');
    }

    #[Route('/Mesdevis_user', name: 'Mesdevis_user', methods: ['GET'])]
    public function adminDevis(UserRepository $userRepository): Response
    {
        return $this->render('company_user/admin/Mesdevis_user.html.twig');
    }

    #[Route('/MonProfil_user', name: 'MonProfil_user', methods: ['GET'])]
    public function index13(UserRepository $userRepository): Response
    {
        return $this->render('company_user/user/MonProfil_user.html.twig');
    }

    // ************
    // espace admin

    // ************
    #[Route('/Mesfactures_admin', name: 'Mesfactures_admin', methods: ['GET'])]
    public function admoin_factures(UserRepository $userRepository): Response
    {
        return $this->render('company_user/admin/VoirLesfactures_admin.html.twig');
    }

    #[Route('/Mesdevis_admin', name: 'Mesdevis_admin', methods: ['GET'])]
    public function admin_devis(UserRepository $userRepository): Response
    {
        return $this->render('company_user/admin/VoirLesdevis_admin.html.twig');
    }

    #[Route('/MonProfil_admin', name: 'MonProfil_admin', methods: ['GET'])]
    public function admin_profil(UserRepository $userRepository): Response
    {
        return $this->render('company_user/admin/Monprofil_admin.html.twig');
    }
    #[Route('/GererUtilisateurs_admin', name: 'GererUtilisateurs_admin', methods: ['GET'])]
    public function admin_Gersion_utilisateur(UserRepository $userRepository): Response
    {
        return $this->render('company_user/admin/GererLesutilisateurs_admin.html.twig');
    }
}
