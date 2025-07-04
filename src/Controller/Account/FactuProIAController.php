<?php

namespace App\Controller\Account;

use App\Service\AI\GroqService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account/factuproIA', name: 'app_account_factupro_ia_')]
class FactuProIAController extends AbstractController
{
    public function __construct(
        private GroqService $groqService
    ) {
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('account/factuproIA/index.html.twig');
    }

    #[Route('/chat', name: 'chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $data = json_decode($request->getContent(), true);
            $message = $data['message'] ?? '';
            $context = $data['context'] ?? [];

            if (empty($message)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Le message ne peut pas être vide'
                ]);
            }

            $result = $this->groqService->chat($message, $context);

            return new JsonResponse([
                'success' => true,
                'response' => $result['response'],
                'context' => $result['context'],
                'data' => null
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Une erreur est survenue lors du traitement de votre demande: ' . $e->getMessage()
            ], 500);
        }
    }
} 