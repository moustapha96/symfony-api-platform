<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\DemandeRepository;
use App\Repository\DemandeurRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class TransactionController extends AbstractController
{

    #[Route('/api/transactions', name: 'api_create_transaction', methods: ['POST'])]
    public function create(
        Request $request,
        UserRepository $userRepository,
        DemandeRepository $demandeRepository,
        TransactionRepository $transactionRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['montant']) || !isset($data['typePaiement']) || !isset($data['typeTransaction']) || !isset($data['user_id'])) {
            return $this->json([
                'status' => 400,
                'message' => 'montant, typePaiement, typeTransaction et user_id sont requis'
            ], Response::HTTP_BAD_REQUEST);
        }
        $user = $userRepository->find($data['user_id']);
        if (!$user) {
            return $this->json([
                'status' => 404,
                'message' => 'Utilisateur non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }
        $demande = $demandeRepository->find($data['demande_id']);
        if (!$demande) {
            return $this->json([
                'status' => 404,
                'message' => 'Demande non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }

        $transaction = new Transaction();
        $transaction->setDemande($demande);
        $transaction->setMontant($data['montant']);
        $transaction->setDateTransaction(new \DateTime());
        $transaction->setTypePaiement($data['typePaiement']);
        $transaction->setTypeTransaction($data['typeTransaction']);

        $transactionRepository->save($transaction, true);

        return $this->json($transaction, Response::HTTP_CREATED);
    }

    // get transaction by id demandeur
    #[Route('/api/transactions/demandeur/{id}', name: 'api_get_transaction_by_demandeur', methods: ['GET'])]
    public function getTransactionByDemandeur(
        int $id,
        EntityManagerInterface $entityManager,
    ): Response {
        $qb = $entityManager->createQueryBuilder();
        $qb->select('t')
            ->from('App\Entity\Transaction', 't')
            ->join('t.demande', 'd')
            ->join('d.demandeur', 'dem')
            ->where('dem.id = :demandeurId')
            ->setParameter('demandeurId', $id);

        $transactions = $qb->getQuery()->getResult();

        if (empty($transactions)) {
            return $this->json(['message' => 'Aucune transaction trouvée pour ce demandeur'], Response::HTTP_OK);
        }

        return $this->json($transactions, Response::HTTP_OK, [], ['groups' => 'transaction:list']);
    }
}
