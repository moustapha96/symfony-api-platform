<?php

namespace App\Controller;

use App\Entity\Demandeur;
use App\Entity\Payment;
use App\Repository\DemandeRepository;
use App\Repository\DemandeurRepository;
use App\Repository\InstitutRepository;
use App\Repository\PaymentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends AbstractController
{

    private $parameterBag;
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        Stripe::setApiKey($this->parameterBag->get('STRIPE_SECRET_KEY'));
    }

    #[Route('/api/publishable-key', name: 'api_stripe_key', methods: ['GET'])]
    public function publishableKey(): JsonResponse
    {
        return new JsonResponse(['publishable_key' =>  $this->parameterBag->get('STRIPE_PUBLISHABLE_KEY')]);
    }

    #[Route('/api/create-payment-intent-institut', name: 'api_create_payment_intent_institut', methods: ['POST'])]
    public function createPaymentIntentInstitut(Request $request, PaymentRepository $paymentRepository, InstitutRepository $institutRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'];
        $userId = $data['userId'];

        $user = $institutRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'user not found'], 404);
        }
        $currency = 'eur';
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => ['bancontact', 'card'],
            ]);

            // Sauvegarder les informations du paiement dans la base de données
            $payment = new Payment();
            $payment->setUserId($user->getId());
            $payment->setAmount($amount);
            $payment->setCurrency($currency);
            $payment->setPaymentIntentId($paymentIntent->id);
            $payment->setStatus('pending');
            $payment->setCreatedAt(new \DateTime());
            $paymentRepository->save($payment, true);

            return new JsonResponse(['client_secret' => $paymentIntent->client_secret]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 200);
        }
    }


    #[Route('/api/create-payment-intent-demandeur', name: 'api_create_payment_intent_demandeur', methods: ['POST'])]
    public function createPaymentIntentDemandeur(Request $request, PaymentRepository $paymentRepository, DemandeurRepository $demandeurRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'];
        $userId = $data['userId'];

        $user = $demandeurRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'user not found'], 404);
        }

        try {
            $currency = 'eur';
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => ['bancontact', 'card'],
            ]);

            // Sauvegarder les informations du paiement dans la base de données
            // $payment = new Payment();
            // $payment->setUserId($user->getId());
            // $payment->setAmount($amount);
            // $payment->setCurrency($currency);
            // $payment->setPaymentIntentId($paymentIntent->id);
            // $payment->setStatus('pending');
            // $payment->setCreatedAt(new \DateTime());
            // $paymentRepository->save($payment, true);

            return new JsonResponse(['client_secret' => $paymentIntent->client_secret, 'payment_intent_id' => $paymentIntent->id]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/update-payment-intent-demandeur', name: 'api_update_payment_status_demandeur', methods: ['POST'])]
    public function updatePaymentStatusDemandeur(
        Request $request,
        DemandeRepository $demandeRepository,
        PaymentRepository $paymentRepository,
        DemandeurRepository $demandeurRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $paymentIntentId = $data['paymentIntentId'];
        $status = $data['status'];
        $demandeurId = $data['demandeurId'];

        $user = $demandeurRepository->find($demandeurId);
        $payment = $paymentRepository->findOneBy(['paymentIntentId' => $paymentIntentId, 'userId' => $user->getId()]);
        if (!$payment) {
            return new JsonResponse(['error' => 'Payment not found'], 404);
        }

        $demande = $demandeRepository->findOneBy(['payment' => $payment]);
        if (!$demande) {
            return new JsonResponse(['error' => 'Demande not found'], 404);
        }
        $demande->setStatusPayment("Paid");
        $demandeRepository->save($demande, true);
        $payment->setStatus($status);
        $payment->setUpdatedAt(new \DateTime());
        $paymentRepository->save($payment, true);
        return new JsonResponse(['message' => 'Payment status updated'], 200);
    }

    #[Route('/api/update-payment-intent-institut', name: 'api_update_payment_status_institut', methods: ['POST'])]
    public function updatePaymentStatusInstitut(Request $request, PaymentRepository $paymentRepository, InstitutRepository $institutRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $paymentIntentId = $data['paymentIntentId'];
        $status = $data['status'];
        $institutId = $data['institutId'];

        $user = $institutRepository->find($institutId);
        // Trouver le paiement correspondant à cet intent
        $payment = $paymentRepository->findOneBy(['paymentIntentId' => $paymentIntentId, 'userId' => $user->getId()]);
        if (!$payment) {
            return new JsonResponse(['error' => 'Payment not found'], 404);
        }
        // Mettre à jour le statut du paiement
        $payment->setStatus($status);
        $payment->setUpdatedAt(new \DateTime());
        $paymentRepository->save($payment, true);
        return new JsonResponse(['message' => 'Payment status updated'], 200);
    }

    #[Route('/api/stripe/webhook', name: 'api_stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(Request $request, PaymentRepository $paymentRepository): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader,    $this->parameterBag->get('STRIPE_WEBHOOK_SECRET'));
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new Response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $payment = $paymentRepository->findOneBy(['paymentIntentId' => $paymentIntent->id]);
                if (!$payment) {
                    return new Response('Payment not found', 404);
                }
                $status = $paymentIntent->status;
                $payment->setStatus($status);
                $payment->setUpdatedAt(new \DateTime());
                $paymentRepository->save($payment, true);
                break;
                // ... gérer d'autres événements Stripe
            default:
                return new Response('Event type not supported', 400);
        }

        return new Response('Webhook received', 200);
    }
}
