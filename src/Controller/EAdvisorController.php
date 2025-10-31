<?php

namespace App\Controller;

use App\Service\GrokService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/eadvisor')]
class EAdvisorController extends AbstractController
{
    public function __construct(
        private GrokService $grokService
    ) {}

    /**
     * Répond à une question ouverte de l'utilisateur
     */
    #[Route('/chat', name: 'eadvisor_chat', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $question = $data['question'] ?? null;
        $conversationHistory = $data['conversationHistory'] ?? [];
        $userProfile = $data['userProfile'] ?? [];

        if (!$question) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Question requise'
            ], 400);
        }

        try {
            $response = $this->grokService->answerUserQuestion($question, $conversationHistory, $userProfile);
            
            if (!$response || strpos($response, "Désolé, je n'ai pas pu traiter") === 0) {
                // Erreur dans le service, retourner les détails
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors du traitement de la question',
                    'response' => $response,
                    'error' => 'La réponse de Grok est vide ou en erreur. Vérifiez les logs serveur.'
                ], 500);
            }
            
            return new JsonResponse([
                'success' => true,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            error_log('EAdvisor Chat Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors du traitement de la question',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyse le profil utilisateur et génère des recommandations
     */
    #[Route('/analyze', name: 'eadvisor_analyze', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $answers = $data['answers'] ?? [];
        $stage = $data['stage'] ?? 'general';

        try {
            $analysis = $this->grokService->analyzeUserProfile($answers, $stage);
            
            return new JsonResponse([
                'success' => true,
                'data' => $analysis,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère une suggestion après chaque étape
     */
    #[Route('/suggest', name: 'eadvisor_suggest', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function suggest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $answers = $data['answers'] ?? [];
        $currentStep = $data['currentStep'] ?? '';
        $nextQuestionId = $data['nextQuestionId'] ?? null;

        try {
            // Construire le prompt selon l'étape actuelle
            $context = [
                'currentStep' => $currentStep,
                'nextQuestionId' => $nextQuestionId,
                'answers' => $answers,
            ];

            $messages = [
                [
                    'role' => 'user',
                    'content' => "Génère une suggestion ou un encouragement court (2-3 phrases max) après cette étape: {$currentStep}. Réponses utilisateur:\n" . json_encode($answers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                ]
            ];

            $suggestion = $this->grokService->chat($messages, $context);
            
            return new JsonResponse([
                'success' => true,
                'suggestion' => $suggestion ?? '',
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la génération de suggestion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

