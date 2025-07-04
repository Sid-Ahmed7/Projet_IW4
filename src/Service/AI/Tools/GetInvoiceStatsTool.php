<?php

namespace App\Service\AI\Tools;

use App\Repository\InvoiceRepository;
use Symfony\Component\Security\Core\Security;

class GetInvoiceStatsTool extends AbstractAITool
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
        return 'get_invoice_stats';
    }

    public function getDescription(): string
    {
        return 'Obtient les statistiques des factures pour une organisation donnée';
    }

    public function execute(array $parameters): array
    {
        $organizationId = $parameters['organization_id'] ?? null;
        $user = $this->security->getUser();
        
        $stats = $this->invoiceRepository->getStatsByOrganization($organizationId, $user);
        
        return [
            'total_invoices' => $stats['total'] ?? 0,
            'pending_invoices' => $stats['pending'] ?? 0,
            'paid_invoices' => $stats['paid'] ?? 0,
            'overdue_invoices' => $stats['overdue'] ?? 0,
            'total_amount' => $stats['total_amount'] ?? 0,
            'pending_amount' => $stats['pending_amount'] ?? 0,
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