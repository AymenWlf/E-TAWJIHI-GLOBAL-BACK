<?php

#[Route('/documents/{id}/view', name: 'view_document', methods: ['GET'])]
public function viewDocument(int $id): Response
{
    $user = $this->getUser();
    if (!$user) {
        return new Response('User not authenticated', 401);
    }

    $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
    if (!$userProfile) {
        return new Response('User profile not found', 404);
    }

    $document = $this->entityManager->getRepository(Document::class)->findOneBy([
        'id' => $id,
        'userProfile' => $userProfile
    ]);

    if (!$document) {
        return new Response('Document not found', 404);
    }

    // Get file path
    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/documents/';
    $filePath = $uploadDir . $document->getFilename();

    // Check if file exists
    if (!file_exists($filePath)) {
        return new Response('File not found on server', 404);
    }

    // Create response with file content
    $response = new Response();
    $response->headers->set('Content-Type', $document->getMimeType());
    $response->headers->set('Content-Disposition', 'inline; filename="' . $document->getOriginalFilename() . '"');
    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');

    // Set file content
    $response->setContent(file_get_contents($filePath));

    return $response;
}
