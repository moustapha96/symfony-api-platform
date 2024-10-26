<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Demande;
use App\Entity\Document;
use App\Entity\Payment;
use App\Repository\DemandeRepository;
use App\Repository\DemandeurRepository;
use App\Repository\DocumentRepository;
use App\Repository\InstitutRepository;
use App\Repository\PaymentRepository;
use App\services\MailService;
use PDO;
use Stripe\PaymentIntent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[ApiResource]
class DemandeController extends AbstractController
{
    // #[Route('/api/create-demande', name: 'api_demande_creation_compte', methods: ['POST'])]
    // public function createDemande(
    //     Request $request,
    //     ValidatorInterface $validator,
    //     DemandeRepository $demandeRepository,
    //     DemandeurRepository $demandeurRepository,
    //     InstitutRepository $institutRepository,
    //     MailService $mailService
    // ): Response {
    //     $data = json_decode($request->getContent(), true);

    //     // Récupération des données
    //     $demandeurId = $data['demandeur_id'];
    //     $resultat = $data['resultat'];
    //     $intitule = $data['intitule'];
    //     $dateDemande = new \DateTimeImmutable();
    //     $paysInstitut = $data['paysInstitut'];
    //     $emailInstitut = $data['emailInstitut'];
    //     $nameInstitut = $data['nameInstitut'];
    //     $phoneInstitut = $data['phoneInstitut'];
    //     $adresseInstitut = $data['adresseInstitut'];
    //     $anneeObtentiont = $data['anneeObtention'];
    //     $institutDemandeur_id = $data['institutDemandeur_id'];

    //     $institutDemandeur = $institutRepository->find($institutDemandeur_id);
    //     if (!$institutDemandeur) {
    //         return $this->json(['error' => 'Institut demandeur not found'], Response::HTTP_NOT_FOUND);
    //     }
    //     $demandeur = $demandeurRepository->find($demandeurId);
    //     if (!$demandeur) {
    //         return $this->json(['error' => 'Demandeur not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     // Création de la demande
    //     $demande = new Demande();
    //     $demande->setDateDemande($dateDemande);
    //     $demande->setIntitule($intitule);
    //     $demande->setResultat($resultat);
    //     $demande->setDemandeur($demandeur);
    //     $demande->setAnneeObtention($anneeObtentiont);
    //     $demande->setInstitutDemandeur($institutDemandeur);

    //     // Vérification si l'institut existe
    //     $institut = $institutRepository->findOneBy(['email' => $emailInstitut]);
    //     if ($institut) {
    //         // Si l'institut existe, on le lie à la demande et on efface les champs liés à l'institut
    //         $demande->setInstitut($institut);
    //         $demande->setPaysInstitut('');
    //         $demande->setEmailInstitut('');
    //         $demande->setNameInstitut('');
    //         $demande->setPhoneInstitut('');
    //         $demande->setAdresseInstitut('');
    //     } else {
    //         // Si l'institut n'existe pas, on remplit les informations liées à l'institut manuellement
    //         $demande->setInstitut(null);
    //         $demande->setPaysInstitut($paysInstitut);
    //         $demande->setEmailInstitut($emailInstitut);
    //         $demande->setNameInstitut($nameInstitut);
    //         $demande->setPhoneInstitut($phoneInstitut);
    //         $demande->setAdresseInstitut($adresseInstitut);
    //     }

    //     // Validation des erreurs
    //     $errors = $validator->validate($demande);
    //     if (count($errors) > 0) {
    //         return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
    //     }

    //     $demandeRepository->save($demande, true);
    //     if ($demande) {
    //         if ($demande->getInstitut()) {
    //             $mailService->sendDemandeToExistingInstitut($institut, $demande, $institutDemandeur);
    //         } else {
    //             $mailService->sendInvitationToInstitutForAuthentication($emailInstitut, $nameInstitut,  $demande, $institutDemandeur);
    //         }
    //         $mailService->sendConfirmationToDemandeur($demandeur, $demande);

    //         return $this->json(['message' => 'Demande created successfully'], Response::HTTP_CREATED);
    //     } else {
    //         return $this->json(['message' => 'Demande not created'], Response::HTTP_BAD_REQUEST);
    //     }
    // }
    #[Route('/api/create-demande', name: 'api_demande_creation_compte', methods: ['POST'])]
    public function createDemande(
        Request $request,
        ValidatorInterface $validator,
        DemandeRepository $demandeRepository,
        DemandeurRepository $demandeurRepository,
        InstitutRepository $institutRepository,
        MailService $mailService,
        PaymentRepository $paymentRepository,

    ): Response {


        $data = json_decode($request->getContent(), true);

        // Extract data from request
        $demandeurId = $data['demandeur_id'];
        $resultat = $data['resultat'];
        $intitule = $data['intitule'];
        $dateDemande = new \DateTimeImmutable();
        $paysInstitut = $data['paysInstitut'];
        $emailInstitut = $data['emailInstitut'];
        $nameInstitut = $data['nameInstitut'];
        $phoneInstitut = $data['phoneInstitut'];
        $adresseInstitut = $data['adresseInstitut'];
        $anneeObtention = $data['anneeObtention'];
        $institutDemandeur_id = $data['institutDemandeur'];
        $institutId = $data['institutId'];
        $clientSecret = $data['clientSecret'];
        $amount = $data['amount'];
        $typePaiement = $data['typePaiement'];
        $paymentInfo = $data['paymentInfo'];
        $statustPaiement = $paymentInfo['status'] ?? null;

        $institutDemandeur = $institutRepository->find($institutDemandeur_id);
        $demandeur = $demandeurRepository->find($demandeurId);

        if (!$institutDemandeur || !$demandeur) {
            return $this->json(['error' => 'Institut demandeur or Demandeur not found'], Response::HTTP_NOT_FOUND);
        }

        $currency = 'eur';
        $payment = new Payment();
        $payment->setAmount($amount);
        $payment->setCurrency($currency);
        $payment->setUserId($demandeur->getId());
        $payment->setCreatedAt(new \DateTime());
        $payment->setPaymentInfo($paymentInfo);
        $payment->setTypePaiement($typePaiement);


        if ($typePaiement === "PayPal") {
            $payment->setPaymentIntentId($paymentInfo['id']);
            $payment->setStatus($paymentInfo['status']);
        } else if ($typePaiement === "Stripe") {
            $payment->setPaymentIntentId($clientSecret);
            $payment->setStatus("pending"); // Stripe payments start as pending
        } else {
            return $this->json(['error' => 'Invalid payment type'], Response::HTTP_BAD_REQUEST);
        }
        $paymentRepository->save($payment, true);

        $demande = new Demande();
        $demande->setStatusPayment("pending");
        $demande->setDateDemande($dateDemande);
        $demande->setIntitule($intitule);
        $demande->setResultat($resultat);
        $demande->setDemandeur($demandeur);
        $demande->setAnneeObtention($anneeObtention);
        $demande->setInstitutDemandeur($institutDemandeur);

        if ($payment) {
            $demande->setPayment($payment);
        }
        $institut = $institutRepository->find($institutId);

        if ($institut) {
            $demande->setInstitut($institut);
            $demande->setPaysInstitut($institut->getPaysResidence());
            $demande->setEmailInstitut($institut->getEmail());
            $demande->setNameInstitut($institut->getName());
            $demande->setPhoneInstitut($institut->getPhone());
            $demande->setAdresseInstitut($institut->getAdresse());
        } else {
            $demande->setPaysInstitut($paysInstitut);
            $demande->setEmailInstitut($emailInstitut);
            $demande->setNameInstitut($nameInstitut);
            $demande->setPhoneInstitut($phoneInstitut);
            $demande->setAdresseInstitut($adresseInstitut);
        }

        $errors = $validator->validate($demande);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // Save entity
        $demandeRepository->save($demande, true);

        // Send emails
        if ($demande->getInstitut()) {
            $mailService->sendDemandeToExistingInstitut($institut, $demande, $institutDemandeur);
        } else {
            $mailService->sendInvitationToInstitutForAuthentication($emailInstitut, $nameInstitut, $demande, $institutDemandeur);
        }
        $mailService->sendConfirmationToDemandeur($demandeur, $demande);

        return $this->json(['message' => 'Demande created successfully'], Response::HTTP_CREATED);
    }


    #[Route('/api/demandes/{demandeId}', name: 'api_get_demande', methods: ['GET'])]
    public function getDemandes($demandeId, DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->find($demandeId);
        if ($demandes) {
            return $this->json($demandes, Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'No demandes found'], Response::HTTP_NOT_FOUND);
        }
    }
    #[Route('/api/demandes/{demandeId}', name: 'api_delete_demande', methods: ['DELETE'])]
    public function deleteDemande($demandeId, DemandeRepository $demandeRepository): Response
    {
        $demande = $demandeRepository->find($demandeId);
        if ($demande) {
            $demandeRepository->remove($demande, true);
            return $this->json(['message' => 'Demande deleted successfully'], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Demande not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/demandes/{demandeId}', name: 'api_update_demande', methods: ['PUT'])]
    public function updateDemande($demandeId, Request $request, DemandeRepository $demandeRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $demande = $demandeRepository->find($demandeId);
        if ($demande) {
            $demande->setResultat($data['resultat']);
            $demandeRepository->save($demande, true);
            return $this->json(['message' => 'Demande updated successfully'], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Demande not found'], Response::HTTP_NOT_FOUND);
        }
    }
    #[Route('/api/demandes/{demandeId}/accept', name: 'api_accept_demande', methods: ['PUT'])]
    public function acceptDemande($demandeId, DemandeRepository $demandeRepository): Response
    {
        $demande = $demandeRepository->find($demandeId);
        if ($demande) {
            $demande->setResultat("Accepted");
            $demandeRepository->save($demande, true);
            return $this->json(['message' => 'Demande accepted successfully'], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Demande not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/demandes/{demandeId}/reject', name: 'api_reject_demande', methods: ['PUT'])]
    public function rejectDemande($demandeId, DemandeRepository $demandeRepository): Response
    {
        $demande = $demandeRepository->find($demandeId);
        if ($demande) {
            $demande->setResultat("Rejected");
            $demandeRepository->save($demande, true);
            return $this->json(['message' => 'Demande rejected successfully'], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Demande not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/demandes/demandeur/{demandeurId}', name: 'api_get_demandes_by_demandeur', methods: ['GET'])]
    public function getDemandesByDemandeur($demandeurId, DemandeurRepository $demandeurRepository, DemandeRepository $demandeRepository): Response
    {
        $demandeur = $demandeurRepository->find($demandeurId);
        if ($demandeur) {
            $demandes = $demandeRepository->findBy(['demandeur' => $demandeur]);
            if ($demandes) {
                return $this->json($demandes, Response::HTTP_OK);
            } else {
                return $this->json([], Response::HTTP_OK);
            }
        } else {
            return $this->json([], Response::HTTP_OK);
        }
    }

    #[Route('/api/demandes/institut/{institutId}', name: 'api_get_demandes_by_institut', methods: ['GET'])]
    public function getDemandesByInstitut($institutId, InstitutRepository $institutRepository, DemandeRepository $demandeRepository): Response
    {
        $institut = $institutRepository->find($institutId);
        if ($institut) {
            $demandes = $demandeRepository->findBy(['institut' => $institut]);
            if ($demandes) {
                return $this->json($demandes, Response::HTTP_OK);
            } else {
                return $this->json([], Response::HTTP_OK);
            }
        } else {
            return $this->json([], Response::HTTP_OK);
        }
    }

    #[Route('/api/verifier-demande/{id}', name: 'api_verifier_demande', methods: ['POST'])]
    public function verifierDemande(
        Request $request,
        DemandeRepository $demandeRepository,
        DocumentRepository $documentRepository,
        ValidatorInterface $validator
    ): Response {
        $demandeId = $request->attributes->get('id');
        $demande = $demandeRepository->find($demandeId);

        if (!$demande) {
            return $this->json(['error' => 'Demande non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Ajouter le document à la demande
        if (isset($data['document'])) {
            $document = new Document();

            // Remplir les informations du document
            $document->setTypeDocument($data['document']['typeDocument']);
            $document->setIntitule($data['document']['intitule']);
            $document->setUrl($data['document']['url']);
            $document->setDateObtention(new \DateTimeImmutable()); // Ou une date fournie dans le payload
            $document->setAnneeObtention(date('Y')); // Ou une année fournie dans le payload

            $demandeur = $demande->getDemandeur(); // Récupérer le demandeur associé à la demande

            // Sauvegarder le document
            $documentRepository->save($document, true);
        }

        $errors = $validator->validate($demande);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        if ($document) {
            $demande->setResultat("Verified");
            $demandeRepository->save($demande, true);
            return $this->json(['message' => 'Demande vérifiée avec succès'], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Document non fourni'], Response::HTTP_BAD_REQUEST);
        }
    }
}
