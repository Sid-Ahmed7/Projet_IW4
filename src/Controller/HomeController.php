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
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
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
    #[Route('/VoirLespaiements', name: 'VoirLespaiements', methods: ['GET'])]
    public function index7(UserRepository $userRepository): Response
    {
        return $this->render('company_user/VoirLespaiements.html.twig');
    }

    #[Route('/VoirMesdevis', name: 'VoirMesdevis', methods: ['GET'])]
    public function index8(UserRepository $userRepository): Response
    {
        return $this->render('company_user/VoirMesdevis.html.twig');
    }

    #[Route('/GererMesClients', name: 'GererMesClients', methods: ['GET'])]
    public function index9(UserRepository $userRepository): Response
    {
        return $this->render('company_user/GererMesClients.html.twig');
    }

    #[Route('/Notifications', name: 'Notifications', methods: ['GET'])]
    public function index10(UserRepository $userRepository): Response
    {
        return $this->render('company_user/Notifications.html.twig');
        
    }

    #[Route('/newsubscribe', name: 'newsubscribe', methods: ['GET'])]
    public function sub(UserRepository $userRepository): Response
    {
        return $this->render('wizzard/newsubscriber.html.twig');
    }
}
