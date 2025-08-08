<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UserExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('account_type_label', [$this, 'getAccountTypeLabel']),
        ];
    }

    public function getAccountTypeLabel(string $accountType): string
    {
        return match($accountType) {
            User::ACCOUNT_TYPE_PERSONAL => 'Compte Personnel',
            User::ACCOUNT_TYPE_COMPANY => 'Compte Entreprise',
            default => 'Type inconnu',
        };
    }
}
