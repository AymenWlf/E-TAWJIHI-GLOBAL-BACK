<?php

namespace App\Controller;

use App\Entity\Shortlist;
use App\Entity\Program;
use App\Entity\Establishment;
use App\Repository\ShortlistRepository;
use App\Repository\ProgramRepository;
use App\Repository\EstablishmentRepository;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/api/shortlist')]
class ShortlistController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ShortlistRepository $shortlistRepository,
        private ProgramRepository $programRepository,
        private EstablishmentRepository $establishmentRepository,
        private UserProfileRepository $userProfileRepository
    ) {}

    #[Route('/toggle/program/{id}', name: 'shortlist_toggle_program', methods: ['POST'])]
    public function toggleProgram(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('User must be authenticated');
        }

        $program = $this->programRepository->find($id);
        if (!$program) {
            return new JsonResponse(['error' => 'Program not found'], 404);
        }

        // Get or create user profile
        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found. Please complete your profile first.'], 404);
        }

        $existingShortlist = $this->shortlistRepository->findByUserAndProgram($user, $program);

        if ($existingShortlist) {
            // Remove from shortlist
            $this->entityManager->remove($existingShortlist);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'action' => 'removed',
                'message' => 'Program removed from shortlist'
            ]);
        } else {
            // Add to shortlist
            $shortlist = new Shortlist();
            $shortlist->setUser($user);
            $shortlist->setUserProfile($userProfile);
            $shortlist->setProgram($program);

            $this->entityManager->persist($shortlist);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'action' => 'added',
                'message' => 'Program added to shortlist'
            ]);
        }
    }

    #[Route('/toggle/establishment/{id}', name: 'shortlist_toggle_establishment', methods: ['POST'])]
    public function toggleEstablishment(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('User must be authenticated');
        }

        $establishment = $this->establishmentRepository->find($id);
        if (!$establishment) {
            return new JsonResponse(['error' => 'Establishment not found'], 404);
        }

        // Get or create user profile
        $userProfile = $this->userProfileRepository->findOneBy(['user' => $user]);
        if (!$userProfile) {
            return new JsonResponse(['error' => 'User profile not found. Please complete your profile first.'], 404);
        }

        $existingShortlist = $this->shortlistRepository->findByUserAndEstablishment($user, $establishment);

        if ($existingShortlist) {
            // Remove from shortlist
            $this->entityManager->remove($existingShortlist);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'action' => 'removed',
                'message' => 'Establishment removed from shortlist'
            ]);
        } else {
            // Add to shortlist
            $shortlist = new Shortlist();
            $shortlist->setUser($user);
            $shortlist->setUserProfile($userProfile);
            $shortlist->setEstablishment($establishment);

            $this->entityManager->persist($shortlist);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'action' => 'added',
                'message' => 'Establishment added to shortlist'
            ]);
        }
    }

    #[Route('/check/program/{id}', name: 'shortlist_check_program', methods: ['GET'])]
    public function checkProgram(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['isShortlisted' => false]);
        }

        $program = $this->programRepository->find($id);
        if (!$program) {
            return new JsonResponse(['error' => 'Program not found'], 404);
        }

        $existingShortlist = $this->shortlistRepository->findByUserAndProgram($user, $program);

        return new JsonResponse([
            'isShortlisted' => $existingShortlist !== null
        ]);
    }

    #[Route('/check/establishment/{id}', name: 'shortlist_check_establishment', methods: ['GET'])]
    public function checkEstablishment(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['isShortlisted' => false]);
        }

        $establishment = $this->establishmentRepository->find($id);
        if (!$establishment) {
            return new JsonResponse(['error' => 'Establishment not found'], 404);
        }

        $existingShortlist = $this->shortlistRepository->findByUserAndEstablishment($user, $establishment);

        return new JsonResponse([
            'isShortlisted' => $existingShortlist !== null
        ]);
    }
}
