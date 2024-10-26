<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['transaction:item']]),
        new Post(normalizationContext: ['groups' => ['transaction:write']]),
        new GetCollection(normalizationContext: ['groups' => ['transaction:list']]),
    ],
    order: ["id" => "DESC"],
    paginationEnabled: false,
)]

#[ORM\Table(name: '`adn_transactions`')]
class Transaction
{

    // Constants for payment types
    const TYPE_STRIPE = 'Stripe';
    const TYPE_PAYPAL = 'PayPal';
    const TRANSACTION_VERIFICATION = 'vérification';
    const TRANSACTION_ABONNEMENT = 'abonnement';

    const ETAT_PAID = 'paid';
    const ETAT_PENDING = 'pending';
    const ETAT_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['transaction:item', 'transaction:write', 'transaction:list',  'demande:list', 'demande:item',])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['transaction:item', 'transaction:write', 'transaction:list',  'demande:list', 'demande:item',])]
    private ?float $montant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['transaction:item', 'transaction:write', 'transaction:list',  'demande:list', 'demande:item',])]
    private ?\DateTimeInterface $dateTransaction = null;

    #[ORM\Column(length: 255)]
    #[Groups(['transaction:item', 'transaction:write', 'transaction:list',  'demande:list', 'demande:item',])]
    private ?string $typePaiement = null;

    #[ORM\Column(length: 255)]
    #[Groups(['transaction:item', 'transaction:write', 'transaction:list',  'demande:list', 'demande:item',])]
    private ?string $typeTransaction = null;

    #[ORM\OneToOne(inversedBy: 'transaction')]
    #[Groups(['transaction:item', 'transaction:write', 'transaction:list'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Demande $demande = null;

    #[Groups(['transaction:item', 'transaction:write', 'transaction:list',  'demande:list', 'demande:item',])]
    #[ORM\Column(nullable: true)]
    private ?bool $isDeleted = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['transaction:item', 'transaction:write', 'transaction:list',  'demande:list', 'demande:item'])]
    private ?string $etat = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->dateTransaction;
    }

    public function setDateTransaction(\DateTimeInterface $dateTransaction): self
    {
        $this->dateTransaction = $dateTransaction;

        return $this;
    }

    public function getTypePaiement(): ?string
    {
        return $this->typePaiement;
    }

    public function setTypePaiement(string $typePaiement): static
    {
        // Validate the payment type
        if (!in_array($typePaiement, [self::TYPE_STRIPE, self::TYPE_PAYPAL])) {
            throw new \InvalidArgumentException("Invalid payment type: " . $typePaiement);
        }

        $this->typePaiement = $typePaiement;

        return $this;
    }

    public function getTypeTransaction(): ?string
    {
        return $this->typeTransaction;
    }


    public function setTypeTransaction(string $typeTransaction): static
    {
        // Validate the transaction type
        if (!in_array($typeTransaction, [self::TRANSACTION_VERIFICATION, self::TRANSACTION_ABONNEMENT])) {
            throw new \InvalidArgumentException("Invalid transaction type: " . $typeTransaction);
        }

        $this->typeTransaction = $typeTransaction;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        $this->demande = $demande;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        if (!in_array($etat, [self::ETAT_PAID, self::ETAT_PENDING, self::ETAT_FAILED])) {
            throw new \InvalidArgumentException("Invalid etat : " . $etat);
        }
        $this->etat = $etat;
        return $this;
    }
}
