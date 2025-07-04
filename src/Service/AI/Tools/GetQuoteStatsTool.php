<?php

namespace App\Service\AI\Tools;

use App\Repository\DevisRepository;
use Symfony\Component\Security\Core\Security;

class GetQuoteStatsTool extends AbstractAITool
{
    private DevisRepository $devisRepository;
    private Security $security;

    public function __construct(DevisRepository $devisRepository, Security $security)
    {
        $this->devisRepository = $devisRepository;
        $this->security = $security;
    }

    public function getName(): string
    {
        return 'get_quote_stats';
    }

    public function getDescription(): string
    {
        return 'Obtient les statistiques des devis pour une organisation donnée';
    }

    public function execute(array $parameters): array
    {
        $organizationId = $parameters['organization_id'] ?? null;
        $user = $this->security->getUser();
        
        $stats = $this->devisRepository->getStatsByOrganization($organizationId, $user);
        
        return [
            'total_quotes' => $stats['total'] ?? 0,
            'pending_quotes' => $stats['pending'] ?? 0,
            'accepted_quotes' => $stats['accepted'] ?? 0,
            'rejected_quotes' => $stats['rejected'] ?? 0,
            'expired_quotes' => $stats['expired'] ?? 0,
            'total_amount' => $stats['total_amount'] ?? 0,
            'potential_revenue' => $stats['pending_amount'] ?? 0,
            'conversion_rate' => isset($stats['total'], $stats['accepted']) && $stats['total'] > 0 
                ? round(($stats['accepted'] / $stats['total']) * 100, 2) . '%'
                : '0%'
        ];
    }

    protected function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'organization_id' => [
                    'type' => 'integer',
                    'description' => 'ID de l\'organisation (optionnel)',
                ],
            ],
            'required' => []
        ];
    }
} 