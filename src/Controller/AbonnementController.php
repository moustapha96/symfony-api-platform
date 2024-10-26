<?php

namespace App\Controller;

use App\Repository\InstitutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementController extends AbstractController
{
    //    find abonnements by institut
    #[Route('/api/abonnements/institut/actif/{id}', name: 'api_abonnements_institut_actif', methods: ['GET'])]
    public function getAbonnementsByInstitutActif($id, InstitutRepository $institutRepository): Response
    {
        $institut = $institutRepository->find($id);

        if (!$institut) {
            return $this->json(['message' => 'Institut non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $abonnements = $institut->getAbonnements();

        if ($abonnements->isEmpty()) {
            return $this->json(null, Response::HTTP_OK);
        }

        $now = new \DateTime();

        // Filtrer les abonnements valides (non expirés)
        $abonnementsActifs = $abonnements->filter(function ($abonnement) use ($now) {
            return $abonnement->getDateExpiration() > $now;
        });

        if ($abonnementsActifs->isEmpty()) {
            return $this->json(['message' => 'Cet institut n\'a pas d\'abonnements actifs'], Response::HTTP_OK);
        }

        return $this->json($abonnementsActifs, Response::HTTP_OK);
    }

    // find abonnement by id institut
    #[Route('/api/abonnements/institut/{id}', name: 'api_abonnement_institut', methods: ['GET'])]
    public function getAbonnementByInstitut($id, InstitutRepository $institutRepository): Response
    {
        $institut = $institutRepository->find($id);

        if (!$institut) {
            return $this->json(['message' => 'Institut non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $abonnements = $institut->getAbonnements();

        if ($abonnements->isEmpty()) {
            return $this->json([], Response::HTTP_OK);
        }

        return $this->json($abonnements, Response::HTTP_OK);
    }
}
