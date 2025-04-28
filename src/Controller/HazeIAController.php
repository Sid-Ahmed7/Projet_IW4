<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class HazeIAController extends AbstractController
{
    #[Route('/hazeIA', name: 'app_haze_ia')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('account/hazeIA/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
} 