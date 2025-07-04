<?php

namespace App\Service\AI\Tools;

use App\Repository\DevisRepository;
use Symfony\Component\Security\Core\Security;

class QuoteFollowUpTool extends AbstractAITool
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
        return 'quote_follow_up';
    }

    public function getDescription(): string
    {
        return 'Identifie les devis nécessitant une relance et fournit des recommandations de suivi';
    }

    public function execute(array $parameters): array
    {
        $organizationId = $parameters['organization_id'] ?? null;
        $user = $this->security->getUser();
        
        $quotes = $this->devisRepository->findQuotesToFollowUp($organizationId, $user);
        
        $followUpCategories = [
            'urgent' => [],
            'to_follow' => [],
            'upcoming' => []
        ];
        
        foreach ($quotes as $quote) {
            $daysRemaining = $quote->getValidUntil()->diff(new \DateTime())->days;
            $daysSinceLastContact = $quote->getLastContactDate() 
                ? $quote->getLastContactDate()->diff(new \DateTime())->days 
                : null;
            
            $quoteData = [
                'id' => $quote->getId(),
                'reference' => $quote->getReference(),
                'client' => $quote->getClient()->getName(),
                'amount' => $quote->getAmount(),
                'created_at' => $quote->getCreatedAt()->format('Y-m-d'),
                'valid_until' => $quote->getValidUntil()->format('Y-m-d'),
                'days_remaining' => $daysRemaining,
                'last_contact' => $quote->getLastContactDate() ? $quote->getLastContactDate()->format('Y-m-d') : 'Aucun contact',
                'recommendation' => $this->getRecommendation($daysRemaining, $daysSinceLastContact)
            ];
            
            // Catégorisation des devis
            if ($daysRemaining <= 3 || ($daysSinceLastContact && $daysSinceLastContact > 14)) {
                $followUpCategories['urgent'][] = $quoteData;
            } elseif ($daysRemaining <= 7 || ($daysSinceLastContact && $daysSinceLastContact > 7)) {
                $followUpCategories['to_follow'][] = $quoteData;
            } else {
                $followUpCategories['upcoming'][] = $quoteData;
            }
        }
        
        return [
            'urgent_follow_ups' => $followUpCategories['urgent'],
            'to_follow_up' => $followUpCategories['to_follow'],
            'upcoming_follow_ups' => $followUpCategories['upcoming'],
            'total_to_follow' => count($quotes),
            'summary' => [
                'urgent_count' => count($followUpCategories['urgent']),
                'to_follow_count' => count($followUpCategories['to_follow']),
                'upcoming_count' => count($followUpCategories['upcoming'])
            ]
        ];
    }

    private function getRecommendation(int $daysRemaining, ?int $daysSinceLastContact): string
    {
        if ($daysRemaining <= 3) {
            return 'Relance urgente - Devis proche de l\'expiration';
        }
        
        if ($daysSinceLastContact && $daysSinceLastContact > 14) {
            return 'Relance prioritaire - Aucun contact depuis plus de 2 semaines';
        }
        
        if ($daysRemaining <= 7) {
            return 'Planifier une relance cette semaine';
        }
        
        if ($daysSinceLastContact && $daysSinceLastContact > 7) {
            return 'Reprendre contact - Dernier échange il y a plus d\'une semaine';
        }
        
        return 'Suivi régulier à maintenir';
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