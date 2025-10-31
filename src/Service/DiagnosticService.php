<?php

namespace App\Service;

use App\Entity\DiagnosticQuestion;
use App\Entity\DiagnosticTestSession;
use App\Entity\User;
use App\Repository\DiagnosticQuestionRepository;
use App\Repository\DiagnosticTestSessionRepository;
use Doctrine\ORM\EntityManagerInterface;

class DiagnosticService
{
    public function __construct(
        private DiagnosticQuestionRepository $questionRepository,
        private DiagnosticTestSessionRepository $sessionRepository,
        private EntityManagerInterface $entityManager,
        private GrokService $grokService
    ) {}

    /**
     * Récupère toutes les questions actives groupées par catégorie
     */
    public function getQuestionsGroupedByCategory(): array
    {
        $questions = $this->questionRepository->findAllGroupedByCategory();
        $result = [];

        foreach ($questions as $category => $categoryQuestions) {
            $result[$category] = array_map(function (DiagnosticQuestion $q) {
                return [
                    'id' => $q->getId(),
                    'questionText' => $q->getQuestionText(),
                    'questionTextEn' => $q->getQuestionTextEn(),
                    'options' => $q->getOptions(),
                    'type' => $q->getType(),
                    'orderIndex' => $q->getOrderIndex(),
                    'description' => $q->getDescription(),
                    'isRequired' => $q->isRequired(),
                ];
            }, $categoryQuestions);
        }

        return $result;
    }

    /**
     * Crée ou récupère une session de test active pour un utilisateur
     */
    public function getOrCreateSession(User $user): DiagnosticTestSession
    {
        $session = $this->sessionRepository->findActiveSessionByUser($user);
        
        if (!$session) {
            $session = new DiagnosticTestSession();
            $session->setUser($user);
            $this->entityManager->persist($session);
            $this->entityManager->flush();
        }

        return $session;
    }

    /**
     * Sauvegarde une réponse à une question
     */
    public function saveAnswer(DiagnosticTestSession $session, int $questionId, $answer): void
    {
        $session->addAnswer($questionId, $answer);
        $session->setCurrentQuestionIndex($session->getCurrentQuestionIndex() + 1);
        
        $this->sessionRepository->getEntityManager()->flush();
    }

    /**
     * Calcule les scores par catégorie basés sur les réponses
     */
    public function calculateScores(DiagnosticTestSession $session): array
    {
        $answers = $session->getAnswers();
        $questions = $this->questionRepository->findAllActive();
        
        $categoryScores = [];
        $categoryTotals = [];

        foreach ($questions as $question) {
            $questionId = $question->getId();
            $category = $question->getCategory();
            
            if (!isset($answers[$questionId])) {
                continue;
            }

            if (!isset($categoryScores[$category])) {
                $categoryScores[$category] = 0;
                $categoryTotals[$category] = 0;
            }

            $answer = $answers[$questionId];
            $score = $this->calculateQuestionScore($question, $answer);
            
            $categoryScores[$category] += $score;
            $categoryTotals[$category] += 1;
        }

        // Normaliser les scores (0-100)
        $normalizedScores = [];
        foreach ($categoryScores as $category => $score) {
            $normalizedScores[$category] = $categoryTotals[$category] > 0 
                ? round(($score / ($categoryTotals[$category] * 10)) * 100, 2)
                : 0;
        }

        $session->setScores($normalizedScores);
        $this->entityManager->flush();

        return $normalizedScores;
    }

    /**
     * Calcule le score pour une question/réponse
     */
    private function calculateQuestionScore(DiagnosticQuestion $question, $answer): int
    {
        $options = $question->getOptions();
        $type = $question->getType();

        if ($type === 'select') {
            // Chercher l'option correspondante et récupérer son score
            foreach ($options as $option) {
                if (isset($option['value']) && $option['value'] === $answer) {
                    return $option['score'] ?? 5; // Score par défaut
                }
            }
        } elseif ($type === 'multiselect') {
            // Score = moyenne des scores des options sélectionnées
            if (!is_array($answer)) {
                return 0;
            }
            $totalScore = 0;
            $count = 0;
            foreach ($options as $option) {
                if (isset($option['value']) && in_array($option['value'], $answer)) {
                    $totalScore += $option['score'] ?? 5;
                    $count++;
                }
            }
            return $count > 0 ? round($totalScore / $count) : 0;
        } elseif ($type === 'scale') {
            // Pour les échelles (1-10), le score est directement la valeur
            return is_numeric($answer) ? (int)$answer : 5;
        }

        return 5; // Score par défaut
    }

    /**
     * Génère le diagnostic final avec E-DVISOR
     */
    public function generateDiagnostic(DiagnosticTestSession $session): string
    {
        $scores = $session->getScores() ?? [];
        $answers = $session->getAnswers();
        
        // Récupérer les questions pour avoir le contexte
        $questions = $this->questionRepository->findAllActive();
        $questionMap = [];
        foreach ($questions as $q) {
            $questionMap[$q->getId()] = [
                'text' => $q->getQuestionText(),
                'category' => $q->getCategory(),
            ];
        }

        // Construire le contexte pour Grok
        $context = [
            'scores' => $scores,
            'answers' => $this->formatAnswersForGrok($answers, $questionMap),
            'categories' => array_keys($scores),
        ];

        // Créer le prompt pour Grok
        $prompt = $this->buildDiagnosticPrompt($scores, $answers, $questionMap);

        // Appeler Grok
        $messages = [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        $diagnostic = $this->grokService->chat($messages, $context);

        $session->setDiagnosticResult($diagnostic);
        $session->setStatus('completed');
        $this->entityManager->flush();

        return $diagnostic;
    }

    /**
     * Formate les réponses pour Grok
     */
    private function formatAnswersForGrok(array $answers, array $questionMap): array
    {
        $formatted = [];
        
        foreach ($answers as $questionId => $answer) {
            if (!isset($questionMap[$questionId])) {
                continue;
            }
            
            $formatted[] = [
                'category' => $questionMap[$questionId]['category'],
                'question' => $questionMap[$questionId]['text'],
                'answer' => $answer,
            ];
        }

        return $formatted;
    }

    /**
     * Construit le prompt pour générer le diagnostic
     */
    private function buildDiagnosticPrompt(array $scores, array $answers, array $questionMap): string
    {
        $prompt = <<<'PROMPT'
Tu es E-DVISOR, un assistant IA d'orientation scolaire pour E-TAWJIHI.
Un étudiant vient de compléter un test de diagnostic complet avec plus de 50 questions couvrant plusieurs aspects : académique, carrière, personnalité, compétences, et préférences.

Tâche : Analyse les résultats et génère un diagnostic complet et personnalisé.

STRUCTURE DU DIAGNOSTIC (à suivre) :
1. **Résumé exécutif** (2-3 phrases) : Vue d'ensemble du profil
2. **Points forts identifiés** : 3-5 points avec astérisques pour l'emphase
3. **Axes d'amélioration** : 2-3 suggestions constructives
4. **Orientation recommandée** : Suggestions de domaines/pays selon les résultats
5. **Plan d'action** : 3-4 étapes concrètes avec astérisques

RÈGLES :
- Sois TRÈS DÉTAILLÉ et COMPLET (c'est un diagnostic final, pas une réponse courte)
- Utilise les astérisques pour mettre en gras : *texte* = texte en gras
- Base-toi sur les scores par catégorie et les réponses détaillées
- Sois bienveillant et encourageant
- Réponds en français

PROMPT;

        // Ajouter les scores par catégorie
        $prompt .= "\n\n=== SCORES PAR CATÉGORIE ===\n";
        foreach ($scores as $category => $score) {
            $prompt .= "- {$category}: {$score}/100\n";
        }

        // Ajouter un résumé des réponses principales
        $prompt .= "\n\n=== RÉPONSES CLÉS ===\n";
        $sampleAnswers = array_slice($answers, 0, 10); // Premières 10 réponses comme contexte
        foreach ($sampleAnswers as $questionId => $answer) {
            if (isset($questionMap[$questionId])) {
                $prompt .= "Q: {$questionMap[$questionId]['text']}\n";
                $prompt .= "R: " . (is_array($answer) ? implode(', ', $answer) : $answer) . "\n\n";
            }
        }

        $prompt .= "\nGénère maintenant le diagnostic complet selon la structure demandée.";

        return $prompt;
    }
}

