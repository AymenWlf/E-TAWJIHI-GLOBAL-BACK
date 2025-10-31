<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\Qualification;
use App\Entity\Application;
use App\Entity\Shortlist;
use App\Entity\Document;
use App\Entity\DocumentTranslation;
use App\Repository\ShortlistRepository;
use App\Repository\UserRepository;
use App\Repository\UserProfileRepository;
use App\Repository\DocumentTranslationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserProfileRepository $userProfileRepository,
        private ShortlistRepository $shortlistRepository,
        private DocumentTranslationRepository $documentTranslationRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'get_profile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $profile = $user->getProfile();
        if (!$profile) {
            $profile = $this->createDefaultProfile($user);
        }

        $data = [
            'id' => $profile->getId(),
            'firstName' => $profile->getFirstName(),
            'lastName' => $profile->getLastName(),
            'email' => $profile->getEmail() ?? $user->getEmail(),
            'phone' => $profile->getPhone(),
            'whatsapp' => $profile->getWhatsapp(),
            'phoneCountry' => $profile->getPhoneCountry(),
            'whatsappCountry' => $profile->getWhatsappCountry(),
            'dateOfBirth' => $profile->getDateOfBirth()?->format('Y-m-d'),
            'gender' => $profile->getGender(),
            'maritalStatus' => $profile->getMaritalStatus(),
            'countryOfBirth' => $profile->getCountryOfBirth(),
            'cityOfBirth' => $profile->getCityOfBirth(),
            'nationality' => $profile->getNationality(),
            'country' => $profile->getCountry(),
            'city' => $profile->getCity(),
            'address' => $profile->getAddress(),
            'postalCode' => $profile->getPostalCode(),
            'passportNumber' => $profile->getPassportNumber(),
            'passportExpirationDate' => $profile->getPassportExpirationDate()?->format('Y-m-d'),
            'cinNumber' => $profile->getCinNumber(),
            'alternateEmail' => $profile->getAlternateEmail(),
            'religion' => $profile->getReligion(),
            'nativeLanguage' => $profile->getNativeLanguage(),
            'chineseName' => $profile->getChineseName(),
            'wechatId' => $profile->getWechatId(),
            'skypeNo' => $profile->getSkypeNo(),
            'emergencyContactName' => $profile->getEmergencyContactName(),
            'emergencyContactGender' => $profile->getEmergencyContactGender(),
            'emergencyContactRelationship' => $profile->getEmergencyContactRelationship(),
            'emergencyContactPhone' => $profile->getEmergencyContactPhone(),
            'emergencyContactEmail' => $profile->getEmergencyContactEmail(),
            'emergencyContactAddress' => $profile->getEmergencyContactAddress(),
            'hasWorkExperience' => $profile->getHasWorkExperience(),
            'workCompany' => $profile->getWorkCompany(),
            'workPosition' => $profile->getWorkPosition(),
            'workStartDate' => $profile->getWorkStartDate()?->format('Y-m-d'),
            'workEndDate' => $profile->getWorkEndDate()?->format('Y-m-d'),
            'workDescription' => $profile->getWorkDescription(),
            'avatar' => $profile->getAvatar(),
            'studyLevel' => $profile->getStudyLevel(),
            'fieldOfStudy' => $profile->getFieldOfStudy(),
            'preferredCountry' => $profile->getPreferredCountry(),
            'startDate' => $profile->getStartDate(),
            'preferredCurrency' => $profile->getPreferredCurrency(),
            'annualBudget' => $profile->getAnnualBudget(),
            'scholarshipRequired' => $profile->isScholarshipRequired(),
            'languagePreferences' => $profile->getLanguagePreferences(),
            'createdAt' => $profile->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $profile->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        return new JsonResponse($data);
    }

    #[Route('', name: 'update_profile', methods: ['PUT'])]
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $profile = $user->getProfile();
        if (!$profile) {
            $profile = $this->createDefaultProfile($user);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) {
            $profile->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $profile->setLastName($data['lastName']);
        }
        if (isset($data['phone'])) {
            $profile->setPhone($data['phone']);
        }
        if (isset($data['whatsapp'])) {
            $profile->setWhatsapp($data['whatsapp']);
        }
        if (isset($data['dateOfBirth'])) {
            $profile->setDateOfBirth($data['dateOfBirth'] ? new \DateTime($data['dateOfBirth']) : null);
        }
        if (isset($data['nationality'])) {
            $profile->setNationality($data['nationality']);
        }
        if (isset($data['country'])) {
            $profile->setCountry($data['country']);
        }
        if (isset($data['city'])) {
            $profile->setCity($data['city']);
        }
        if (isset($data['address'])) {
            $profile->setAddress($data['address']);
        }
        if (isset($data['postalCode'])) {
            $profile->setPostalCode($data['postalCode']);
        }
        if (isset($data['studyLevel'])) {
            $profile->setStudyLevel($data['studyLevel']);
        }
        if (isset($data['fieldOfStudy'])) {
            $profile->setFieldOfStudy($data['fieldOfStudy']);
        }
        if (isset($data['preferredCountry'])) {
            $profile->setPreferredCountry($data['preferredCountry']);
        }
        if (isset($data['startDate'])) {
            $profile->setStartDate($data['startDate']);
        }
        if (isset($data['preferredCurrency'])) {
            $profile->setPreferredCurrency($data['preferredCurrency']);
        }
        if (isset($data['phoneCountry'])) {
            $profile->setPhoneCountry($data['phoneCountry']);
        }
        if (isset($data['whatsappCountry'])) {
            $profile->setWhatsappCountry($data['whatsappCountry']);
        }
        if (isset($data['passportNumber'])) {
            $profile->setPassportNumber($data['passportNumber']);
        }
        if (isset($data['passportExpirationDate'])) {
            $profile->setPassportExpirationDate($data['passportExpirationDate'] ? new \DateTime($data['passportExpirationDate']) : null);
        }
        if (isset($data['cinNumber'])) {
            $profile->setCinNumber($data['cinNumber']);
        }
        if (isset($data['email'])) {
            $profile->setEmail($data['email']);
        }
        if (isset($data['gender'])) {
            $profile->setGender($data['gender']);
        }
        if (isset($data['maritalStatus'])) {
            $profile->setMaritalStatus($data['maritalStatus']);
        }
        if (isset($data['countryOfBirth'])) {
            $profile->setCountryOfBirth($data['countryOfBirth']);
        }
        if (isset($data['cityOfBirth'])) {
            $profile->setCityOfBirth($data['cityOfBirth']);
        }
        if (isset($data['alternateEmail'])) {
            $profile->setAlternateEmail($data['alternateEmail']);
        }
        if (isset($data['religion'])) {
            $profile->setReligion($data['religion']);
        }
        if (isset($data['nativeLanguage'])) {
            $profile->setNativeLanguage($data['nativeLanguage']);
        }
        if (isset($data['chineseName'])) {
            $profile->setChineseName($data['chineseName']);
        }
        if (isset($data['wechatId'])) {
            $profile->setWechatId($data['wechatId']);
        }
        if (isset($data['skypeNo'])) {
            $profile->setSkypeNo($data['skypeNo']);
        }
        if (isset($data['emergencyContactName'])) {
            $profile->setEmergencyContactName($data['emergencyContactName']);
        }
        if (isset($data['emergencyContactGender'])) {
            $profile->setEmergencyContactGender($data['emergencyContactGender']);
        }
        if (isset($data['emergencyContactRelationship'])) {
            $profile->setEmergencyContactRelationship($data['emergencyContactRelationship']);
        }
        if (isset($data['emergencyContactPhone'])) {
            $profile->setEmergencyContactPhone($data['emergencyContactPhone']);
        }
        if (isset($data['emergencyContactEmail'])) {
            $profile->setEmergencyContactEmail($data['emergencyContactEmail']);
        }
        if (isset($data['emergencyContactAddress'])) {
            $profile->setEmergencyContactAddress($data['emergencyContactAddress']);
        }
        if (isset($data['hasWorkExperience'])) {
            $profile->setHasWorkExperience($data['hasWorkExperience']);
        }
        if (isset($data['workCompany'])) {
            $profile->setWorkCompany($data['workCompany']);
        }
        if (isset($data['workPosition'])) {
            $profile->setWorkPosition($data['workPosition']);
        }
        if (isset($data['workStartDate'])) {
            $profile->setWorkStartDate($data['workStartDate'] ? new \DateTime($data['workStartDate']) : null);
        }
        if (isset($data['workEndDate'])) {
            $profile->setWorkEndDate($data['workEndDate'] ? new \DateTime($data['workEndDate']) : null);
        }
        if (isset($data['workDescription'])) {
            $profile->setWorkDescription($data['workDescription']);
        }
        if (isset($data['annualBudget'])) {
            $profile->setAnnualBudget($data['annualBudget'] !== '' && $data['annualBudget'] !== null ? (float)$data['annualBudget'] : null);
        }
        if (isset($data['scholarshipRequired'])) {
            $profile->setScholarshipRequired($data['scholarshipRequired']);
        }
        if (isset($data['languagePreferences'])) {
            $profile->setLanguagePreferences($data['languagePreferences']);
        }

        $profile->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Profile updated successfully']);
    }

    #[Route('/qualifications', name: 'get_qualifications', methods: ['GET'])]
    public function getQualifications(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $profile = $user->getProfile();
        if (!$profile) {
            return new JsonResponse([]);
        }

        $qualifications = $profile->getQualifications();
        $data = [];
        foreach ($qualifications as $qualification) {
            $data[] = [
                'id' => $qualification->getId(),
                'type' => $qualification->getType(),
                'title' => $qualification->getTitle(),
                'institution' => $qualification->getInstitution(),
                'country' => $qualification->getCountry(),
                'field' => $qualification->getField(),
                'grade' => $qualification->getGrade(),
                'score' => $qualification->getScore(),
                'scoreType' => $qualification->getScoreType(),
                'description' => $qualification->getDescription(),
                'board' => $qualification->getBoard(),
                'gradingScheme' => $qualification->getGradingScheme(),
                'englishScore' => $qualification->getEnglishScore(),
                'academicQualification' => $qualification->getAcademicQualification(),
                'exactQualificationName' => $qualification->getExactQualificationName(),
                'baccalaureateStream' => $qualification->getBaccalaureateStream(),
                'baccalaureateStreamOther' => $qualification->getBaccalaureateStreamOther(),
                'startDate' => $qualification->getStartDate()?->format('Y-m-d'),
                'endDate' => $qualification->getEndDate()?->format('Y-m-d'),
                'expiryDate' => $qualification->getExpiryDate()?->format('Y-m-d'),
                'detailedScores' => $qualification->getDetailedScores(),
                'createdAt' => $qualification->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $qualification->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/qualifications', name: 'add_qualification', methods: ['POST'])]
    public function addQualification(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $profile = $user->getProfile();
        if (!$profile) {
            $profile = $this->createDefaultProfile($user);
        }

        $data = json_decode($request->getContent(), true);

        $qualification = new Qualification();
        $qualification->setUserProfile($profile);
        $qualification->setType($data['type']);
        $qualification->setTitle($data['title']);
        $qualification->setInstitution($data['institution'] ?? null);
        $qualification->setCountry($data['country'] ?? null);
        $qualification->setField($data['field'] ?? null);
        $qualification->setGrade($data['grade'] ?? null);
        $qualification->setScore($data['score'] ?? null);
        $qualification->setScoreType($data['scoreType'] ?? null);
        $qualification->setDescription($data['description'] ?? null);
        $qualification->setBoard($data['board'] ?? null);
        $qualification->setGradingScheme($data['gradingScheme'] ?? null);
        $qualification->setEnglishScore($data['englishScore'] ?? null);
        $qualification->setAcademicQualification($data['academicQualification'] ?? null);
        $qualification->setExactQualificationName($data['exactQualificationName'] ?? null);
        $qualification->setBaccalaureateStream($data['baccalaureateStream'] ?? null);
        $qualification->setBaccalaureateStreamOther($data['baccalaureateStreamOther'] ?? null);
        $qualification->setStartDate(array_key_exists('startDate', $data) && $data['startDate'] ? new \DateTime($data['startDate']) : null);
        $qualification->setEndDate(array_key_exists('endDate', $data) && $data['endDate'] ? new \DateTime($data['endDate']) : null);
        $qualification->setExpiryDate(array_key_exists('expiryDate', $data) && $data['expiryDate'] ? new \DateTime($data['expiryDate']) : null);
        $qualification->setDetailedScores($data['detailedScores'] ?? null);

        $this->entityManager->persist($qualification);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $qualification->getId(),
            'type' => $qualification->getType(),
            'title' => $qualification->getTitle(),
            'institution' => $qualification->getInstitution(),
            'country' => $qualification->getCountry(),
            'field' => $qualification->getField(),
            'grade' => $qualification->getGrade(),
            'score' => $qualification->getScore(),
            'scoreType' => $qualification->getScoreType(),
            'description' => $qualification->getDescription(),
            'board' => $qualification->getBoard(),
            'gradingScheme' => $qualification->getGradingScheme(),
            'englishScore' => $qualification->getEnglishScore(),
            'academicQualification' => $qualification->getAcademicQualification(),
            'exactQualificationName' => $qualification->getExactQualificationName(),
            'startDate' => $qualification->getStartDate()?->format('Y-m-d'),
            'endDate' => $qualification->getEndDate()?->format('Y-m-d'),
            'expiryDate' => $qualification->getExpiryDate()?->format('Y-m-d'),
            'detailedScores' => $qualification->getDetailedScores(),
            'createdAt' => $qualification->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $qualification->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/qualifications/{id}', name: 'update_qualification', methods: ['PUT'])]
    public function updateQualification(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $profile = $user->getProfile();
        if (!$profile) {
            return new JsonResponse(['error' => 'Profile not found'], Response::HTTP_NOT_FOUND);
        }

        $qualification = $this->entityManager->getRepository(Qualification::class)->findOneBy([
            'id' => $id,
            'userProfile' => $profile
        ]);

        if (!$qualification) {
            return new JsonResponse(['error' => 'Qualification not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Log pour déboguer
        error_log('Update qualification data received: ' . json_encode($data));

        if (isset($data['type'])) {
            $qualification->setType($data['type']);
        }
        if (isset($data['title'])) {
            $qualification->setTitle($data['title']);
        }
        if (isset($data['institution'])) {
            $qualification->setInstitution($data['institution']);
        }
        if (isset($data['country'])) {
            $qualification->setCountry($data['country']);
        }
        if (isset($data['field'])) {
            $qualification->setField($data['field']);
        }
        if (isset($data['grade'])) {
            $qualification->setGrade($data['grade']);
        }
        if (isset($data['score'])) {
            $qualification->setScore($data['score']);
        }
        if (isset($data['scoreType'])) {
            $qualification->setScoreType($data['scoreType']);
        }
        if (isset($data['description'])) {
            $qualification->setDescription($data['description']);
        }
        if (isset($data['board'])) {
            $qualification->setBoard($data['board']);
        }
        if (isset($data['gradingScheme'])) {
            $qualification->setGradingScheme($data['gradingScheme']);
        }
        if (isset($data['englishScore'])) {
            $qualification->setEnglishScore($data['englishScore']);
        }
        if (isset($data['academicQualification'])) {
            $qualification->setAcademicQualification($data['academicQualification']);
        }
        if (isset($data['exactQualificationName'])) {
            $qualification->setExactQualificationName($data['exactQualificationName']);
        }
        if (isset($data['baccalaureateStream'])) {
            $qualification->setBaccalaureateStream($data['baccalaureateStream']);
        }
        if (isset($data['baccalaureateStreamOther'])) {
            $qualification->setBaccalaureateStreamOther($data['baccalaureateStreamOther']);
        }
        if (array_key_exists('startDate', $data)) {
            $qualification->setStartDate($data['startDate'] ? new \DateTime($data['startDate']) : null);
        }
        if (array_key_exists('endDate', $data)) {
            $qualification->setEndDate($data['endDate'] ? new \DateTime($data['endDate']) : null);
        }
        if (array_key_exists('expiryDate', $data)) {
            $qualification->setExpiryDate($data['expiryDate'] ? new \DateTime($data['expiryDate']) : null);
        }
        if (isset($data['detailedScores'])) {
            $qualification->setDetailedScores($data['detailedScores']);
        }

        $this->entityManager->flush();

        // Log pour déboguer
        error_log('Qualification updated successfully. ID: ' . $qualification->getId() . ', StartDate: ' . ($qualification->getStartDate() ? $qualification->getStartDate()->format('Y-m-d') : 'null') . ', EndDate: ' . ($qualification->getEndDate() ? $qualification->getEndDate()->format('Y-m-d') : 'null'));

        return new JsonResponse(['message' => 'Qualification updated successfully']);
    }

    #[Route('/qualifications/{id}', name: 'delete_qualification', methods: ['DELETE'])]
    public function deleteQualification(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $profile = $user->getProfile();
        if (!$profile) {
            return new JsonResponse(['error' => 'Profile not found'], Response::HTTP_NOT_FOUND);
        }

        $qualification = $this->entityManager->getRepository(Qualification::class)->findOneBy([
            'id' => $id,
            'userProfile' => $profile
        ]);

        if (!$qualification) {
            return new JsonResponse(['error' => 'Qualification not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($qualification);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Qualification deleted successfully']);
    }

    #[Route('/applications', name: 'get_applications', methods: ['GET'])]
    public function getApplications(): JsonResponse
    {
        // Rediriger vers la page ApplicationProcess avec des inputs statiques
        return new JsonResponse([
            'message' => 'Applications are now managed through the Application Process page',
            'redirect' => '/application/process'
        ]);
    }

    #[Route('/applications', name: 'add_application', methods: ['POST'])]
    public function addApplication(Request $request): JsonResponse
    {
        // Rediriger vers la page ApplicationProcess avec des inputs statiques
        return new JsonResponse([
            'message' => 'Applications are now managed through the Application Process page',
            'redirect' => '/application/process'
        ]);
    }

    #[Route('/shortlist', name: 'get_shortlist', methods: ['GET'])]
    public function getShortlist(): JsonResponse
    {
        // Shortlist functionality temporarily disabled
        return new JsonResponse([
            'programs' => [],
            'establishments' => []
        ]);
    }

    #[Route('/shortlist/programs/{programId}', name: 'add_program_to_shortlist', methods: ['POST'])]
    public function addProgramToShortlist(int $programId): JsonResponse
    {
        // Shortlist functionality temporarily disabled
        return new JsonResponse(['message' => 'Shortlist functionality temporarily disabled']);
    }

    #[Route('/shortlist/programs/{programId}', name: 'remove_program_from_shortlist', methods: ['DELETE'])]
    public function removeProgramFromShortlist(int $programId): JsonResponse
    {
        // Shortlist functionality temporarily disabled
        return new JsonResponse(['message' => 'Shortlist functionality temporarily disabled']);
    }

    #[Route('/shortlist/establishments/{establishmentId}', name: 'add_establishment_to_shortlist', methods: ['POST'])]
    public function addEstablishmentToShortlist(int $establishmentId): JsonResponse
    {
        // Shortlist functionality temporarily disabled
        return new JsonResponse(['message' => 'Shortlist functionality temporarily disabled']);
    }

    #[Route('/shortlist/establishments/{establishmentId}', name: 'remove_establishment_from_shortlist', methods: ['DELETE'])]
    public function removeEstablishmentFromShortlist(int $establishmentId): JsonResponse
    {
        // Shortlist functionality temporarily disabled
        return new JsonResponse(['message' => 'Shortlist functionality temporarily disabled']);
    }

    // Document management endpoints
    #[Route('/documents', name: 'get_documents', methods: ['GET'])]
    public function getDocuments(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['documents' => []]);
        }

        $documents = [];
        foreach ($userProfile->getDocuments() as $document) {
            $documents[] = [
                'id' => $document->getId(),
                'type' => $document->getType(),
                'category' => $document->getCategory(),
                'title' => $document->getTitle(),
                'filename' => $document->getFilename(),
                'originalFilename' => $document->getOriginalFilename(),
                'mimeType' => $document->getMimeType(),
                'fileSize' => $document->getFileSize(),
                'status' => $document->getStatus(),
                'validationStatus' => $document->getValidationStatus(),
                'validationNotes' => $document->getValidationNotes(),
                'validatedBy' => $document->getValidatedBy(),
                'validatedAt' => $document->getValidatedAt()?->format('Y-m-d H:i:s'),
                'rejectionReason' => $document->getRejectionReason(),
                'originalLanguage' => $document->getOriginalLanguage(),
                'etawjihiNotes' => $document->getEtawjihiNotes(),
                'expiryDate' => $document->getExpiryDate()?->format('Y-m-d'),
                'createdAt' => $document->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $document->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse(['documents' => $documents]);
    }

    #[Route('/documents', name: 'upload_document', methods: ['POST'])]
    public function uploadDocument(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        // Handle file upload
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'No file provided'], 400);
        }

        // Validate file
        if (!$uploadedFile->isValid()) {
            return new JsonResponse(['error' => 'Invalid file upload: ' . $uploadedFile->getErrorMessage()], 400);
        }

        // Check for upload errors
        $uploadError = $uploadedFile->getError();
        if ($uploadError !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            return new JsonResponse(['error' => 'Upload error: ' . ($errorMessages[$uploadError] ?? 'Unknown error')], 400);
        }

        // Get document data from form
        $type = $request->request->get('type', 'personal');
        $category = $request->request->get('category', 'other');
        $title = $request->request->get('title', '');
        $description = $request->request->get('description', '');
        $originalLanguage = $request->request->get('originalLanguage', '');

        // Validate file size (10MB max)
        $maxSize = 10 * 1024 * 1024; // 10MB

        // Try to get file size from different sources
        $fileSize = null;
        if (method_exists($uploadedFile, 'getSize') && $uploadedFile->getSize() !== false) {
            $fileSize = $uploadedFile->getSize();
        } elseif (method_exists($uploadedFile, 'getClientSize') && $uploadedFile->getClientSize() !== false) {
            $fileSize = $uploadedFile->getClientSize();
        } else {
            // Fallback: check the temporary file directly
            $tmpPath = $uploadedFile->getPathname();
            if (file_exists($tmpPath)) {
                $fileSize = filesize($tmpPath);
            }
        }

        if ($fileSize === null || $fileSize === false) {
            return new JsonResponse(['error' => 'Could not determine file size. File may be corrupted or too large.'], 400);
        }

        if ($fileSize > $maxSize) {
            return new JsonResponse(['error' => 'File too large. Maximum size: 10MB, actual size: ' . round($fileSize / 1024 / 1024, 2) . 'MB'], 400);
        }

        // Validate file type
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];

        $mimeType = $uploadedFile->getMimeType();
        if (!in_array($mimeType, $allowedTypes)) {
            return new JsonResponse(['error' => 'File type not allowed. Allowed types: PDF, DOC, DOCX, JPG, PNG'], 400);
        }

        try {
            // Get upload directory from parameters
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/documents/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename with title + unique code
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->guessExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueCode = uniqid('doc_', true);
            $fileName = $title ?
                preg_replace('/[^a-zA-Z0-9_-]/', '_', $title) . '_' . $uniqueCode . '.' . $extension :
                $uniqueCode . '.' . $extension;

            // Move file to upload directory
            try {
                $uploadedFile->move($uploadDir, $fileName);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Failed to save file: ' . $e->getMessage()], 500);
            }

            // Create document entity
            $document = new Document();
            $document->setUserProfile($userProfile);
            $document->setType($type);
            $document->setCategory($category);

            // If originalLanguage is missing, infer default from user's applications (China -> English, France -> French)
            if (!$originalLanguage) {
                $applications = $this->entityManager->getRepository(Application::class)->findBy(['user' => $user]);
                $hasChinaApplication = false;
                $hasFranceApplication = false;
                foreach ($applications as $app) {
                    if (method_exists($app, 'getIsChina') && $app->getIsChina()) { $hasChinaApplication = true; }
                    if (method_exists($app, 'getIsFrance') && $app->getIsFrance()) { $hasFranceApplication = true; }
                }
                if ($hasChinaApplication) {
                    $originalLanguage = 'English';
                } elseif ($hasFranceApplication) {
                    $originalLanguage = 'French';
                }
            }

            // Canonicalize title regardless of UI language
            $canonicalMap = [
                'passport' => ['Passeport', 'Passport'],
                'nationalId' => ['Carte Nationale', 'National ID', 'National ID Card'],
                'cv' => ['CV', 'Curriculum Vitae', 'Resume'],
                'guardian1NationalId' => ['Carte Nationale du Tuteur 1', 'Guardian 1 National ID', 'Guardian 1 ID', 'Guardian ID 1', 'Guardian National ID 1', 'Carte Nationale Tuteur 1'],
                'generalTranscript' => ['Relevé de Notes', 'General Transcript', 'Transcript', 'Academic Transcript'],
                'baccalaureate' => ['Baccalauréat', 'Baccalaureate Diploma', 'Baccalaureate'],
                'motivationLetter' => ['Lettre de Motivation', 'Motivation Letter'],
                'recommendationLetter1' => ['Lettre de Recommandation 1', 'Recommendation Letter 1'],
                'medicalHealthCheck' => ['Certificat Médical de Santé', 'Medical Health Check'],
                'anthropometricRecord' => ['Fiche Anthropométrique (Bonne Conduite)', 'Anthropometric Record', 'Good Conduct'],
                'frenchTest' => ['Certificat de Test de Français', 'French Test Certificate']
            ];
            $canonicalLabels = [
                // Fixed canonical display labels (in English for consistency)
                'passport' => 'Passport',
                'nationalId' => 'National ID Card',
                'cv' => 'Curriculum Vitae (CV)',
                'guardian1NationalId' => 'Guardian 1 National ID',
                'generalTranscript' => 'General Transcript',
                'baccalaureate' => 'Baccalaureate Diploma',
                'motivationLetter' => 'Motivation Letter',
                'recommendationLetter1' => 'Recommendation Letter 1',
                'medicalHealthCheck' => 'Medical Health Check',
                'anthropometricRecord' => 'Anthropometric Record (Good Conduct)',
                'frenchTest' => 'French Test Certificate'
            ];
            $normalize = function(string $s): string {
                $s = mb_strtolower($s, 'UTF-8');
                $s = str_replace(['é','è','ê','ë','à','â','ä','ù','û','ü','ô','ö','î','ï','ç'], ['e','e','e','e','a','a','a','u','u','u','o','o','i','i','c'], $s);
                return $s;
            };
            $inferKeyFromTitle = function(?string $inputTitle) use ($canonicalMap, $normalize): ?string {
                if (!$inputTitle) return null;
                $normInput = $normalize($inputTitle);
                foreach ($canonicalMap as $key => $alts) {
                    foreach ($alts as $alt) {
                        if ($normInput === $normalize($alt)) return $key;
                    }
                }
                foreach ($canonicalMap as $key => $alts) {
                    foreach ($alts as $alt) {
                        if (strpos($normInput, $normalize($alt)) !== false) return $key;
                    }
                }
                return null;
            };
            // Prefer using provided $type as hint when possible
            $inferredKey = $inferKeyFromTitle($title);
            if (!$inferredKey && isset($canonicalMap[$type])) {
                $inferredKey = $type;
            }
            $canonicalTitle = $inferredKey ?: $title;

            $document->setTitle($canonicalTitle);
            $document->setFilename($fileName);
            $document->setOriginalFilename($originalName);
            $document->setMimeType($mimeType);
            $document->setFileSize($fileSize);
            $document->setStatus('uploaded');
            $document->setValidationStatus('under_review');
            $document->setDescription($description);
            $document->setOriginalLanguage($originalLanguage);

            if ($request->request->has('expiryDate') && $request->request->get('expiryDate')) {
                $document->setExpiryDate(new \DateTime($request->request->get('expiryDate')));
            }

            $this->entityManager->persist($document);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Document uploaded successfully',
                'document' => [
                    'id' => $document->getId(),
                    'type' => $document->getType(),
                    'category' => $document->getCategory(),
                    'title' => $document->getTitle(),
                    'filename' => $document->getFilename(),
                    'originalFilename' => $document->getOriginalFilename(),
                    'mimeType' => $document->getMimeType(),
                    'fileSize' => $document->getFileSize(),
                    'status' => $document->getStatus(),
                    'validationStatus' => $document->getValidationStatus(),
                    'description' => $document->getDescription(),
                    'originalLanguage' => $document->getOriginalLanguage(),
                    'etawjihiNotes' => $document->getEtawjihiNotes(),
                    'expiryDate' => $document->getExpiryDate()?->format('Y-m-d'),
                    'createdAt' => $document->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $document->getUpdatedAt()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to upload document: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/documents/{id}', name: 'update_document', methods: ['PUT'])]
    public function updateDocument(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'id' => $id,
            'userProfile' => $userProfile
        ]);

        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }

        if (isset($data['title'])) {
            $document->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $document->setDescription($data['description']);
        }
        if (isset($data['expiryDate'])) {
            $document->setExpiryDate($data['expiryDate'] ? new \DateTime($data['expiryDate']) : null);
        }
        if (isset($data['validationStatus'])) {
            $document->setValidationStatus($data['validationStatus']);
        }
        if (isset($data['validationNotes'])) {
            $document->setValidationNotes($data['validationNotes']);
        }
        if (isset($data['rejectionReason'])) {
            $document->setRejectionReason($data['rejectionReason']);
        }
        if (isset($data['originalLanguage'])) {
            $document->setOriginalLanguage($data['originalLanguage']);
        }
        if (isset($data['etawjihiNotes'])) {
            $document->setEtawjihiNotes($data['etawjihiNotes']);
        }

        $document->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Document updated successfully',
            'document' => [
                'id' => $document->getId(),
                'title' => $document->getTitle(),
                'validationStatus' => $document->getValidationStatus(),
                'validationNotes' => $document->getValidationNotes(),
                'rejectionReason' => $document->getRejectionReason(),
                'updatedAt' => $document->getUpdatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    #[Route('/documents/{id}', name: 'delete_document', methods: ['DELETE'])]
    public function deleteDocument(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'id' => $id,
            'userProfile' => $userProfile
        ]);

        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], 404);
        }

        $this->entityManager->remove($document);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Document deleted successfully']);
    }

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

    // Document Translation endpoints
    #[Route('/documents/{id}/translations', name: 'get_document_translations', methods: ['GET'])]
    public function getDocumentTranslations(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'id' => $id,
            'userProfile' => $userProfile
        ]);

        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], 404);
        }

        $translations = $this->documentTranslationRepository->findByOriginalDocument($id);

        $data = array_map(function (DocumentTranslation $translation) {
            return [
                'id' => $translation->getId(),
                'targetLanguage' => $translation->getTargetLanguage(),
                'filename' => $translation->getFilename(),
                'originalFilename' => $translation->getOriginalFilename(),
                'mimeType' => $translation->getMimeType(),
                'fileSize' => $translation->getFileSize(),
                'status' => $translation->getStatus(),
                'notes' => $translation->getNotes(),
                'etawjihiNotes' => $translation->getEtawjihiNotes(),
                'createdAt' => $translation->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $translation->getUpdatedAt()->format('Y-m-d H:i:s'),
                'completedAt' => $translation->getCompletedAt()?->format('Y-m-d H:i:s')
            ];
        }, $translations);

        return new JsonResponse(['success' => true, 'translations' => $data]);
    }

    #[Route('/documents/{id}/translations', name: 'create_document_translation', methods: ['POST'])]
    public function createDocumentTranslation(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'id' => $id,
            'userProfile' => $userProfile
        ]);

        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], 404);
        }

        // Handle file upload
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'No file provided'], 400);
        }

        // Validate file
        if (!$uploadedFile->isValid()) {
            return new JsonResponse(['error' => 'Invalid file upload: ' . $uploadedFile->getErrorMessage()], 400);
        }

        // Get translation data from form
        $targetLanguage = $request->request->get('targetLanguage');
        $notes = $request->request->get('notes', '');

        if (!$targetLanguage) {
            return new JsonResponse(['error' => 'Target language is required'], 400);
        }

        // Check if translation already exists for this language
        $existingTranslation = $this->documentTranslationRepository->findByDocumentAndLanguage($id, $targetLanguage);
        if ($existingTranslation) {
            return new JsonResponse(['error' => 'Translation already exists for this language'], 409);
        }

        // Validate file size (10MB max)
        $maxSize = 10 * 1024 * 1024; // 10MB
        $fileSize = $uploadedFile->getSize();

        if ($fileSize === false) {
            return new JsonResponse(['error' => 'Could not determine file size. File may be corrupted or too large.'], 400);
        }

        if ($fileSize > $maxSize) {
            return new JsonResponse(['error' => 'File too large. Maximum size: 10MB'], 400);
        }

        // Validate file type
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];

        $mimeType = $uploadedFile->getMimeType();
        if (!in_array($mimeType, $allowedTypes)) {
            return new JsonResponse(['error' => 'File type not allowed. Allowed types: PDF, DOC, DOCX, JPG, PNG'], 400);
        }

        try {
            // Get upload directory - use translations-documents folder like documents
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/translations-documents/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename with target language + unique code (same pattern as documents)
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->guessExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueCode = uniqid('trans_', true);
            $fileName = $targetLanguage . '_' . $uniqueCode . '.' . $extension;

            // Move file to upload directory
            try {
                $uploadedFile->move($uploadDir, $fileName);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Failed to save file: ' . $e->getMessage()], 500);
            }

            // Create translation entity
            $translation = new DocumentTranslation();
            $translation->setOriginalDocument($document);
            $translation->setTargetLanguage($targetLanguage);
            $translation->setFilename($fileName);
            $translation->setOriginalFilename($originalName);
            $translation->setMimeType($mimeType);
            $translation->setFileSize($fileSize);
            $translation->setStatus('uploaded');
            $translation->setNotes($notes);

            $this->entityManager->persist($translation);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'translation' => [
                    'id' => $translation->getId(),
                    'targetLanguage' => $translation->getTargetLanguage(),
                    'filename' => $translation->getFilename(),
                    'originalFilename' => $translation->getOriginalFilename(),
                    'mimeType' => $translation->getMimeType(),
                    'fileSize' => $translation->getFileSize(),
                    'status' => $translation->getStatus(),
                    'notes' => $translation->getNotes(),
                    'etawjihiNotes' => $translation->getEtawjihiNotes(),
                    'createdAt' => $translation->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $translation->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'completedAt' => $translation->getCompletedAt()?->format('Y-m-d H:i:s')
                ]
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to upload translation: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/documents/{id}/translations/{translationId}', name: 'update_document_translation', methods: ['PUT'])]
    public function updateDocumentTranslation(int $id, int $translationId, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'id' => $id,
            'userProfile' => $userProfile
        ]);

        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], 404);
        }

        $translation = $this->documentTranslationRepository->findOneBy([
            'id' => $translationId,
            'originalDocument' => $document
        ]);

        if (!$translation) {
            return new JsonResponse(['error' => 'Translation not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['status'])) {
            $translation->setStatus($data['status']);
            if ($data['status'] === 'completed') {
                $translation->setCompletedAt(new \DateTime());
            }
        }

        if (isset($data['notes'])) {
            $translation->setNotes($data['notes']);
        }

        if (isset($data['etawjihiNotes'])) {
            $translation->setEtawjihiNotes($data['etawjihiNotes']);
        }

        if (isset($data['targetLanguage'])) {
            // Check if translation already exists for this language (excluding current translation)
            $existingTranslation = $this->documentTranslationRepository->findByDocumentAndLanguage($id, $data['targetLanguage']);
            if ($existingTranslation && $existingTranslation->getId() !== $translationId) {
                return new JsonResponse(['error' => 'Translation already exists for this language'], 409);
            }
            $translation->setTargetLanguage($data['targetLanguage']);
        }

        $translation->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'translation' => [
                'id' => $translation->getId(),
                'targetLanguage' => $translation->getTargetLanguage(),
                'filename' => $translation->getFilename(),
                'originalFilename' => $translation->getOriginalFilename(),
                'mimeType' => $translation->getMimeType(),
                'fileSize' => $translation->getFileSize(),
                'status' => $translation->getStatus(),
                'notes' => $translation->getNotes(),
                'etawjihiNotes' => $translation->getEtawjihiNotes(),
                'createdAt' => $translation->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $translation->getUpdatedAt()->format('Y-m-d H:i:s'),
                'completedAt' => $translation->getCompletedAt()?->format('Y-m-d H:i:s')
            ]
        ]);
    }

    #[Route('/documents/{id}/translations/{translationId}', name: 'delete_document_translation', methods: ['DELETE'])]
    public function deleteDocumentTranslation(int $id, int $translationId): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'id' => $id,
            'userProfile' => $userProfile
        ]);

        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], 404);
        }

        $translation = $this->documentTranslationRepository->findOneBy([
            'id' => $translationId,
            'originalDocument' => $document
        ]);

        if (!$translation) {
            return new JsonResponse(['error' => 'Translation not found'], 404);
        }

        $this->entityManager->remove($translation);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Translation deleted successfully']);
    }

    #[Route('/documents/{id}/translations/{translationId}/view', name: 'view_document_translation', methods: ['GET'])]
    public function viewDocumentTranslation(int $id, int $translationId): Response
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

        $translation = $this->documentTranslationRepository->findOneBy([
            'id' => $translationId,
            'originalDocument' => $document
        ]);

        if (!$translation) {
            return new Response('Translation not found', 404);
        }

        // Get file path - use translations-documents folder like documents
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/translations-documents/';
        $filePath = $uploadDir . $translation->getFilename();

        // Check if file exists
        if (!file_exists($filePath)) {
            error_log("Translation file not found: " . $filePath);
            return new Response('Translation file not found on server: ' . $filePath, 404);
        }

        // Log file info for debugging
        error_log("Serving translation file: " . $filePath);
        error_log("File size: " . filesize($filePath) . " bytes");
        error_log("MIME type: " . $translation->getMimeType());

        // Create response with file content
        $response = new Response();
        $response->headers->set('Content-Type', $translation->getMimeType());
        $response->headers->set('Content-Disposition', 'inline; filename="' . $translation->getOriginalFilename() . '"');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        // Set file content
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            error_log("Failed to read translation file content: " . $filePath);
            return new Response('Failed to read translation file content', 500);
        }

        $response->setContent($fileContent);

        return $response;
    }

    #[Route('/documents/{id}/translations/{translationId}/content', name: 'get_document_translation_content', methods: ['GET'])]
    public function getDocumentTranslationContent(int $id, int $translationId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], 404);
        }

        $document = $this->entityManager->getRepository(Document::class)->findOneBy([
            'id' => $id,
            'userProfile' => $userProfile
        ]);

        if (!$document) {
            return new JsonResponse(['error' => 'Document not found'], 404);
        }

        $translation = $this->documentTranslationRepository->findOneBy([
            'id' => $translationId,
            'originalDocument' => $document
        ]);

        if (!$translation) {
            return new JsonResponse(['error' => 'Translation not found'], 404);
        }

        // Get file path - use translations-documents folder like documents
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/translations-documents/';
        $filePath = $uploadDir . $translation->getFilename();

        // Check if file exists
        if (!file_exists($filePath)) {
            error_log("Translation file not found: " . $filePath);
            return new Response('Translation file not found on server: ' . $filePath, 404);
        }

        // Create response with file content
        $response = new Response();
        $response->headers->set('Content-Type', $translation->getMimeType());
        $response->headers->set('Content-Disposition', 'inline; filename="' . $translation->getOriginalFilename() . '"');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        // Set file content
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            error_log("Failed to read translation file content: " . $filePath);
            return new Response('Failed to read translation file content', 500);
        }

        $response->setContent($fileContent);

        return $response;
    }

    #[Route('/validate-step2', name: 'validate_step2', methods: ['POST'])]
    public function validateStep2(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $isValidated = $data['isValidated'] ?? false;

        // Save validation status to user profile
        $userProfile->setStep2Validated($isValidated);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'step2Validated' => $isValidated
        ]);
    }

    #[Route('/get-step2-validation', name: 'get_step2_validation', methods: ['GET'])]
    public function getStep2Validation(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'step2Validated' => $userProfile->getStep2Validated() ?? false
        ]);
    }

    #[Route('/validate-step4', name: 'validate_step4', methods: ['POST'])]
    public function validateStep4(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $isValidated = $data['isValidated'] ?? false;

        // Save validation status to user profile
        $userProfile->setStep4Validated($isValidated);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'step4Validated' => $isValidated
        ]);
    }

    #[Route('/get-step4-validation', name: 'get_step4_validation', methods: ['GET'])]
    public function getStep4Validation(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'step4Validated' => $userProfile->getStep4Validated() ?? false
        ]);
    }

    #[Route('/validate-step5', name: 'validate_step5', methods: ['POST'])]
    public function validateStep5(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $isValidated = $data['isValidated'] ?? false;

        $userProfile->setStep5Validated($isValidated);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'step5Validated' => $isValidated
        ]);
    }

    #[Route('/get-step5-validation', name: 'get_step5_validation', methods: ['GET'])]
    public function getStep5Validation(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'step5Validated' => $userProfile->getStep5Validated() ?? false
        ]);
    }

    #[Route('/validate-step3', name: 'validate_step3', methods: ['GET'])]
    public function validateStep3(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Get user profile
        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        // Get user documents via userProfile
        $documents = $this->entityManager->getRepository(Document::class)->findBy(['userProfile' => $userProfile]);

        // Check if user has China or France applications
        $applications = $this->entityManager->getRepository(Application::class)->findBy(['user' => $user]);
        $hasChinaApplication = false;
        $hasFranceApplication = false;

        foreach ($applications as $application) {
            if ($application->getIsChina()) {
                $hasChinaApplication = true;
            }
            if ($application->getIsFrance()) {
                $hasFranceApplication = true;
            }
        }

        // Base required documents (always required)
        $requiredDocuments = [
            'passport' => 'Passeport',
            'nationalId' => 'Carte Nationale',
            'cv' => 'CV',
            'guardian1NationalId' => 'Carte Nationale du Tuteur 1',
            'generalTranscript' => 'Relevé de Notes',
            'baccalaureate' => 'Baccalauréat',
            'motivationLetter' => 'Lettre de Motivation',
            'recommendationLetter1' => 'Lettre de Recommandation 1'
        ];

        // Add China-specific required documents
        if ($hasChinaApplication) {
            $requiredDocuments['medicalHealthCheck'] = 'Certificat Médical de Santé';
            $requiredDocuments['anthropometricRecord'] = 'Fiche Anthropométrique (Bonne Conduite)';
        }

        // Add France-specific required documents
        if ($hasFranceApplication && !$hasChinaApplication) {
            $requiredDocuments['frenchTest'] = 'Certificat de Test de Français';
        }

        $validationResult = [
            'isValid' => true,
            'missingDocuments' => [],
            'documentsStatus' => []
        ];

        // --- Ajout d'un mapping multilingue et fonction de recherche ---
        $titleAlternatives = [
            'passport' => ['Passeport', 'Passport'],
            'nationalId' => ['Carte Nationale', 'National ID', 'National ID Card'],
            'cv' => ['CV', 'Curriculum Vitae', 'Resume'],
            'guardian1NationalId' => ['Carte Nationale du Tuteur 1', 'Guardian 1 National ID', 'Guardian 1 ID', 'Guardian ID 1', 'Guardian National ID 1'],
            'generalTranscript' => ['Relevé de Notes', 'General Transcript', 'Transcript', 'Academic Transcript'],
            'baccalaureate' => ['Baccalauréat', 'Baccalaureate Diploma', 'Baccalaureate'],
            'motivationLetter' => ['Lettre de Motivation', 'Motivation Letter'],
            'recommendationLetter1' => ['Lettre de Recommandation 1', 'Recommendation Letter 1'],
            'medicalHealthCheck' => ['Certificat Médical de Santé', 'Medical Health Check'],
            'anthropometricRecord' => ['Fiche Anthropométrique (Bonne Conduite)', 'Anthropometric Record', 'Good Conduct'],
            'frenchTest' => ['Certificat de Test de Français', 'French Test Certificate']
        ];
        $normalize = function(string $s): string {
            $s = mb_strtolower($s, 'UTF-8');
            $s = str_replace([
                'é','è','ê','ë','à','â','ä','ù','û','ü','ô','ö','î','ï','ç'
            ], [
                'e','e','e','e','a','a','a','u','u','u','o','o','i','i','c'
            ], $s);
            return $s;
        };
        $findDocument = function(array $documents, array $alternatives, $normalize) {
            // Exact match
            foreach ($documents as $doc) {
                $docNorm = $normalize($doc->getTitle());
                foreach ($alternatives as $alt) {
                    if ($docNorm === $normalize($alt)) return $doc;
                }
            }
            // Contains match
            foreach ($documents as $doc) {
                $docNorm = $normalize($doc->getTitle());
                foreach ($alternatives as $alt) {
                    if (strpos($docNorm, $normalize($alt)) !== false) return $doc;
                }
            }
            return null;
        };
        // Remplacement du matching par le mapping multilingue
        foreach ($requiredDocuments as $key => $title) {
            $document = null;
            
            $alternatives = $titleAlternatives[$key] ?? [$title];
            // inclure la clé canonique elle-même comme alternative exacte
            array_unshift($alternatives, $key);
            $document = $findDocument($documents, $alternatives, $normalize);

            // Log for debugging
            error_log("Checking document: $key -> $title");
            error_log("Available documents: " . json_encode(array_map(function ($d) {
                return $d->getTitle();
            }, $documents)));
            error_log("Document found: " . ($document ? 'YES' : 'NO'));

            if ($document) {
                $validationResult['documentsStatus'][$key] = [
                    'exists' => true,
                    'title' => $document->getTitle(),
                    'originalLanguage' => $document->getOriginalLanguage(),
                    'validationStatus' => $document->getValidationStatus()
                ];
            } else {
                $validationResult['isValid'] = false;
                $validationResult['missingDocuments'][] = $key;
                $validationResult['documentsStatus'][$key] = [
                    'exists' => false,
                    'title' => $title
                ];
            }
        }

        return new JsonResponse($validationResult);
    }

    #[Route('/documents/normalize-titles', name: 'normalize_document_titles', methods: ['POST'])]
    public function normalizeDocumentTitles(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getProfile();
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found'], Response::HTTP_NOT_FOUND);
        }

        $documents = $this->entityManager->getRepository(Document::class)->findBy(['userProfile' => $userProfile]);

        $canonicalMap = [
            'passport' => ['Passeport', 'Passport'],
            'nationalId' => ['Carte Nationale', 'National ID', 'National ID Card'],
            'cv' => ['CV', 'Curriculum Vitae', 'Resume'],
            'guardian1NationalId' => ['Carte Nationale du Tuteur 1', 'Guardian 1 National ID', 'Guardian 1 ID', 'Guardian ID 1', 'Guardian National ID 1', 'Carte Nationale Tuteur 1'],
            'generalTranscript' => ['Relevé de Notes', 'General Transcript', 'Transcript', 'Academic Transcript'],
            'baccalaureate' => ['Baccalauréat', 'Baccalaureate Diploma', 'Baccalaureate'],
            'motivationLetter' => ['Lettre de Motivation', 'Motivation Letter'],
            'recommendationLetter1' => ['Lettre de Recommandation 1', 'Recommendation Letter 1'],
            'medicalHealthCheck' => ['Certificat Médical de Santé', 'Medical Health Check'],
            'anthropometricRecord' => ['Fiche Anthropométrique (Bonne Conduite)', 'Anthropometric Record', 'Good Conduct'],
            'frenchTest' => ['Certificat de Test de Français', 'French Test Certificate']
        ];
        $canonicalLabels = [
            'passport' => 'Passport',
            'nationalId' => 'National ID Card',
            'cv' => 'Curriculum Vitae (CV)',
            'guardian1NationalId' => 'Guardian 1 National ID',
            'generalTranscript' => 'General Transcript',
            'baccalaureate' => 'Baccalaureate Diploma',
            'motivationLetter' => 'Motivation Letter',
            'recommendationLetter1' => 'Recommendation Letter 1',
            'medicalHealthCheck' => 'Medical Health Check',
            'anthropometricRecord' => 'Anthropometric Record (Good Conduct)',
            'frenchTest' => 'French Test Certificate'
        ];
        $normalize = function(string $s): string {
            $s = mb_strtolower($s, 'UTF-8');
            $s = str_replace(['é','è','ê','ë','à','â','ä','ù','û','ü','ô','ö','î','ï','ç'], ['e','e','e','e','a','a','a','u','u','u','o','o','i','i','c'], $s);
            return $s;
        };
        $guessKey = function(string $title) use ($canonicalMap, $normalize): ?string {
            $t = $normalize($title);
            foreach ($canonicalMap as $key => $alts) {
                foreach ($alts as $alt) {
                    if ($t === $normalize($alt)) return $key;
                }
            }
            foreach ($canonicalMap as $key => $alts) {
                foreach ($alts as $alt) {
                    if (strpos($t, $normalize($alt)) !== false) return $key;
                }
            }
            return null;
        };

        $updated = 0;
        foreach ($documents as $doc) {
            $key = $guessKey($doc->getTitle() ?? '');
            if ($key) {
                if ($doc->getTitle() !== $key) {
                    $doc->setTitle($key);
                    $updated++;
                }
            }
        }
        if ($updated > 0) {
            $this->entityManager->flush();
        }

        return new JsonResponse([
            'success' => true,
            'updated' => $updated
        ]);
    }

    private function createDefaultProfile(User $user): UserProfile
    {
        $profile = new UserProfile();
        $profile->setUser($user); // IMPORTANT: Associate the profile with the user
        $profile->setFirstName('');
        $profile->setLastName('');
        $profile->setPhone('');
        $profile->setWhatsapp('');
        $profile->setNationality(null);
        $profile->setCountry('');
        $profile->setCity('');
        $profile->setAddress('');
        $profile->setPostalCode('');
        $profile->setStudyLevel('');
        $profile->setFieldOfStudy('');
        $profile->setPreferredCountry('');
        $profile->setStartDate('');
        $profile->setPreferredCurrency('');

        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $profile;
    }
}
