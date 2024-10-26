<?php


namespace App\services;

use App\Entity\Demande;
use App\Entity\Demandeur;
use App\Entity\Document;
use App\Entity\Institut;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService extends AbstractController
{
    private $config;
    private $mailer;
    private $userRepository;

    public function __construct(
        ConfigurationService $config,
        MailerInterface $mailer,
        UserRepository $userRepository
    ) {
        $this->config = $config;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }



    /**
     * @param string $emailFormData
     */
    public function activerCompteDemandeur(Demandeur $demandeur): string
    {

        // $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $emailbase =  "contact@authenticpage.com";
        // $nombase =  $this->config->get("name") ?? "Authentic Page";
        $nombase =  "Authentic Page";
        $user =  $demandeur->getCompte();

        if ($user) {
            $token = bin2hex(random_bytes(32));
        }
        try {
            $user->setTokenActiveted($token);
            $this->userRepository->save($user, true);

            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($user->getEmail())
                ->subject('Activation de votre compte')
                ->htmlTemplate('user/activerCompte.html.twig')
                ->context([
                    'name' => $demandeur->getName(),
                    'activationUrl' => sprintf('%s/activate/%s', 'http://authenticpage.com', $token), // URL d'activation
                    'role' => implode(', ', $user->getRoles()), // Récupérer les rôles de l'utilisateur
                ]);
            $this->mailer->send($email);
            return 'Demande envoyée avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }



    public function activerCompteInstitut(Institut $institut): string
    {

        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase =  $this->config->get("name") ?? "Authentic Page";
        $user =   $institut->getCompte();

        if ($user) {
            $token = bin2hex(random_bytes(32));
        }
        try {
            $user->setTokenActiveted($token);
            $this->userRepository->save($user, true);

            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($user->getEmail())
                ->subject('Activation de votre compte')
                ->htmlTemplate('user/activerCompte.html.twig')
                ->context([
                    'name' => $institut->getName(),
                    'activationUrl' => sprintf('%s/activate/%s', 'http://authenticpage.com', $token), // URL d'activation
                    'role' => implode(', ', $user->getRoles()), // Récupérer les rôles de l'utilisateur
                ]);
            $this->mailer->send($email);
            return 'Demande envoyée avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendDocumentCreationEmail(User $etudiant, Document $document,  $file)
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase =  $this->config->get("name") ?? "Authentic Page";

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($etudiant->getEmail()) // Email de l'utilisateur
                ->subject('Nouveau document créé')
                ->htmlTemplate('emails/document_creation.html.twig')
                ->context([
                    'etudiant' => $etudiant,
                    'document' => $document,
                    'file' => $file,
                ]);

            $this->mailer->send($email);
            return 200;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendDemandeToDemandeur(Demande $demande): string
    {

        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase =  $this->config->get("name") ?? "Authentic Page";

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($demande->getDemandeur()->getEmail()) // Email de l'utilisateur
                ->subject('Nouvelle demande')
                ->htmlTemplate('emails/nouvelle_demande_demandeur.html.twig')
                ->context([
                    'demande' => $demande,
                    'demandeur' => $demande->getDemandeur(),
                ]);

            $this->mailer->send($email);
            return 200;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendDemandeToInstitut(Demande $demande): string
    {

        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase =  $this->config->get("name") ?? "Authentic Page";


        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($demande->getDemandeur()->getEmail()) // Email de l'utilisateur
                ->subject('Nouvelle demande')
                ->htmlTemplate('emails/nouvelle_demande_institut.html.twig')
                ->context([
                    'demande' => $demande,
                    'demandeur' => $demande->getDemandeur(),
                ]);

            $this->mailer->send($email);
            return 200;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendInvitationToInstitut(string $emailInstitut, string $nameInstitut, Demande $demande): string
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase = $this->config->get("name") ?? "Authentic Page";

        try {
            $token = bin2hex(random_bytes(32)); // Génération d'un token d'invitation unique

            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($emailInstitut) // Envoi à l'email de l'institut
                ->subject('Invitation à s\'inscrire et valider la demande')
                ->htmlTemplate('emails/invitation_institut.html.twig') // Template Twig pour l'email
                ->context([
                    'nameInstitut' => $nameInstitut,
                    'demande' => $demande,
                    'inscriptionUrl' => sprintf('http://authenticpage.com/invitation/%s', $token), // Lien d'inscription avec token
                ]);

            $this->mailer->send($email);
            return 'Invitation envoyée avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendDemandeToExistingInstitut(Institut $institut, Demande $demande, $institutDemandeur): string
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase = $this->config->get("name") ?? "Authentic Page";

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($institut->getEmail()) // Email de l'institut
                ->subject('Nouvelle Demande d\'Inscription')
                ->htmlTemplate('emails/nouvelle_demande_inscription.html.twig') // Template Twig pour l'email
                ->context([
                    'nameInstitut' => $institut->getName(),
                    'demande' => $demande,
                    'dateDemande' => $demande->getDateDemande()->format('d/m/Y'), // Formatage de la date
                    'intitule' => $demande->getIntitule(),
                    'anneeObtention' => $demande->getAnneeObtention(),
                    'institutDemandeur' => $institutDemandeur,
                ]);

            $this->mailer->send($email);
            return 'Email de demande d\'inscription envoyé avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    public function sendInvitationToInstitutForAuthentication(string $mailIntitut, string $nameInstitut,  Demande $demande, $institutDemandeur): string
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase = $this->config->get("name") ?? "Authentic Page";

        try {
            // Génération d'un token d'invitation unique
            $token = bin2hex(random_bytes(32));

            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($mailIntitut) // Email de l'institut
                ->subject('Invitation à s\'inscrire et authentifier une demande')
                ->htmlTemplate('emails/invitation_authentification.html.twig') // Template Twig pour l'email
                ->context([
                    'nameInstitut' => $nameInstitut,
                    'demandeurName' => $demande->getDemandeur()->getName(),
                    'demandeurEmail' => $demande->getDemandeur()->getEmail(),
                    'dateDemande' => $demande->getDateDemande()->format('d/m/Y'),
                    'inscriptionUrl' => sprintf('http://authenticpage.com/invitation/%s', $token),
                    'institutDemandeur' => $institutDemandeur,
                ]);

            $this->mailer->send($email);
            return 'Invitation envoyée avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendConfirmationToDemandeur(Demandeur $demandeur, Demande $demande): string
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase = $this->config->get("name") ?? "Authentic Page";

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($demandeur->getEmail()) // Email du demandeur
                ->subject('Confirmation de votre Demande')
                ->htmlTemplate('emails/confirmation_demande.html.twig') // Template Twig pour l'email
                ->context([
                    'demandeurName' => $demandeur->getName(),
                    'demande' => $demande,
                    // Ajoutez d'autres variables si nécessaire
                ]);

            $this->mailer->send($email);
            return 'Email de confirmation envoyé avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendDemandeResultToDemandeur(Demandeur $demandeur, string $statutDemande, string $detailsDemande): int
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase = $this->config->get("name") ?? "Authentic Page";
        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($demandeur->getEmail())
                ->subject('Résultat de votre Demande')
                ->htmlTemplate('emails/resultat_demande.html.twig') // Assurez-vous que le chemin est correct
                ->context([
                    'demandeurName' => $demandeur->getName(),
                    'statutDemande' => $statutDemande,
                    'detailsDemande' => $detailsDemande,
                ]);

            $this->mailer->send($email);
            return 200;
        } catch (\Throwable $th) {

            return 400;
        }
    }

    public function sendPaymentSuccessEmail(Demandeur $demandeur, Transaction $transaction): string
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase = $this->config->get("name") ?? "Authentic Page";

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($demandeur->getEmail()) // Email of the demandeur
                ->subject('Confirmation de votre paiement')
                ->htmlTemplate('emails/payment_success.html.twig') // Template for success email
                ->context([
                    'demandeurName' => $demandeur->getName(),
                    'montant' => $transaction->getMontant(),
                    'dateTransaction' => $transaction->getDateTransaction()->format('d/m/Y H:i:s'),
                    'typePaiement' => $transaction->getTypePaiement(),
                    'typeTransaction' => $transaction->getTypeTransaction(),
                ]);

            $this->mailer->send($email);
            return 'Email de confirmation de paiement envoyé avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    public function sendPaymentFailureEmail(Demandeur $demandeur, string $errorMessage): string
    {
        $emailbase = $this->config->get("email") ?? "contact@authenticpage.com";
        $nombase = $this->config->get("name") ?? "Authentic Page";

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($emailbase, $nombase))
                ->to($demandeur->getEmail()) // Email of the demandeur
                ->subject('Échec de votre paiement')
                ->htmlTemplate('emails/payment_failure.html.twig') // Template for failure email
                ->context([
                    'demandeurName' => $demandeur->getName(),
                    'errorMessage' => $errorMessage,
                ]);

            $this->mailer->send($email);
            return 'Email d\'échec de paiement envoyé avec succès !';
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
