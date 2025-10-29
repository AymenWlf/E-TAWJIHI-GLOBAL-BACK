<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Program;
use App\Entity\User;
use App\Repository\ApplicationRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/applications', name: 'api_applications_')]
#[IsGranted('ROLE_USER')]
class ApplicationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ApplicationRepository $applicationRepository,
        private ProgramRepository $programRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $applications = $this->applicationRepository->findByUser($user);

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($applications, 'json', ['groups' => ['application:list']]), true)
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $application = $this->applicationRepository->findByIdAndUser($id, $user);

        if (!$application) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($application, 'json', ['groups' => ['application:read']]), true)
        ]);
    }

    #[Route('/program/{programId}', name: 'create_or_get', methods: ['POST'])]
    public function createOrGet(int $programId, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $program = $this->programRepository->find($programId);
        if (!$program) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Program not found'
            ], 404);
        }

        // Check if there's already an active application for this program
        $existingApplication = $this->applicationRepository->findActiveByUserAndProgram($user, $program);

        if ($existingApplication) {
            return new JsonResponse([
                'success' => true,
                'data' => json_decode($this->serializer->serialize($existingApplication, 'json', ['groups' => ['application:read']]), true),
                'isExisting' => true
            ]);
        }

        // Create new application
        $application = new Application();
        $application->setUser($user);
        $application->setProgram($program);

        // Set language from request or user preference
        $requestData = json_decode($request->getContent(), true);
        $language = $requestData['language'] ?? $user->getPreferredLanguage() ?? 'en';

        // Store language in applicationData
        $applicationData = [
            'language' => $language,
            'personalInfo' => [],
            'academicInfo' => [],
            'documents' => [],
            'preferences' => [],
            'qualifications' => [],
            'preAdmission' => [],
            'enrollment' => [],
            'finalOffer' => [],
            'visaApplication' => [],
            'enroll' => []
        ];
        $application->setApplicationData($applicationData);

        $this->entityManager->persist($application);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($application, 'json', ['groups' => ['application:read']]), true),
            'isExisting' => false
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $application = $this->applicationRepository->findByIdAndUser($id, $user);

        if (!$application) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $requestData = json_decode($request->getContent(), true);

        // Get current application data
        $applicationData = $application->getApplicationData() ?? [];

        // Update application data fields
        if (isset($requestData['personalInfo'])) {
            $applicationData['personalInfo'] = $requestData['personalInfo'];
        }

        if (isset($requestData['academicInfo'])) {
            $applicationData['academicInfo'] = $requestData['academicInfo'];
        }

        if (isset($requestData['documents'])) {
            $applicationData['documents'] = $requestData['documents'];
        }

        if (isset($requestData['preferences'])) {
            $applicationData['preferences'] = $requestData['preferences'];
        }

        if (isset($requestData['preAdmission'])) {
            $applicationData['preAdmission'] = $requestData['preAdmission'];
        }

        if (isset($requestData['enrollment'])) {
            $applicationData['enrollment'] = $requestData['enrollment'];
        }

        if (isset($requestData['finalOffer'])) {
            $applicationData['finalOffer'] = $requestData['finalOffer'];
        }

        if (isset($requestData['visaApplication'])) {
            $applicationData['visaApplication'] = $requestData['visaApplication'];
        }

        if (isset($requestData['enroll'])) {
            $applicationData['enroll'] = $requestData['enroll'];
        }

        if (isset($requestData['language'])) {
            $applicationData['language'] = $requestData['language'];
        }

        // Update application data
        $application->setApplicationData($applicationData);

        // Update status
        if (isset($requestData['status'])) {
            $application->setStatus($requestData['status']);

            // Set submitted date and duplicate data if status is submitted
            if ($requestData['status'] === 'submitted' && !$application->getSubmittedAt()) {
                // Duplicate applicationData to submittedData (excluding documents and qualifications)
                $currentApplicationData = $application->getApplicationData() ?? [];
                $submittedData = $currentApplicationData;

                // Remove documents and qualifications from submittedData
                unset($submittedData['documents']);
                unset($submittedData['qualifications']);

                $application->setSubmittedData($submittedData);
                $application->setSubmittedAt(new \DateTimeImmutable());
            }
        }

        $application->updateTimestamp();
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($application, 'json', ['groups' => ['application:read']]), true)
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $application = $this->applicationRepository->findByIdAndUser($id, $user);

        if (!$application) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        // Only allow deletion of draft applications
        if ($application->getStatus() !== 'draft') {
            return new JsonResponse([
                'success' => false,
                'message' => 'Only draft applications can be deleted'
            ], 400);
        }

        $this->entityManager->remove($application);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Application deleted successfully'
        ]);
    }

    #[Route('/check/{programId}', name: 'check', methods: ['GET'])]
    public function check(int $programId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $program = $this->programRepository->find($programId);
        if (!$program) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Program not found'
            ], 404);
        }

        $existingApplication = $this->applicationRepository->findActiveByUserAndProgram($user, $program);

        return new JsonResponse([
            'success' => true,
            'hasActiveApplication' => $existingApplication !== null,
            'application' => $existingApplication ? json_decode($this->serializer->serialize($existingApplication, 'json', ['groups' => ['application:read']]), true) : null
        ]);
    }
}
