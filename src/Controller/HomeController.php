<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/home')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('/home/home.html.twig');
    }

    #[Route('/entreprise', name: 'home_entreprise_app', methods: ['GET'])]
    public function index1(UserRepository $userRepository): Response
    {
        return $this->render('/home/entreprise.html.twig');
    }

    #[Route('/request', name: 'home_request_app', methods: ['GET'])]
    public function index2(CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/request.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

}
