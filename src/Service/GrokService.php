<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GrokService
{
    private const API_BASE_URL = 'https://api.x.ai/v1';
    
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {}
    
    /**
     * Get API key from environment or parameters
     */
    private function getApiKey(): ?string
    {
        try {
            return $this->parameterBag->get('app.grok.api_key');
        } catch (\Exception $e) {
            // Fallback to env var directly
            return $_ENV['GROK_API_KEY'] ?? null;
        }
    }

    /**
     * Envoie un message à Grok et retourne la réponse
     */
    public function chat(array $messages, array $context = []): ?string
    {
        $apiKey = $this->getApiKey();
        
        if (!$apiKey) {
            error_log('Grok API Error: API key not configured');
            throw new \RuntimeException('Grok API key not configured. Set GROK_API_KEY in .env');
        }
        
        // Vérifier que la clé n'est pas vide
        if (empty(trim($apiKey))) {
            error_log('Grok API Error: API key is empty');
            throw new \RuntimeException('Grok API key is empty');
        }

        // Construire le système prompt avec contexte E-DVISOR
        $systemPrompt = $this->buildSystemPrompt($context);
        
        // Modèles Grok possibles (xAI)
        // grok-beta était déprécié le 2025-09-15
        // Modèles disponibles: grok-4-fast-reasoning (recommandé), grok-4-fast-non-reasoning, grok-3, grok-3-mini, etc.
        $model = $_ENV['GROK_MODEL'] ?? 'grok-4-fast-reasoning';
        
        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ...$messages
            ],
            'temperature' => 0.7,
            'max_tokens' => 300, // Limiter à 300 tokens pour des réponses courtes
        ];
        
        // Log pour debug (sans exposer la clé API)
        error_log('Grok API Request - Model: ' . $payload['model']);
        error_log('Grok API Request - Messages count: ' . count($payload['messages']));
        error_log('Grok API Request - URL: ' . self::API_BASE_URL . '/chat/completions');

        try {
            $ch = curl_init(self::API_BASE_URL . '/chat/completions');
            
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                error_log('Grok API cURL Error: ' . $error);
                throw new \RuntimeException('Erreur réseau cURL: ' . $error);
            }
            
            if ($httpCode !== 200) {
                error_log('Grok API HTTP Error: Code ' . $httpCode);
                error_log('Grok API Response: ' . substr($response, 0, 1000)); // Limiter la taille
                
                // Essayer de parser l'erreur pour la retourner plus utile
                $errorData = json_decode($response, true);
                $errorMessage = null;
                
                if ($errorData) {
                    if (isset($errorData['error']['message'])) {
                        $errorMessage = $errorData['error']['message'];
                        error_log('Grok API Error Message: ' . $errorMessage);
                    } elseif (isset($errorData['message'])) {
                        $errorMessage = $errorData['message'];
                        error_log('Grok API Message: ' . $errorMessage);
                    }
                }
                
                // Lancer une exception avec le message d'erreur utile
                throw new \RuntimeException($errorMessage ?? "HTTP Error $httpCode: " . substr($response, 0, 200));
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('Grok API JSON Error: ' . json_last_error_msg());
                error_log('Raw Response: ' . substr($response, 0, 500));
                throw new \RuntimeException('Réponse JSON invalide: ' . json_last_error_msg());
            }
            
            // Vérifier la structure de la réponse
            if (!isset($data['choices']) || empty($data['choices'])) {
                error_log('Grok API: No choices in response');
                error_log('Response structure: ' . json_encode(array_keys($data)));
                error_log('Full response (first 500 chars): ' . substr(json_encode($data), 0, 500));
                throw new \RuntimeException('Réponse Grok invalide: pas de choix dans la réponse');
            }
            
            $content = $data['choices'][0]['message']['content'] ?? null;
            
            if (!$content) {
                error_log('Grok API: Empty content in response');
                error_log('First choice structure: ' . json_encode($data['choices'][0] ?? []));
                throw new \RuntimeException('Réponse Grok invalide: contenu vide');
            }
            
            return $content;
        } catch (\RuntimeException $e) {
            // Ré-exécuter les RuntimeExceptions pour qu'elles remontent
            error_log('Grok API RuntimeException: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            error_log('Grok API Error: ' . $e->getMessage());
            error_log('Grok API Trace: ' . $e->getTraceAsString());
            throw new \RuntimeException('Erreur lors de l\'appel à l\'API Grok: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Analyse les réponses de l'utilisateur et génère des recommandations personnalisées
     */
    public function analyzeUserProfile(array $answers, string $stage = 'general'): array
    {
        $context = [
            'stage' => $stage,
            'answers' => $answers,
        ];

        $prompt = match($stage) {
            'step1' => "Analyse le profil de base de l'utilisateur et donne des conseils d'orientation basés sur : niveau d'étude, filière, pays de résidence.",
            'step2' => "Analyse les préférences de destination d'études et recommande le meilleur choix entre Maroc, France et Chine selon le profil.",
            'step3_france' => "Fournis des conseils spécifiques pour étudier en France : budget, dossier Campus France, Parcoursup.",
            'step3_china' => "Fournis des conseils spécifiques pour étudier en Chine : bourses CSC, procédures, budget minimum.",
            'step3_morocco' => "Fournis des conseils spécifiques pour étudier au Maroc : écoles publiques vs privées, budget.",
            'step4' => "Donne des conseils finaux et une roadmap personnalisée selon les réponses complètes.",
            default => "Analyse les réponses et donne des recommandations personnalisées."
        };

        $messages = [
            [
                'role' => 'user',
                'content' => $prompt . "\n\nRéponses utilisateur:\n" . json_encode($answers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]
        ];

        $response = $this->chat($messages, $context);
        
        return [
            'recommendations' => $response ?? 'Analyse en cours...',
            'stage' => $stage,
        ];
    }

    /**
     * Répond à une question ouverte de l'utilisateur dans le chat
     */
    public function answerUserQuestion(string $question, array $conversationHistory = [], array $userProfile = []): string
    {
        $context = [
            'conversation_history' => $conversationHistory,
            'user_profile' => $userProfile,
        ];

        $messages = [];
        
        // Ajouter l'historique de conversation
        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? ''
            ];
        }

        // Ajouter la question actuelle
        $messages[] = [
            'role' => 'user',
            'content' => $question
        ];

        try {
            $response = $this->chat($messages, $context);
            
            if (!$response) {
                error_log('Grok answerUserQuestion: Empty response from chat()');
                return "Désolé, je n'ai pas pu traiter ta question pour le moment. Vérifiez votre connexion et réessayez.";
            }
            
            return $response;
        } catch (\Exception $e) {
            error_log('Grok answerUserQuestion Error: ' . $e->getMessage());
            return "Désolé, une erreur est survenue: " . $e->getMessage();
        }
    }

    /**
     * Construit le prompt système pour Grok
     */
    private function buildSystemPrompt(array $context): string
    {
        $basePrompt = <<<'PROMPT'
Tu es E-DVISOR, un assistant IA d'orientation scolaire pour E-TAWJIHI.
Tu aides les étudiants à choisir entre étudier au Maroc, en France ou en Chine.
Tu es bienveillant, professionnel et tu donnes des conseils pratiques et réalistes.

Contexte E-TAWJIHI :
- Plateforme d'accompagnement pour études au Maroc, France et Chine
- Services : Campus France, Parcoursup, procédures CSC (Chine), écoles marocaines
- Accompagnement personnalisé et payant pour maximiser les chances d'admission

Règles IMPORTANTES :
1. Sois TRÈS CONCIS : maximum 2-3 phrases par réponse. Évite les longues explications.
2. Utilise les astérisques pour mettre en gras : *texte* = texte en gras (comme WhatsApp)
3. Adapte tes réponses selon le pays choisi (FR/CN/MA)
4. Mentionne les budgets minimums quand pertinent (en gras avec *)
5. Guide vers les services E-TAWJIHI quand approprié
6. Réponds toujours en français (sauf demande contraire)
7. Structure tes réponses avec des points courts et des astérisques pour l'emphase
8. Si l'utilisateur demande des établissements, universités ou écoles, suggère une recherche d'établissements en mentionnant le pays ou le domaine (ex: "Je peux te chercher des universités en France pour l'informatique")
9. Si l'utilisateur demande des programmes, formations, cursus, master, licence ou bachelor, suggère une recherche de programmes en mentionnant le pays, le domaine ou le niveau (ex: "Je peux te chercher des masters en informatique en France")
10. Si l'utilisateur demande un test, diagnostic, évaluation complète ou analyse de profil, propose-lui de commencer le test de diagnostic E-DVISOR avec plus de 50 questions

PROMPT;

        // Ajouter contexte spécifique selon l'étape
        if (isset($context['stage'])) {
            $stageContext = match($context['stage']) {
                'step3_france' => "\n\nContexte actuel : L'utilisateur explore les études en France. Parle de Campus France, Parcoursup, budget minimum 8000€/an.",
                'step3_china' => "\n\nContexte actuel : L'utilisateur explore les études en Chine. Parle de bourses CSC, procédure CSCA, budget minimum 30000 Dhs/an.",
                'step3_morocco' => "\n\nContexte actuel : L'utilisateur explore les études au Maroc. Parle des 75 écoles publiques, écoles privées, budgets variables.",
                default => ''
            };
            $basePrompt .= $stageContext;
        }

        return $basePrompt;
    }
}

