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
class TestController extends AbstractController
{
    #[Route('/', name: 'app_test_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('accueil.html.twig');
    }

    #[Route('/reserver', name: 'app_test_index2', methods: ['GET'])]
    public function index2(UserRepository $userRepository): Response
    {
        return $this->render('reservation/reserver.html.twig');
    }
}

// #[Route('/reserver')]
// class TestController extends AbstractController
// {
//     #[Route('/', name: 'app_test_index', methods: ['GET'])]
//     public function index(UserRepository $userRepository): Response
//     {
//         return $this->render('accueil.html.twig');
//     }
// }

