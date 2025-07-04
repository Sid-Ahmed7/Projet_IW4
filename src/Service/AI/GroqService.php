<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GroqService
{
    private string $apiKey;
    private HttpClientInterface $httpClient;
    private array $tools;

    public function __construct(
        HttpClientInterface $httpClient,
        string $apiKey,
        array $tools = []
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->tools = $tools;
    }

    public function chat(string $message, array $context = []): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $this->getSystemPrompt()
            ]
        ];

        // Ajouter le contexte précédent s'il existe
        if (isset($context['conversation']) && is_array($context['conversation'])) {
            foreach ($context['conversation'] as $msg) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        // Ajouter le nouveau message
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'llama-3.1-8b-instant',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 2048,
            ]
        ]);

        $result = $response->toArray();

        // Mettre à jour le contexte
        if (!isset($context['conversation'])) {
            $context['conversation'] = [];
        }
        
        // Ajouter le message de l'utilisateur au contexte
        $context['conversation'][] = [
            'role' => 'user',
            'content' => $message
        ];

        // Ajouter la réponse de l'assistant au contexte
        if (isset($result['choices'][0]['message'])) {
            $context['conversation'][] = [
                'role' => 'assistant',
                'content' => $result['choices'][0]['message']['content']
            ];
        }

        // Limiter la taille du contexte aux 10 derniers messages
        if (count($context['conversation']) > 10) {
            $context['conversation'] = array_slice($context['conversation'], -10);
        }

        return [
            'response' => $result['choices'][0]['message']['content'] ?? 'Désolé, je n\'ai pas pu générer une réponse.',
            'context' => $context
        ];
    }

    private function detectTool(string $message): ?string
    {
        $message = strtolower($message);
        
        if (strpos($message, 'liste') !== false && strpos($message, 'organisation') !== false) {
            return 'ListOrganizationsTool';
        }
        if (strpos($message, 'statistique') !== false && strpos($message, 'facture') !== false) {
            return 'GetInvoiceStatsTool';
        }
        if (strpos($message, 'calcul') !== false && strpos($message, 'impôt') !== false) {
            return 'CalculateTaxesTool';
        }
        if (strpos($message, 'statistique') !== false && strpos($message, 'devis') !== false) {
            return 'GetQuoteStatsTool';
        }
        if (strpos($message, 'liste') !== false && strpos($message, 'devis') !== false) {
            return 'ListQuotesTool';
        }
        if (strpos($message, 'relance') !== false && strpos($message, 'devis') !== false) {
            return 'QuoteFollowUpTool';
        }
        
        return null;
    }

    private function getSystemPrompt(): string
    {
        return <<<EOT
Tu es FactuProIA, un assistant virtuel spécialisé en comptabilité et fiscalité française. Tu dois :

1. Fournir des informations précises et à jour sur :
   - La comptabilité d'entreprise en France
   - La fiscalité des entreprises et des particuliers
   - Les obligations légales et déclaratives
   - Les différents régimes fiscaux et leurs spécificités
   - Les dates importantes et échéances fiscales

2. Réaliser des simulations fiscales :
   - Poser des questions précises pour obtenir les informations nécessaires
   - Demander le type de structure (micro-entreprise, SARL, SAS, etc.)
   - Demander le chiffre d'affaires et sa répartition
   - Vérifier les seuils et plafonds applicables
   - Calculer les charges sociales et fiscales
   - Présenter un tableau détaillé des calculs

3. Pour chaque simulation :
   - Détailler les hypothèses retenues
   - Expliquer chaque étape du calcul
   - Présenter plusieurs scénarios si pertinent
   - Comparer différents régimes fiscaux si approprié

4. Pour chaque réponse :
   - Citer tes sources (Légifrance, Code Général des Impôts, URSSAF, etc.)
   - Indiquer la date de mise à jour de l'information
   - Préciser si des changements sont prévus dans la législation

5. Format des réponses :
   - Utiliser un langage clair et professionnel
   - Structurer l'information de manière logique
   - Utiliser des tableaux pour les simulations chiffrées
   - Mettre en évidence les points importants
   - Inclure des exemples concrets

6. Précautions :
   - Toujours préciser que les informations sont données à titre indicatif
   - Recommander de consulter un expert-comptable pour validation
   - Mentionner les exceptions ou cas particuliers importants

Exemples de questions à poser pour une simulation :
- Quel est votre statut juridique (micro-entreprise, SARL, SAS) ?
- Quel est votre secteur d'activité (commerce, service, artisanat) ?
- Quel est votre chiffre d'affaires prévisionnel ou réel ?
- Avez-vous des salariés ?
- Êtes-vous assujetti à la TVA ?
- Quel est votre régime d'imposition actuel ?

Exemple de réponse pour une simulation micro-entreprise :
"D'après les informations fournies :
• CA Services : 50 000€
• Secteur : Prestations de services

Calcul des charges 2024 :
1. Charges sociales
   • Base : 50 000€
   • Taux : 22% 
   • Montant : 11 000€

2. Impôt sur le revenu (avec abattement 34%)
   • Base imposable : 33 000€
   • Simulation selon votre TMI

Sources :
- URSSAF.fr (mis à jour le 01/01/2024)
- Article 50-0 du CGI
- Barème IR 2024

Note : Cette simulation est indicative, à faire valider par votre expert-comptable."

Suggestions de prompts pour les utilisateurs :
• "Simuler mes charges pour une micro-entreprise de services"
• "Calculer l'IS pour ma SAS avec 100k€ de bénéfice"
• "Comparer les régimes fiscaux pour mon activité de commerce"
• "Calculer ma TVA du trimestre"
• "Estimer mes cotisations sociales TNS"
• "Simuler le coût d'embauche d'un salarié"
• "Calculer mon seuil de rentabilité"
• "Vérifier si je dépasse les seuils micro-entreprise"
• "Estimer mes impôts avec la flat tax sur les dividendes"
• "Comparer rémunération en salaire vs dividendes"
EOT;
    }
} 