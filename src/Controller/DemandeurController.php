<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Demandeur;
use App\Entity\User;
use App\Repository\DemandeurRepository;
use App\Repository\UserRepository;
use App\services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[ApiResource]
class DemandeurController extends AbstractController
{
    #[Route('/api/create-demandeur', name: 'api_demandeur_creation_compte', methods: ['POST'])]
    public function createDemandeur(
        Request $request,
        ValidatorInterface $validator,
        UserRepository $userRepository,
        DemandeurRepository $demandeurRepository,
        MailService $mailService
    ): Response {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;

        // Validation de l'existence de l'email et du téléphone
        if ($demandeurRepository->findOneBy(['email' => $email]) || $userRepository->findOneBy(['email' => $email])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Un utilisateur ou demandeur avec cet email existe déjà.',
            ])->setStatusCode(400);
        }

        if ($demandeurRepository->findOneBy(['phone' => $phone])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Un demandeur avec ce numéro de téléphone existe déjà.',
            ])->setStatusCode(400);
        }

        // Création de l'entité Demandeur
        $demandeur = new Demandeur();
        $code = $demandeur->generateCode();
        $demandeur->setSexe($data['sexe'])
            ->setName($data['name'])
            ->setPhone($phone)
            ->setEmail($email)
            ->setCodeUser($code)
            ->setAdresse($data['adresse'])
            ->setIntitule($data['intitule'])
            ->setProfession($data['profession'])
            ->setLieuNaissance($data['lieu_naissance'])
            ->setPaysResidence($data['pays_residence']);

        // Validation et conversion de la date de naissance
        $dateNaissance = \DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
        if (!$dateNaissance) {
            return $this->json([
                'status' => 'error',
                'message' => 'Format de la date de naissance invalide. Utilisez le format YYYY-MM-DD.',
            ])->setStatusCode(400);
        }
        $demandeur->setDateNaissance($dateNaissance);

        // Création de l'entité User
        $user = new User();
        $user->setUsername($email)
            ->setEmail($email)
            ->setPassword(password_hash($data['password'], PASSWORD_BCRYPT)) // Hachage du mot de passe
            ->setRoles(User::ROLE_DEMANDEUR)
            ->setActiveted(false)
            ->setTokenActiveted(null)
            ->setEnabled(false);

        // Validation des entités

        // Sauvegarde des entités
        $userRepository->save($user, true);
        $demandeur->setCompte($user); // Lien avec l'utilisateur
        $demandeurRepository->save($demandeur, true);

        // Envoi de l'email de confirmation
        $mailService->activerCompteDemandeur($demandeur);


        return $this->json([
            'status' => 'success',
            'message' => 'Demandeur créé avec succès.',
            'demandeur' => $demandeur,
        ], Response::HTTP_CREATED);
    }

    // #[Route('/api/update-demandeur/{id}', name: 'api_demandeur_update', methods: ['PUT'])]
    // public function updateDemandeur(
    //     $id,
    //     Request $request,
    //     UserRepository $userRepository,
    //     DemandeurRepository $demandeurRepository
    // ): Response {
    //     $data = json_decode($request->getContent(), true);
    //     $email = $data['email'] ?? null;
    //     $phone = $data['phone'] ?? null;

    //     $name = $data['name'] ?? null;
    //     $adresse = $data['adresse'] ?? null;
    //     $intitule = $data['intitule'] ?? null;
    //     $dateNaissance = $data['dateNaissance'] ?? null;
    //     $lieuNaissance = $data['lieuNaissance'] ?? null;
    //     $profession = $data['profession'] ?? null;
    //     $sexe = $data['sexe'] ?? null;
    //     $paysResidence = $data['paysResidence'] ?? null;

    //     $demandeurExiste = $demandeurRepository->find($id);
    //     if (!$demandeurExiste) {
    //         return $this->json([
    //             'status' => 'error',
    //             'message' => 'Demandeur non existant.',
    //         ])->setStatusCode(400);
    //     }

    //     // Validate email uniqueness
    //     if (
    //         $email &&
    //         ($demandeurRepository->findOneBy(['email' => $email]) &&
    //             $demandeurExiste->getEmail() !== $email) ||
    //         ($userRepository->findOneBy(['email' => $email]) &&
    //             $demandeurExiste->getEmail() !== $email)
    //     ) {
    //         return $this->json([
    //             'status' => 'error',
    //             'message' => 'Un utilisateur ou demandeur avec cet email existe déjà.',
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     // Validate phone uniqueness
    //     if (
    //         $phone &&
    //         ($demandeurRepository->findOneBy(['phone' => $phone]) &&
    //             $demandeurExiste->getPhone() !== $phone)
    //     ) {
    //         return $this->json([
    //             'status' => 'error',
    //             'message' => 'Un demandeur avec ce numéro de téléphone existe déjà.',
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     if ($demandeurExiste) {
    //         $demandeurExiste
    //             ->setSexe($sexe)
    //             ->setName($name)
    //             ->setPhone($phone)
    //             ->setEmail($email)
    //             ->setAdresse($adresse)
    //             ->setIntitule($intitule)
    //             ->setProfession($profession)
    //             ->setLieuNaissance($lieuNaissance)
    //             ->setPaysResidence($paysResidence);

    //         // Validation et conversion de la date de naissance
    //         $dateNaissance = \DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
    //         if (!$dateNaissance) {
    //             return $this->json([
    //                 'status' => 'error',
    //                 'message' => 'Format de la date de naissance invalide. Utilisez le format YYYY-MM-DD.',
    //             ])->setStatusCode(400);
    //         }
    //         $demandeurExiste->setDateNaissance($dateNaissance);
    //         $demandeurRepository->save($demandeurExiste, true);

    //         return $this->json($demandeurExiste, 200);
    //     } else {
    //         return $this->json([
    //             'status' => 'error',
    //             'message' => 'Demandeur non trouvé.',
    //         ])->setStatusCode(400);
    //     }
    // }

    #[Route('/api/update-demandeur/{id}', name: 'api_demandeur_update', methods: ['PUT'])]
    public function updateDemandeur(
        $id,
        Request $request,
        UserRepository $userRepository,
        DemandeurRepository $demandeurRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $name = $data['name'] ?? null;
        $adresse = $data['adresse'] ?? null;
        $intitule = $data['intitule'] ?? null;
        $dateNaissance = $data['dateNaissance'] ?? null;
        $lieuNaissance = $data['lieuNaissance'] ?? null;
        $profession = $data['profession'] ?? null;
        $sexe = $data['sexe'] ?? null;
        $paysResidence = $data['paysResidence'] ?? null;

        $demandeurExiste = $demandeurRepository->find($id);

        if (!$demandeurExiste) {
            return $this->json([
                'status' => 'error',
                'message' => 'Demandeur non existant.',
            ], Response::HTTP_NOT_FOUND);
        }

        if (
            $email &&
            ($demandeurRepository->findOneBy(['email' => $email]) &&
                $demandeurExiste->getEmail() !== $email) ||
            ($userRepository->findOneBy(['email' => $email]) &&
                $demandeurExiste->getEmail() !== $email)
        ) {
            return $this->json([
                'status' => 'error',
                'message' => 'Un utilisateur ou demandeur avec cet email existe déjà.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate phone uniqueness
        if (
            $phone &&
            ($demandeurRepository->findOneBy(['phone' => $phone]) &&
                $demandeurExiste->getPhone() !== $phone)
        ) {
            return $this->json([
                'status' => 'error',
                'message' => 'Un demandeur avec ce numéro de téléphone existe déjà.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Update demandeur details
        if ($demandeurExiste) {
            // Validation et conversion de la date de naissance
            // if ($dateNaissance) {
            //     // Assuming date is in 'Y-m-d' format
            //     $dateNaissanceObj = \DateTime::createFromFormat('Y-m-d', $dateNaissance);
            //     if (!$dateNaissanceObj) {
            //         return $this->json([
            //             'status' => 'error',
            //             'message' => 'Format de la date de naissance invalide. Utilisez le format YYYY-MM-DD.',
            //         ], Response::HTTP_BAD_REQUEST);
            //     }
            //     // Set date of birth after validation
            //     $demandeurExiste->setDateNaissance($dateNaissanceObj);
            // }

            // Update other fields
            $demandeurExiste
                ->setSexe($sexe)
                ->setName($name)
                ->setPhone($phone)
                ->setEmail($email)
                ->setAdresse($adresse)
                ->setIntitule($intitule)
                ->setProfession($profession)
                ->setLieuNaissance($lieuNaissance)
                ->setPaysResidence($paysResidence);

            // Save updated demandeur
            $demandeurRepository->save($demandeurExiste, true);

            return $this->json($demandeurExiste, Response::HTTP_OK);
        } else {
            return $this->json([
                'status' => 'error',
                'message' => 'Demandeur non trouvé.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/demandeur/{id}', name: 'api_demandeur_delete', methods: ['DELETE'])]
    public function deleteDemandeur($id, DemandeurRepository $demandeurRepository): Response
    {
        $demandeur = $demandeurRepository->find($id);
        if ($demandeur) {
            $demandeurRepository->remove($demandeur, true);
            return $this->json([
                'status' => 'success',
                'message' => 'Demandeur supprimé avec succès.',
            ]);
        } else {
            return $this->json([
                'status' => 'error',
                'message' => 'Demandeur non trouvé.',
            ])->setStatusCode(404);
        }
    }
    #[Route('/api/show-demandeur/{id}', name: 'api_demandeur_show', methods: ['GET'])]
    public function showDemandeur($id, DemandeurRepository $demandeurRepository): Response
    {
        $demandeur = $demandeurRepository->find($id);
        if ($demandeur) {
            return $this->json($demandeur);
        } else {
            return $this->json([
                'status' => 'error',
                'message' => 'Demandeur non trouvé.',
            ])->setStatusCode(404);
        }
    }
}
