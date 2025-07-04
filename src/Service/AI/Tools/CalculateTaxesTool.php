<?php

namespace App\Service\AI\Tools;

use App\Repository\InvoiceRepository;
use Symfony\Component\Security\Core\Security;

class CalculateTaxesTool extends AbstractAITool
{
    private InvoiceRepository $invoiceRepository;
    private Security $security;

    public function __construct(InvoiceRepository $invoiceRepository, Security $security)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->security = $security;
    }

    public function getName(): string
    {
        return 'calculate_taxes';
    }

    public function getDescription(): string
    {
        return 'Calcule les taxes et impôts estimés basés sur le chiffre d\'affaires';
    }

    public function execute(array $parameters): array
    {
        $organizationId = $parameters['organization_id'] ?? null;
        $period = $parameters['period'] ?? 'current_month';
        $user = $this->security->getUser();

        $revenue = $this->invoiceRepository->getRevenueForPeriod($organizationId, $period, $user);
        
        // Calcul simplifié des taxes (à adapter selon vos règles fiscales)
        $tva = $revenue * 0.20; // TVA à 20%
        $impotSocietes = $revenue * 0.25; // IS à 25%
        
        return [
            'period' => $period,
            'revenue' => $revenue,
            'tva' => $tva,
            'impot_societes' => $impotSocietes,
            'total_taxes' => $tva + $impotSocietes,
            'note' => 'Ces calculs sont des estimations. Consultez un expert-comptable pour des calculs précis.'
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
                'period' => [
                    'type' => 'string',
                    'description' => 'Période de calcul (current_month, last_month, year_to_date)',
                    'enum' => ['current_month', 'last_month', 'year_to_date'],
                ],
            ],
            'required' => ['period']
        ];
    }
} 