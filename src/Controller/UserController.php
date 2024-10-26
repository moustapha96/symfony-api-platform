<?php

// src/Controller/UserController.php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;

use App\Entity\User;
use App\Repository\UserRepository;
use App\services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


// use OpenApi\Annotations as OA;
use OpenApi\Attributes as OA;

#[ApiResource]
class UserController extends AbstractController
{

    #[Route('/api/create-users', name: 'api_user_creation_compte', methods: ['POST'])]
    public function createUser(
        Request $request,
        ValidatorInterface $validator,
        UserRepository $userRepository,
    ): Response {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user_with_email = $userRepository->findOneBy(['email' => $data['email']]);
        if ($user_with_email) {
            return $this->json([
                'status' => 'error',
                'message' => 'Cet utilisateur existe de déjà',
            ])->setStatusCode(400);
        }

        $user->setUsername($data['email']);
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT)); // Assurez-vous de hacher le mot de passe
        $user->setRoles($data['roles'] ?? User::ROLE_ADMIN);
        $user->setActiveted(false);
        $user->setTokenActiveted(null);
        $user->setEnabled(false);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }
        return $this->json($user, Response::HTTP_CREATED);
    }


    #[Route('/api/verifier-compte/{token}', name: 'api_user_verifier_compte', methods: ['GET'])]
    public function verifierCompte(string $token, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['tokenActiveted' => $token]);
        if ($user) {
            $user->setActiveted(true);
            $user->setEnabled(true);
            $user->setTokenActiveted(null);
            $userRepository->save($user, true);
            return $this->json($user, Response::HTTP_OK);
        }
        return $this->json(['message' => 'Token invalide'], Response::HTTP_BAD_REQUEST);
    }

    //fonction pour desactiver un compte
    #[Route('/api/deactiver-compte/{idUser}', name: 'api_user_deactiver_compte', methods: ['GET'])]
    public function deactiverCompte(int $idUser,  UserRepository $userRepository): Response
    {
        $user = $userRepository->find($idUser);
        if ($user) {
            $user->setEnabled(false);
            $userRepository->save($user, true);
            return $this->json($user, Response::HTTP_OK);
        }
        return $this->json(['message' => 'Utilisateur non trouvé'], Response::HTTP_BAD_REQUEST);
    }

    // une fonctio pour permettre à l'utilisateur de modifier ces informations
    #[Route('/api/modifier-compte/{id}', name: 'api_user_modifier_compte', methods: ['PUT'])]
    public function modifierCompte(Request $request, int $id, UserRepository $userRepository, MailService $mailService): Response
    {
        $user = $userRepository->find($id);
        if ($user) {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['email'])) {
                return $this->json(['message' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
            }

            $userRepository->save($user, true);
            return $this->json($user, Response::HTTP_OK);
        }
        return $this->json(['message' => 'Utilisateur non trouvé'], Response::HTTP_BAD_REQUEST);
    }

    // create user with admin role
    #[Route('/api/create-admin', name: 'api_user_creation_admin', methods: ['POST'])]
    public function createUserAdmin(Request $request, ValidatorInterface $validator, UserRepository $userRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();

        if ($userRepository->findOneBy(['email' => $data['email']])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Cet utilisateur existe de déjà',
            ])->setStatusCode(400);
        }

        $user->setRoles(User::ROLE_ADMIN);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $userRepository->save($user, true);
        return $this->json($user, Response::HTTP_CREATED);
    }
}
