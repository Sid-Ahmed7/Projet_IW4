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
class ComptableController extends AbstractController
{
    #[Route('/', name: 'app_test_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('accueil.html.twig');
    }
    #[Route('/Accueil_user', name: 'Accueil_user', methods: ['GET'])]
    public function index2(UserRepository $userRepository): Response
    {
        return $this->render('company_user/comptable/Accueil_user.html.twig');
    }
    #[Route('/Lesdevis', name: 'Lesdevis', methods: ['GET'])]
    public function index3(UserRepository $userRepository): Response
    {
        return $this->render('company_user/comptable/Lesdevis.html.twig');
    }
    #[Route('/Lesfactures', name: 'Lesfactures', methods: ['GET'])]
    public function index5(UserRepository $userRepository): Response
    {
        return $this->render('company_user/comptable/Lesfactures.html.twig');
    }
    #[Route('/Monprofil', name: 'Monprofil', methods: ['GET'])]
    public function index6(UserRepository $userRepository): Response
    {
        return $this->render('company_user/comptable/Monprofil.html.twig');
    }

    #[Route('/Lespaiements', name: 'Lespaiements', methods: ['GET'])]
    public function index7(UserRepository $userRepository): Response
    {
        return $this->render('company_user/comptable/Lespaiements.html.twig');
    }


}
