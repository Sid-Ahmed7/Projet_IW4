<?php

namespace App\Twig;

use App\Service\StatusTranslationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class StatusExtension extends AbstractExtension
{
    public function __construct(
        private StatusTranslationService $statusTranslationService
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('devis_status', [$this, 'getDevisStatusLabel']),
            new TwigFilter('invoice_status', [$this, 'getInvoiceStatusLabel']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('devis_status_badge', [$this, 'getDevisStatusBadge'], ['is_safe' => ['html']]),
            new TwigFunction('invoice_status_badge', [$this, 'getInvoiceStatusBadge'], ['is_safe' => ['html']]),
            new TwigFunction('devis_status_info', [$this, 'getDevisStatusInfo']),
            new TwigFunction('invoice_status_info', [$this, 'getInvoiceStatusInfo']),
        ];
    }

    public function getDevisStatusLabel(string $status): string
    {
        return $this->statusTranslationService->getDevisStatusLabel($status);
    }

    public function getInvoiceStatusLabel(string $status): string
    {
        return $this->statusTranslationService->getInvoiceStatusLabel($status);
    }

    public function getDevisStatusBadge(string $status): string
    {
        return $this->statusTranslationService->getDevisStatusBadgeHtml($status);
    }

    public function getInvoiceStatusBadge(string $status): string
    {
        return $this->statusTranslationService->getInvoiceStatusBadgeHtml($status);
    }

    public function getDevisStatusInfo(string $status): array
    {
        return $this->statusTranslationService->translateDevisStatus($status);
    }

    public function getInvoiceStatusInfo(string $status): array
    {
        return $this->statusTranslationService->translateInvoiceStatus($status);
    }
}
