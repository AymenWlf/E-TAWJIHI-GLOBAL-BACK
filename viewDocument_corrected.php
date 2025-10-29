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
    error_log("File not found: " . $filePath);
    return new Response('File not found on server: ' . $filePath, 404);
    }

    // Log file info for debugging
    error_log("Serving file: " . $filePath);
    error_log("File size: " . filesize($filePath) . " bytes");
    error_log("MIME type: " . $document->getMimeType());

    // Create response with file content
    $response = new Response();
    $response->headers->set('Content-Type', $document->getMimeType());
    $response->headers->set('Content-Disposition', 'inline; filename="' . $document->getOriginalFilename() . '"');
    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');

    // Set file content
    $fileContent = file_get_contents($filePath);
    if ($fileContent === false) {
    error_log("Failed to read file content: " . $filePath);
    return new Response('Failed to read file content', 500);
    }

    $response->setContent($fileContent);

    return $response;
    }