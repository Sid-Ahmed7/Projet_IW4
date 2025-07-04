<?php

namespace App\Service\AI\Tools;

use App\Repository\CompanyRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ListOrganizationsTool implements AIToolInterface
{
    private CompanyRepository $companyRepository;
    private Security $security;

    public function __construct(
        CompanyRepository $companyRepository,
        Security $security
    ) {
        $this->companyRepository = $companyRepository;
        $this->security = $security;
    }

    public function execute(): array
    {
        $user = $this->security->getUser();
        if (!$user) {
            return ['error' => 'Utilisateur non connecté'];
        }

        $companies = $this->companyRepository->findByUser($user);
        
        return array_map(function($company) {
            return [
                'id' => $company->getId(),
                'name' => $company->getName(),
                'type' => 'Entreprise', // Type par défaut
                'status' => $company->getStatus() ?? 'Actif' // Status par défaut si null
            ];
        }, $companies);
    }

    public function getName(): string
    {
        return 'ListOrganizationsTool';
    }

    public function getDescription(): string
    {
        return 'Liste toutes les organisations accessibles par l\'utilisateur courant';
    }

    protected function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => []
        ];
    }
} 