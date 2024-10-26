<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Institut;
use App\Entity\User;
use App\Repository\DocumentRepository;
use App\Repository\InstitutRepository;
use App\Repository\UserRepository;
use App\services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class InstitutController extends AbstractController
{
    #[Route('/api/create-institut', name: 'api_institut_creation_compte', methods: ['POST'])]
    public function createInstitut(
        Request $request,
        UserRepository $userRepository,
        InstitutRepository $institutRepository,
        MailService $mailService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;

        // Validation de l'existence de l'email et du téléphone
        if ($institutRepository->findOneBy(['email' => $email]) || $userRepository->findOneBy(['email' => $email])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Un utilisateur ou institut avec cet email existe déjà.',
            ])->setStatusCode(400);
        }

        if ($institutRepository->findOneBy(['phone' => $phone])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Un institut avec ce numéro de téléphone existe déjà.',
            ])->setStatusCode(400);
        }

        // Création de l'entité Institut
        $institut = new Institut();
        $code = $institut->generateCode();


        $institut->setName($data['name'])
            ->setType($data['type'])
            ->setPhone($phone)
            ->setEmail($email)
            ->setCodeUser($code)
            ->setAdresse($data['adresse'])
            ->setIntitule($data['intitule'])
            ->setPaysResidence($data['pays_residence'])
            ->setSiteWeb($data['siteWeb'] ?? null);

        $institutRepository->save($institut, true);
        // Ajout du logo si fourni
        if (isset($data['logo'])) {
            $institut->setLogo($data['logo']);
        }

        // Création de l'entité User
        $user = new User();
        $user->setUsername($email)
            ->setEmail($email)
            ->setPassword(password_hash($data['password'], PASSWORD_BCRYPT)) // Hachage du mot de passe
            ->setRoles(User::ROLE_INSTITUT)
            ->setActiveted(false)
            ->setTokenActiveted(null)
            ->setEnabled(true)
            ->setInstitut($institut);

        $institut->setCompte($user);

        $userRepository->save($user, true);

        $mailService->activerCompteInstitut($institut);

        return $this->json([
            'status' => 'success',
            'message' => 'Institut créé avec succès.',
            'institut' => $institut,
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/show-institut/{id}', name: 'api_institut_show', methods: ['GET'])]
    public function getInstitut($id, InstitutRepository $institutRepository): Response
    {
        $institut = $institutRepository->find($id);
        if ($institut) {
            return $this->json(
                $institut,
            );
        } else {
            return $this->json([
                'status' => 'error',
                'message' => 'Institut non trouvé',
            ])->setStatusCode(404);
        }
    }
    #[Route('/api/update-institut/{id}', name: 'api_institut_update', methods: ['PUT'])]
    public function updateInstitut($id, Request $request, InstitutRepository $institutRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $institut = $institutRepository->find($id);
        if ($institut) {
            $institut->setName($data['name'])
                ->setAdresse($data['adresse'])
                ->setIntitule($data['intitule'])
                ->setPaysResidence($data['paysResidence'])
                ->setSiteWeb($data['siteWeb'] ?? null)
                ->setPhone($data['phone'] ?? null)
                ->setType($data['type']);
            if (isset($data['logo'])) {
                $institut->setLogo($data['logo']);
            }
            $institutRepository->save($institut, true);
            return $this->json([
                'status' => 'success',
                'message' => 'Institut mis à jour',
                'institut' => $institut,
            ]);
        } else {
            return $this->json([
                'status' => 'error',
                'message' => 'Institut non trouvé',
            ])->setStatusCode(404);
        }
    }

    // liste des instituts
    #[Route('/api/liste-institut', name: 'api_institut_liste', methods: ['GET'])]
    public function listInstitut(InstitutRepository $institutRepository): Response
    {
        $instituts = $institutRepository->findAll();
        $datas = [];
        foreach ($instituts as $institut) {
            $datas[] = [
                'id' => $institut->getId(),
                'type' => $institut->getType(),
                'name' => $institut->getName(),
                'phone' => $institut->getPhone(),
                'email' => $institut->getEmail(),
                'adresse' => $institut->getAdresse(),
                'intitule' => $institut->getIntitule(),
                'pays_residence' => $institut->getPaysResidence(),
                'siteWeb' => $institut->getSiteWeb(),
                'logo' => $institut->getLogo(),
            ];
        }
        return $this->json($datas);
    }

    // get institut by codeUser
    #[Route('/api/institut/bycodeUser/{codeUser}', name: 'api_get_institut_by_codeUser', methods: ['GET'])]
    public function getInstitutByCodeUser($codeUser, InstitutRepository $institutRepository): Response
    {
        $institut = $institutRepository->findOneBy(['codeUser' => $codeUser]);
        if ($institut) {
            return $this->json($institut);
        } else {
            return $this->json(['message' => 'Institut not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
