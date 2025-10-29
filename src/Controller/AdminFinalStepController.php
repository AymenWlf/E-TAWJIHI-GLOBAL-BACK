<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\FinalStep;
use App\Entity\FinalStepDocument;
use App\Repository\FinalStepRepository;
use App\Repository\FinalStepDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/api/admin/final-steps')]
class AdminFinalStepController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FinalStepRepository $finalStepRepository,
        private FinalStepDocumentRepository $finalStepDocumentRepository
    ) {}

    #[Route('', name: 'admin_get_final_steps', methods: ['GET'])]
    public function getFinalSteps(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here
        // if (!in_array('ROLE_ADMIN', $user->getRoles())) {
        //     return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        // }

        $finalSteps = $this->finalStepRepository->findAll();

        $result = [];
        foreach ($finalSteps as $step) {
            $documents = $this->finalStepDocumentRepository->findBy(['finalStep' => $step]);

            $stepData = [
                'id' => $step->getId(),
                'name' => $step->getName(),
                'nameEn' => $step->getNameEn(),
                'nameFr' => $step->getNameFr(),
                'description' => $step->getDescription(),
                'descriptionEn' => $step->getDescriptionEn(),
                'descriptionFr' => $step->getDescriptionFr(),
                'stepOrder' => $step->getStepOrder(),
                'isActive' => $step->getIsActive(),
                'createdAt' => $step->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updatedAt' => $step->getUpdatedAt()?->format('Y-m-d H:i:s'),
                'documents' => []
            ];

            foreach ($documents as $document) {
                $stepData['documents'][] = [
                    'id' => $document->getId(),
                    'title' => $document->getTitle(),
                    'titleEn' => $document->getTitleEn(),
                    'titleFr' => $document->getTitleFr(),
                    'filePath' => $document->getFilePath(),
                    'fileType' => $document->getFileType(),
                    'fileSize' => $document->getFileSize(),
                    'isActive' => $document->getIsActive(),
                    'createdAt' => $document->getCreatedAt()?->format('Y-m-d H:i:s'),
                    'updatedAt' => $document->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }

            $result[] = $stepData;
        }

        return new JsonResponse($result);
    }

    #[Route('', name: 'admin_create_final_step', methods: ['POST'])]
    public function createFinalStep(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $data = json_decode($request->getContent(), true);

        $step = new FinalStep();
        $step->setName($data['name'] ?? '');
        $step->setNameEn($data['nameEn'] ?? $data['name'] ?? '');
        $step->setNameFr($data['nameFr'] ?? $data['name'] ?? '');
        $step->setDescription($data['description'] ?? '');
        $step->setDescriptionEn($data['descriptionEn'] ?? $data['description'] ?? '');
        $step->setDescriptionFr($data['descriptionFr'] ?? $data['description'] ?? '');
        $step->setStepOrder($data['stepOrder'] ?? 0);
        $step->setIsActive($data['isActive'] ?? true);

        $this->entityManager->persist($step);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'id' => $step->getId(),
            'message' => 'Final step created successfully'
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'admin_update_final_step', methods: ['PUT'])]
    public function updateFinalStep(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $step = $this->finalStepRepository->find($id);
        if (!$step) {
            return new JsonResponse(['error' => 'Final step not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $step->setName($data['name']);
        if (isset($data['nameEn'])) $step->setNameEn($data['nameEn']);
        if (isset($data['nameFr'])) $step->setNameFr($data['nameFr']);
        if (isset($data['description'])) $step->setDescription($data['description']);
        if (isset($data['descriptionEn'])) $step->setDescriptionEn($data['descriptionEn']);
        if (isset($data['descriptionFr'])) $step->setDescriptionFr($data['descriptionFr']);
        if (isset($data['stepOrder'])) $step->setStepOrder($data['stepOrder']);
        if (isset($data['isActive'])) $step->setIsActive($data['isActive']);

        $step->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Final step updated successfully'
        ]);
    }

    #[Route('/{id}', name: 'admin_delete_final_step', methods: ['DELETE'])]
    public function deleteFinalStep(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $step = $this->finalStepRepository->find($id);
        if (!$step) {
            return new JsonResponse(['error' => 'Final step not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($step);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Final step deleted successfully'
        ]);
    }

    #[Route('/{stepId}/documents', name: 'admin_get_final_step_documents', methods: ['GET'])]
    public function getDocuments(int $stepId): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $step = $this->finalStepRepository->find($stepId);
        if (!$step) {
            return new JsonResponse(['error' => 'Final step not found'], Response::HTTP_NOT_FOUND);
        }

        $documents = $this->finalStepDocumentRepository->findBy(['finalStep' => $step], ['id' => 'ASC']);

        $result = [];
        foreach ($documents as $document) {
            $result[] = [
                'id' => $document->getId(),
                'title' => $document->getTitle(),
                'titleEn' => $document->getTitleEn(),
                'titleFr' => $document->getTitleFr(),
                'filePath' => $document->getFilePath(),
                'fileType' => $document->getFileType(),
                'fileSize' => $document->getFileSize(),
                'isActive' => $document->getIsActive(),
                'createdAt' => $document->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updatedAt' => $document->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('/{stepId}/documents', name: 'admin_create_final_step_document', methods: ['POST'])]
    public function createDocument(int $stepId, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $step = $this->finalStepRepository->find($stepId);
        if (!$step) {
            return new JsonResponse(['error' => 'Final step not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $document = new FinalStepDocument();
        $document->setFinalStep($step);
        $document->setTitle($data['title'] ?? '');
        $document->setTitleEn($data['titleEn'] ?? $data['title'] ?? '');
        $document->setTitleFr($data['titleFr'] ?? $data['title'] ?? '');
        $document->setFilePath($data['filePath'] ?? '');
        $document->setFileType($data['fileType'] ?? 'application/pdf');
        $document->setFileSize($data['fileSize'] ?? null);
        $document->setIsActive($data['isActive'] ?? true);

        $this->entityManager->persist($document);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'id' => $document->getId(),
            'message' => 'Document created successfully'
        ], Response::HTTP_CREATED);
    }

    #[Route('/documents/{id}', name: 'admin_update_final_step_document', methods: ['PUT'])]
    public function updateDocument(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $document = $this->finalStepDocumentRepository->find($id);
        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) $document->setTitle($data['title']);
        if (isset($data['titleEn'])) $document->setTitleEn($data['titleEn']);
        if (isset($data['titleFr'])) $document->setTitleFr($data['titleFr']);
        if (isset($data['filePath'])) $document->setFilePath($data['filePath']);
        if (isset($data['fileType'])) $document->setFileType($data['fileType']);
        if (isset($data['fileSize'])) $document->setFileSize($data['fileSize']);
        if (isset($data['isActive'])) $document->setIsActive($data['isActive']);

        $document->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Document updated successfully'
        ]);
    }

    #[Route('/documents/{id}', name: 'admin_delete_final_step_document', methods: ['DELETE'])]
    public function deleteDocument(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $document = $this->finalStepDocumentRepository->find($id);
        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($document);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    #[Route('/documents/{id}/toggle-active', name: 'admin_toggle_document_active', methods: ['POST'])]
    public function toggleDocumentActive(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $document = $this->finalStepDocumentRepository->find($id);
        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
        }

        $document->setIsActive(!$document->getIsActive());
        $document->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'isActive' => $document->getIsActive(),
            'message' => 'Document status updated successfully'
        ]);
    }

    #[Route('/documents/{id}/upload', name: 'admin_upload_final_step_document', methods: ['POST'])]
    public function uploadDocument(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $document = $this->finalStepDocumentRepository->find($id);
        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
        }

        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile instanceof UploadedFile) {
            return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }

        // Validate file type
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
        if (!in_array($uploadedFile->getMimeType(), $allowedTypes)) {
            return new JsonResponse(['error' => 'Invalid file type. Allowed types: PDF, DOC, DOCX, JPG, PNG'], Response::HTTP_BAD_REQUEST);
        }

        // Create upload directory if it doesn't exist
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/final-steps';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $uploadedFile->guessExtension();
        $newFilename = $originalFilename . '_' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . '/' . $newFilename;

        // Move uploaded file
        $uploadedFile->move($uploadDir, $newFilename);

        // Update document
        $document->setFilePath('/uploads/final-steps/' . $newFilename);
        $document->setFileType($uploadedFile->getMimeType());
        $document->setFileSize($uploadedFile->getSize());
        $document->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'filePath' => $document->getFilePath(),
            'fileType' => $document->getFileType(),
            'fileSize' => $document->getFileSize(),
            'message' => 'File uploaded successfully'
        ]);
    }

    #[Route('/{id}/toggle-active', name: 'admin_toggle_step_active', methods: ['POST'])]
    public function toggleStepActive(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // TODO: Add admin role check here

        $step = $this->finalStepRepository->find($id);
        if (!$step) {
            return new JsonResponse(['error' => 'Final step not found'], Response::HTTP_NOT_FOUND);
        }

        $step->setIsActive(!$step->getIsActive());
        $step->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'isActive' => $step->getIsActive(),
            'message' => 'Final step status updated successfully'
        ]);
    }
}
