<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class FactuProIAController extends AbstractController
{
    #[Route('/factuproIA', name: 'app_factupro_ia')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('account/factuproIA/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
} 