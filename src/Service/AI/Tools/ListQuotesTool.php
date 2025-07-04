<?php

namespace App\Service\AI\Tools;

use App\Repository\DevisRepository;
use Symfony\Component\Security\Core\Security;

class ListQuotesTool extends AbstractAITool
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
        return 'list_quotes';
    }

    public function getDescription(): string
    {
        return 'Liste les devis avec possibilité de filtrer par statut, organisation et période';
    }

    public function execute(array $parameters): array
    {
        $organizationId = $parameters['organization_id'] ?? null;
        $status = $parameters['status'] ?? null;
        $period = $parameters['period'] ?? null;
        $user = $this->security->getUser();

        $quotes = $this->devisRepository->findByFilters([
            'organization' => $organizationId,
            'status' => $status,
            'period' => $period,
            'user' => $user
        ]);

        return [
            'quotes' => array_map(function($quote) {
                return [
                    'id' => $quote->getId(),
                    'reference' => $quote->getReference(),
                    'client' => $quote->getClient()->getName(),
                    'amount' => $quote->getAmount(),
                    'status' => $quote->getStatus(),
                    'created_at' => $quote->getCreatedAt()->format('Y-m-d'),
                    'valid_until' => $quote->getValidUntil()->format('Y-m-d'),
                    'days_remaining' => $quote->getValidUntil()->diff(new \DateTime())->days,
                ];
            }, $quotes),
            'total_count' => count($quotes)
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
                'status' => [
                    'type' => 'string',
                    'enum' => ['pending', 'accepted', 'rejected', 'expired'],
                    'description' => 'Statut des devis à filtrer (optionnel)',
                ],
                'period' => [
                    'type' => 'string',
                    'enum' => ['today', 'this_week', 'this_month', 'this_year'],
                    'description' => 'Période de filtrage (optionnel)',
                ],
            ],
            'required' => []
        ];
    }
} 