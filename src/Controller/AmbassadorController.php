<?php

namespace App\Controller;

use App\Entity\Ambassador;
use App\Repository\AmbassadorRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/ambassadors')]
class AmbassadorController extends AbstractController
{
    private AmbassadorRepository $ambassadorRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        AmbassadorRepository $ambassadorRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->ambassadorRepository = $ambassadorRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('', name: 'get_ambassadors', methods: ['GET'])]
    public function getAmbassadors(Request $request): JsonResponse
    {
        try {
            $status = $request->query->get('status', 'active');
            $university = $request->query->get('university', '');
            $search = $request->query->get('search', '');

            $ambassadors = [];

            if (!empty($search)) {
                $ambassadors = $this->ambassadorRepository->search($search);
            } elseif (!empty($university)) {
                $ambassadors = $this->ambassadorRepository->findByUniversity($university);
            } elseif ($status === 'active') {
                $ambassadors = $this->ambassadorRepository->findActive();
            } else {
                $ambassadors = $this->ambassadorRepository->findByStatus($status);
            }

            $formattedAmbassadors = [];
            foreach ($ambassadors as $ambassador) {
                $formattedAmbassadors[] = [
                    'id' => $ambassador->getId(),
                    'university' => $ambassador->getUniversity(),
                    'fieldOfStudy' => $ambassador->getFieldOfStudy(),
                    'studyLevel' => $ambassador->getStudyLevel(),
                    'graduationYear' => $ambassador->getGraduationYear(),
                    'status' => $ambassador->getStatus(),
                    'points' => $ambassador->getPoints(),
                    'referrals' => $ambassador->getReferrals(),
                    'isActive' => $ambassador->getIsActive(),
                    'userName' => $ambassador->getUser() ?
                        $ambassador->getUser()->getFirstName() . ' ' . $ambassador->getUser()->getLastName() : 'Unknown',
                    'userCountry' => $ambassador->getUser() ?
                        $ambassador->getUser()->getCountry() : null,
                    'createdAt' => $ambassador->getCreatedAt()->format('Y-m-d H:i:s'),
                    'startDate' => $ambassador->getStartDate()?->format('Y-m-d H:i:s')
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $formattedAmbassadors,
                'message' => 'Ambassadors retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve ambassadors: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/my', name: 'get_my_ambassador', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMyAmbassador(): JsonResponse
    {
        try {
            $user = $this->getUser();
            $ambassador = $this->ambassadorRepository->findByUser($user);

            if (!$ambassador) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'No ambassador application found'
                ], 404);
            }

            $formattedAmbassador = [
                'id' => $ambassador->getId(),
                'university' => $ambassador->getUniversity(),
                'fieldOfStudy' => $ambassador->getFieldOfStudy(),
                'studyLevel' => $ambassador->getStudyLevel(),
                'graduationYear' => $ambassador->getGraduationYear(),
                'motivation' => $ambassador->getMotivation(),
                'experience' => $ambassador->getExperience(),
                'skills' => $ambassador->getSkills(),
                'socialMedia' => $ambassador->getSocialMedia(),
                'additionalInfo' => $ambassador->getAdditionalInfo(),
                'status' => $ambassador->getStatus(),
                'adminNotes' => $ambassador->getAdminNotes(),
                'interviewDate' => $ambassador->getInterviewDate()?->format('Y-m-d H:i:s'),
                'trainingDate' => $ambassador->getTrainingDate()?->format('Y-m-d H:i:s'),
                'startDate' => $ambassador->getStartDate()?->format('Y-m-d H:i:s'),
                'endDate' => $ambassador->getEndDate()?->format('Y-m-d H:i:s'),
                'points' => $ambassador->getPoints(),
                'referrals' => $ambassador->getReferrals(),
                'isActive' => $ambassador->getIsActive(),
                'createdAt' => $ambassador->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $ambassador->getUpdatedAt()->format('Y-m-d H:i:s')
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $formattedAmbassador,
                'message' => 'Ambassador application retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve ambassador application: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('', name: 'create_ambassador', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createAmbassador(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            // Check if user already has an ambassador application
            $existingAmbassador = $this->ambassadorRepository->findByUser($user);
            if ($existingAmbassador) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'You already have an ambassador application'
                ], 400);
            }

            $ambassador = new Ambassador();
            $ambassador->setUser($user);
            $ambassador->setUniversity($data['university'] ?? '');
            $ambassador->setFieldOfStudy($data['fieldOfStudy'] ?? '');
            $ambassador->setStudyLevel($data['studyLevel'] ?? '');
            $ambassador->setGraduationYear($data['graduationYear'] ?? date('Y') + 1);
            $ambassador->setMotivation($data['motivation'] ?? '');
            $ambassador->setExperience($data['experience'] ?? '');
            $ambassador->setSkills($data['skills'] ?? '');
            $ambassador->setSocialMedia($data['socialMedia'] ?? null);
            $ambassador->setAdditionalInfo($data['additionalInfo'] ?? null);
            $ambassador->setStatus('pending');

            $this->entityManager->persist($ambassador);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'data' => ['id' => $ambassador->getId()],
                'message' => 'Ambassador application submitted successfully'
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to create ambassador application: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'update_ambassador', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateAmbassador(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            $ambassador = $this->ambassadorRepository->find($id);
            if (!$ambassador) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Ambassador application not found'
                ], 404);
            }

            // Check if user owns this ambassador application or is admin
            if ($ambassador->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Only allow certain fields to be updated by users
            if (isset($data['university'])) $ambassador->setUniversity($data['university']);
            if (isset($data['fieldOfStudy'])) $ambassador->setFieldOfStudy($data['fieldOfStudy']);
            if (isset($data['studyLevel'])) $ambassador->setStudyLevel($data['studyLevel']);
            if (isset($data['graduationYear'])) $ambassador->setGraduationYear($data['graduationYear']);
            if (isset($data['motivation'])) $ambassador->setMotivation($data['motivation']);
            if (isset($data['experience'])) $ambassador->setExperience($data['experience']);
            if (isset($data['skills'])) $ambassador->setSkills($data['skills']);
            if (isset($data['socialMedia'])) $ambassador->setSocialMedia($data['socialMedia']);
            if (isset($data['additionalInfo'])) $ambassador->setAdditionalInfo($data['additionalInfo']);

            // Admin-only fields
            if ($this->isGranted('ROLE_ADMIN')) {
                if (isset($data['status'])) $ambassador->setStatus($data['status']);
                if (isset($data['adminNotes'])) $ambassador->setAdminNotes($data['adminNotes']);
                if (isset($data['interviewDate'])) {
                    $ambassador->setInterviewDate($data['interviewDate'] ? new \DateTime($data['interviewDate']) : null);
                }
                if (isset($data['trainingDate'])) {
                    $ambassador->setTrainingDate($data['trainingDate'] ? new \DateTime($data['trainingDate']) : null);
                }
                if (isset($data['startDate'])) {
                    $ambassador->setStartDate($data['startDate'] ? new \DateTime($data['startDate']) : null);
                }
                if (isset($data['endDate'])) {
                    $ambassador->setEndDate($data['endDate'] ? new \DateTime($data['endDate']) : null);
                }
                if (isset($data['points'])) $ambassador->setPoints($data['points']);
                if (isset($data['referrals'])) $ambassador->setReferrals($data['referrals']);
                if (isset($data['isActive'])) $ambassador->setIsActive($data['isActive']);
            }

            $ambassador->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Ambassador application updated successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to update ambassador application: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'delete_ambassador', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteAmbassador(int $id): JsonResponse
    {
        try {
            $user = $this->getUser();
            $ambassador = $this->ambassadorRepository->find($id);

            if (!$ambassador) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Ambassador application not found'
                ], 404);
            }

            // Check if user owns this ambassador application or is admin
            if ($ambassador->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $this->entityManager->remove($ambassador);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Ambassador application deleted successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to delete ambassador application: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/universities', name: 'get_ambassador_universities', methods: ['GET'])]
    public function getUniversities(): JsonResponse
    {
        try {
            $universities = $this->ambassadorRepository->getUniversities();
            $universityList = array_column($universities, 'university');

            return new JsonResponse([
                'success' => true,
                'data' => $universityList,
                'message' => 'Universities retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve universities: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/fields-of-study', name: 'get_ambassador_fields', methods: ['GET'])]
    public function getFieldsOfStudy(): JsonResponse
    {
        try {
            $fields = $this->ambassadorRepository->getFieldsOfStudy();
            $fieldList = array_column($fields, 'fieldOfStudy');

            return new JsonResponse([
                'success' => true,
                'data' => $fieldList,
                'message' => 'Fields of study retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve fields of study: ' . $e->getMessage()
            ], 500);
        }
    }
}
