<?php

namespace App\Controller;

use App\Service\DiagnosticService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/diagnostic')]
#[IsGranted('ROLE_USER')]
class DiagnosticController extends AbstractController
{
    public function __construct(
        private DiagnosticService $diagnosticService,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Récupère toutes les questions groupées par catégorie
     */
    #[Route('/questions', name: 'diagnostic_questions', methods: ['GET'])]
    public function getQuestions(): JsonResponse
    {
        try {
            $questions = $this->diagnosticService->getQuestionsGroupedByCategory();
            
            return new JsonResponse([
                'success' => true,
                'data' => $questions,
                'total' => array_sum(array_map('count', $questions))
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des questions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère ou crée une session de test
     */
    #[Route('/session', name: 'diagnostic_session', methods: ['GET'])]
    public function getSession(): JsonResponse
    {
        try {
            $user = $this->getUser();
            $session = $this->diagnosticService->getOrCreateSession($user);

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'id' => $session->getId(),
                    'status' => $session->getStatus(),
                    'currentQuestionIndex' => $session->getCurrentQuestionIndex(),
                    'answers' => $session->getAnswers(),
                    'startedAt' => $session->getStartedAt()?->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la session',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sauvegarde une réponse
     */
    #[Route('/answer', name: 'diagnostic_answer', methods: ['POST'])]
    public function saveAnswer(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $questionId = $data['questionId'] ?? null;
            $answer = $data['answer'] ?? null;

            if (!$questionId || $answer === null) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'questionId et answer sont requis'
                ], 400);
            }

            $user = $this->getUser();
            $session = $this->diagnosticService->getOrCreateSession($user);
            $this->diagnosticService->saveAnswer($session, (int)$questionId, $answer);

            return new JsonResponse([
                'success' => true,
                'message' => 'Réponse sauvegardée',
                'data' => [
                    'currentQuestionIndex' => $session->getCurrentQuestionIndex(),
                    'totalAnswers' => count($session->getAnswers()),
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule les scores et génère le diagnostic final
     */
    #[Route('/generate', name: 'diagnostic_generate', methods: ['POST'])]
    public function generateDiagnostic(): JsonResponse
    {
        try {
            $user = $this->getUser();
            $session = $this->diagnosticService->getOrCreateSession($user);

            // Vérifier qu'il y a des réponses
            if (empty($session->getAnswers())) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Aucune réponse trouvée. Complétez d\'abord le test.'
                ], 400);
            }

            // Calculer les scores
            $scores = $this->diagnosticService->calculateScores($session);

            // Générer le diagnostic avec E-DVISOR
            $diagnostic = $this->diagnosticService->generateDiagnostic($session);

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'diagnostic' => $diagnostic,
                    'scores' => $scores,
                    'sessionId' => $session->getId(),
                    'completedAt' => $session->getCompletedAt()?->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la génération du diagnostic',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère le diagnostic d'une session complétée
     */
    #[Route('/result/{sessionId}', name: 'diagnostic_result', methods: ['GET'])]
    public function getResult(int $sessionId): JsonResponse
    {
        try {
            $user = $this->getUser();
            $session = $this->entityManager->getRepository(\App\Entity\DiagnosticTestSession::class)->find($sessionId);

            if (!$session || $session->getUser() !== $user) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Session non trouvée'
                ], 404);
            }

            if ($session->getStatus() !== 'completed') {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le test n\'est pas encore complété'
                ], 400);
            }

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'diagnostic' => $session->getDiagnosticResult(),
                    'scores' => $session->getScores(),
                    'completedAt' => $session->getCompletedAt()?->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération du résultat',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

