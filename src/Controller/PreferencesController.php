<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Preferences;
use App\Repository\PreferencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/preferences', name: 'app_preferences_')]
#[IsGranted('ROLE_USER')]
class PreferencesController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PreferencesRepository $preferencesRepository
    ) {}

    #[Route('', name: 'get', methods: ['GET'])]
    public function getPreferences(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $preferences = $user->getPreferences();
        if (!$preferences) {
            // Create default preferences if they don't exist
            $preferences = $this->createDefaultPreferences($user);
        }

        $data = $this->serializePreferences($preferences);
        return new JsonResponse($data);
    }

    #[Route('', name: 'update', methods: ['PUT'])]
    public function updatePreferences(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $preferences = $user->getPreferences();
        if (!$preferences) {
            $preferences = $this->createDefaultPreferences($user);
        }

        $data = json_decode($request->getContent(), true);

        // Update preferences
        if (isset($data['preferredDestinations'])) {
            $preferences->setPreferredDestinations($data['preferredDestinations']);
        }
        if (isset($data['preferredStudyLevel'])) {
            $preferences->setPreferredStudyLevel($data['preferredStudyLevel']);
        }
        if (isset($data['preferredDegree'])) {
            $preferences->setPreferredDegree($data['preferredDegree']);
        }
        if (isset($data['preferredIntakes'])) {
            $preferences->setPreferredIntakes($data['preferredIntakes']);
        }
        if (isset($data['preferredSubjects'])) {
            $preferences->setPreferredSubjects($data['preferredSubjects']);
        }
        if (isset($data['preferredCurrency'])) {
            $preferences->setPreferredCurrency($data['preferredCurrency']);
        }
        if (isset($data['annualBudget'])) {
            $preferences->setAnnualBudget($data['annualBudget']);
        }
        if (isset($data['scholarshipRequired'])) {
            $preferences->setScholarshipRequired($data['scholarshipRequired']);
        }
        if (isset($data['preferredTeachingLanguage'])) {
            $preferences->setPreferredTeachingLanguage($data['preferredTeachingLanguage']);
        }
        if (isset($data['mainPriority'])) {
            $preferences->setMainPriority($data['mainPriority']);
        }
        if (isset($data['scholarshipSearch'])) {
            $preferences->setScholarshipSearch($data['scholarshipSearch']);
        }
        if (isset($data['englishTest'])) {
            $preferences->setEnglishTest($data['englishTest']);
        }
        if (isset($data['frenchTest'])) {
            $preferences->setFrenchTest($data['frenchTest']);
        }

        $preferences->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $data = $this->serializePreferences($preferences);
        return new JsonResponse($data);
    }

    private function createDefaultPreferences(User $user): Preferences
    {
        $preferences = new Preferences();
        $preferences->setUser($user);
        $preferences->setPreferredDestinations([]);
        $preferences->setPreferredStudyLevel('');
        $preferences->setPreferredDegree('');
        $preferences->setPreferredIntakes([]);
        $preferences->setPreferredSubjects([]);
        $preferences->setPreferredCurrency('USD');
        $preferences->setAnnualBudget([]);
        $preferences->setScholarshipRequired(false);
        $preferences->setPreferredTeachingLanguage('');
        $preferences->setMainPriority('');
        $preferences->setScholarshipSearch(false);
        $preferences->setEnglishTest('none');
        $preferences->setFrenchTest('none');

        $this->entityManager->persist($preferences);
        $this->entityManager->flush();

        return $preferences;
    }

    private function serializePreferences(Preferences $preferences): array
    {
        return [
            'id' => $preferences->getId(),
            'preferredDestinations' => $preferences->getPreferredDestinations(),
            'preferredStudyLevel' => $preferences->getPreferredStudyLevel(),
            'preferredDegree' => $preferences->getPreferredDegree(),
            'preferredIntakes' => $preferences->getPreferredIntakes(),
            'preferredSubjects' => $preferences->getPreferredSubjects(),
            'preferredCurrency' => $preferences->getPreferredCurrency(),
            'annualBudget' => $preferences->getAnnualBudget(),
            'scholarshipRequired' => $preferences->isScholarshipRequired() === true ? true : false,
            'preferredTeachingLanguage' => $preferences->getPreferredTeachingLanguage(),
            'mainPriority' => $preferences->getMainPriority(),
            'scholarshipSearch' => $preferences->isScholarshipSearch() === true ? true : false,
            'englishTest' => $preferences->getEnglishTest() ?: 'none',
            'frenchTest' => $preferences->getFrenchTest() ?: 'none',
            'createdAt' => $preferences->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $preferences->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
