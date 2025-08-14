<?php

namespace App\Service;

class StatusTranslationService
{
    private const DEVIS_STATUS_TRANSLATIONS = [
        'En attente' => [
            'label' => 'En attente',
            'color' => 'warning',
            'bg_color' => 'bg-yellow-100',
            'text_color' => 'text-yellow-800',
            'icon' => '⏳'
        ],
        'Facturé' => [
            'label' => 'Facturé',
            'color' => 'info',
            'bg_color' => 'bg-blue-100',
            'text_color' => 'text-blue-800',
            'icon' => '🧾'
        ],
        'Payé' => [
            'label' => 'Payé',
            'color' => 'success',
            'bg_color' => 'bg-green-100',
            'text_color' => 'text-green-800',
            'icon' => '✅'
        ],
        'Accepté' => [
            'label' => 'Accepté',
            'color' => 'success',
            'bg_color' => 'bg-green-100',
            'text_color' => 'text-green-800',
            'icon' => '✅'
        ],
        'Refusé' => [
            'label' => 'Refusé',
            'color' => 'danger',
            'bg_color' => 'bg-red-100',
            'text_color' => 'text-red-800',
            'icon' => '❌'
        ]
    ];

    private const INVOICE_STATUS_TRANSLATIONS = [
        'pending' => [
            'label' => 'En attente',
            'color' => 'warning',
            'bg_color' => 'bg-yellow-100',
            'text_color' => 'text-yellow-800',
            'icon' => '⏳'
        ],
        'generated' => [
            'label' => 'Générée',
            'color' => 'info',
            'bg_color' => 'bg-blue-100',
            'text_color' => 'text-blue-800',
            'icon' => '🧾'
        ],
        'paid' => [
            'label' => 'Payée et finalisée',
            'color' => 'success',
            'bg_color' => 'bg-green-100',
            'text_color' => 'text-green-800',
            'icon' => '✅'
        ],
        'overdue' => [
            'label' => 'En retard',
            'color' => 'danger',
            'bg_color' => 'bg-red-100',
            'text_color' => 'text-red-800',
            'icon' => '⚠️'
        ]
    ];

    public function translateDevisStatus(string $status): array
    {
        return self::DEVIS_STATUS_TRANSLATIONS[$status] ?? [
            'label' => $status,
            'color' => 'secondary',
            'bg_color' => 'bg-gray-100',
            'text_color' => 'text-gray-800',
            'icon' => '📄'
        ];
    }

    public function translateInvoiceStatus(string $status): array
    {
        return self::INVOICE_STATUS_TRANSLATIONS[$status] ?? [
            'label' => $status,
            'color' => 'secondary',
            'bg_color' => 'bg-gray-100',
            'text_color' => 'text-gray-800',
            'icon' => '📄'
        ];
    }

    public function getDevisStatusLabel(string $status): string
    {
        return $this->translateDevisStatus($status)['label'];
    }

    public function getInvoiceStatusLabel(string $status): string
    {
        return $this->translateInvoiceStatus($status)['label'];
    }

    public function getDevisStatusBadgeHtml(string $status): string
    {
        $translation = $this->translateDevisStatus($status);
        return sprintf(
            '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold %s %s">%s %s</span>',
            $translation['bg_color'],
            $translation['text_color'],
            $translation['icon'],
            $translation['label']
        );
    }

    public function getInvoiceStatusBadgeHtml(string $status): string
    {
        $translation = $this->translateInvoiceStatus($status);
        return sprintf(
            '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold %s %s">%s %s</span>',
            $translation['bg_color'],
            $translation['text_color'],
            $translation['icon'],
            $translation['label']
        );
    }

    public function getAllDevisStatuses(): array
    {
        return array_keys(self::DEVIS_STATUS_TRANSLATIONS);
    }

    public function getAllInvoiceStatuses(): array
    {
        return array_keys(self::INVOICE_STATUS_TRANSLATIONS);
    }
}
