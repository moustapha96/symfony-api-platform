<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Transaction;
use App\Repository\DemandeRepository;
use App\Repository\DemandeurRepository;
use App\Repository\InstitutRepository;
use App\Repository\PaymentRepository;
use App\Repository\TransactionRepository;
use App\services\MailService;
use Stripe\Service\PaymentIntentService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $mailer;
    function __construct(MailService $mailService)
    {
        $this->mailer = $mailService;
    }
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('api/create-payment', name: 'app_create_payment', methods: ['POST'])]
    public function createPayment(
        Request $request,
        DemandeRepository $demandeRepository,
        DemandeurRepository $demandeurRepository,
        InstitutRepository $institutRepository,
        TransactionRepository $transactionRepository
    ): Response {

        $json = json_decode($request->getContent(), true);

        // Validate required fields
        $demande_id = $json['demande_id'] ?? null;
        $type = $json['type'] ?? null;
        $institut_id = $json['institut_id'] ?? null;
        $demandeur_id = $json['demandeur_id'] ?? null;

        if (!$demande_id || !$type) {
            return new JsonResponse(['message' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
        }

        // Find the associated Demande
        $demande = $demandeRepository->find($demande_id);
        if (!$demande) {
            return new JsonResponse(['message' => 'Demande not found'], Response::HTTP_NOT_FOUND);
        }

        $transaction = new Transaction();
        $transaction->setDateTransaction(new \DateTime())
            ->setDemande($demande)
            ->setTypePaiement(Transaction::TYPE_STRIPE)
            ->setEtat(Transaction::ETAT_PENDING);

        if ($type === Transaction::TRANSACTION_ABONNEMENT) {
            // Handle subscription payment
            $institut = $institutRepository->find($institut_id);
            if (!$institut) {
                return new JsonResponse(['message' => 'Institut not found'], Response::HTTP_NOT_FOUND);
            }

            $amount = 100;
            $product_name =
                Transaction::TRANSACTION_ABONNEMENT;
            $transaction->setTypeTransaction(Transaction::TRANSACTION_ABONNEMENT);
        } elseif ($type === Transaction::TRANSACTION_VERIFICATION) {
            $demandeur = $demandeurRepository->find($demandeur_id);
            if (!$demandeur) {
                return new JsonResponse(['message' => 'Demandeur not found'], Response::HTTP_NOT_FOUND);
            }
            $amount = 50;
            $product_name = Transaction::TRANSACTION_VERIFICATION;
            $transaction->setTypeTransaction(Transaction::TRANSACTION_VERIFICATION);
        } else {
            return new JsonResponse(['message' => 'Invalid transaction type'], Response::HTTP_BAD_REQUEST);
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product_name,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => sprintf('%s/payment/success/%s', $_ENV['APP_URL'], $transaction->getId()),
                'cancel_url' => sprintf('%s/payment/failed/%s', $_ENV['APP_URL'], $transaction->getId()),
            ]);

            $transaction->setMontant($amount)
                ->setIsDeleted(false);

            $transactionRepository->save($transaction, true);
            return new JsonResponse(['url' => $session->url], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Payment creation failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/payment/failed/{transactionId}', name: 'app_payment_failed', methods: ['GET'])]
    public function failed($transactionId, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->find($transactionId);
        if (!$transaction) {
            return new JsonResponse(['message' => 'Transaction not found'], 404);
        }

        $demande = $transaction->getDemande();
        $demandeur = $demande->getDemandeur();

        // Update transaction status to failed
        $transaction->setEtat(Transaction::ETAT_FAILED);
        $transactionRepository->save($transaction, true);

        // Prepare error message (this could be more detailed based on your logic)
        $errorMessage = 'Le paiement a échoué. Veuillez vérifier vos informations ou réessayer plus tard.';

        // Send failure email with details
        $this->mailer->sendPaymentFailureEmail($demandeur, $errorMessage);

        return $this->render('payment/failed.html.twig', [
            'controller_name' => 'PaymentController',
            'transactionId' => $transactionId,
            'errorMessage' => $errorMessage,
            'montant' => $transaction->getMontant(),
            'dateTransaction' => $transaction->getDateTransaction()->format('d/m/Y H:i:s'),
            'typePaiement' => $transaction->getTypePaiement(),
            'typeTransaction' => $transaction->getTypeTransaction(),
        ]);
    }
    #[Route('/api/payment/success/{transactionId}', name: 'app_payment_success', methods: ['GET'])]
    public function success($transactionId, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->find($transactionId);
        if (!$transaction) {
            return new JsonResponse(['message' => 'Transaction not found'], 404);
        }

        $demande = $transaction->getDemande();
        $demandeur = $demande->getDemandeur();

        // Update transaction status to paid
        $transaction->setEtat(Transaction::ETAT_PAID);
        $transactionRepository->save($transaction, true);

        $this->mailer->sendPaymentSuccessEmail($demandeur, $transaction);

        return $this->render('payment/success.html.twig', [
            'controller_name' => 'PaymentController',
            'transactionId' => $transactionId,
            'montant' => $transaction->getMontant(),
            'dateTransaction' => $transaction->getDateTransaction()->format('d/m/Y H:i:s'),
            'typePaiement' => $transaction->getTypePaiement(),
            'typeTransaction' => $transaction->getTypeTransaction(),
        ]);
    }

    // get payments by demandeur

    #[Route('/api/payments/demandeur/{demandeurId}', name: 'app_payment_by_demandeur', methods: ['GET'])]
    public function getPaymentsByDemandeur($demandeurId, PaymentRepository $paymentRepository, DemandeRepository $demandeRepository,  DemandeurRepository $demandeurRepository): Response
    {
        $demandeur = $demandeurRepository->find($demandeurId);
        if (!$demandeur) {
            return $this->json(['message' => 'Demandeur not found'], Response::HTTP_NOT_FOUND);
        }
        $demandes = $demandeRepository->findBy(['demandeur' => $demandeur]);
        if (!$demandes) {
            return $this->json([], Response::HTTP_OK);
        }
        $payment = [];
        // dd($demandes);
        foreach ($demandes as $demande) {
            if ($demande) {
                $p = $paymentRepository->findBy(['demande' => $demande]);
                foreach ($p as  $value) {
                    $data = [
                        'updatedAt' => $value->getUpdatedAt(),
                        'createdAt' => $value->getCreatedAt(),
                        'amount' => $value->getAmount(),
                        'currency' => $value->getCurrency(),
                        'paymentIntentId' => $value->getPaymentIntentId(),
                        'status' => $value->getStatus(),
                        'id' => $value->getId(),
                        'demande' => [
                            'id' => $value->getDemande()->getId(),
                            'intitule' => $value->getDemande()->getIntitule(),
                            'dateDemande' => $value->getDemande()->getDateDemande()->format('Y-m-d H:i:s'),
                            'resultat' => $value->getDemande()->getResultat(),
                            'isDeleted' => $value->getDemande()->isIsDeleted(),
                        ],
                    ];

                    array_push($payment, $data);
                }
            }
        }
        return $this->json($payment, Response::HTTP_OK);
    }
}
