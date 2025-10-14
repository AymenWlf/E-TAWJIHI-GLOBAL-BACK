<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('/api/auth/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['email']) || !isset($data['password'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Email et mot de passe requis'
                ], 400);
            }

            // Vérifier si l'utilisateur existe déjà
            $existingUser = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $data['email']]);

            if ($existingUser) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Un utilisateur avec cet email existe déjà'
                ], 409);
            }

            // Créer un nouvel utilisateur
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

            // Champs optionnels
            if (isset($data['firstName'])) {
                $user->setFirstName($data['firstName']);
            }
            if (isset($data['lastName'])) {
                $user->setLastName($data['lastName']);
            }

            // Valider l'entité
            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $errorMessages
                ], 400);
            }

            // Sauvegarder l'utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Générer le token JWT
            $token = $this->jwtManager->create($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => [
                    'user' => [
                        'id' => $user->getId(),
                        'email' => $user->getEmail(),
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                        'roles' => $user->getRoles()
                    ],
                    'token' => $token
                ]
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/auth/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['email']) || !isset($data['password'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Email et mot de passe requis'
                ], 400);
            }

            // Trouver l'utilisateur
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $data['email']]);

            if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ], 401);
            }

            // Générer le token JWT
            $token = $this->jwtManager->create($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => [
                        'id' => $user->getId(),
                        'email' => $user->getEmail(),
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                        'roles' => $user->getRoles()
                    ],
                    'token' => $token
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/auth/me', name: 'app_auth_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'roles' => $user->getRoles()
                ]
            ]
        ]);
    }

    #[Route('/api/auth/refresh', name: 'app_auth_refresh', methods: ['POST'])]
    public function refresh(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Générer un nouveau token
        $token = $this->jwtManager->create($user);

        return new JsonResponse([
            'success' => true,
            'message' => 'Token rafraîchi avec succès',
            'data' => [
                'token' => $token
            ]
        ]);
    }
}
