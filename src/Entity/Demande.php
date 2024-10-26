<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['demande:item']]),
        new Post(normalizationContext: ['groups' => ['demande:write']]),
        new GetCollection(normalizationContext: ['groups' => ['demande:list']]),
    ],
    order: ["id" => "DESC"],
    paginationEnabled: false,
)]
#[ORM\Table(name: '`adn_demandes`')]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?\DateTimeInterface $dateDemande = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $intitule = null;

    #[ORM\Column(length: 4)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $anneeObtention = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $nameInstitut = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $adresseInstitut = null;

    #[ORM\Column(length: 30)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $phoneInstitut = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $emailInstitut = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'institut:list', 'institut:item', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $resultat = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'demandeur:item', 'institut:list', 'institut:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $paysInstitut = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'institut:list', 'institut:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?Demandeur $demandeur = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?Institut $institut = null;


    #[ORM\OneToOne(mappedBy: 'demande', cascade: ['persist', 'remove'])]
    #[Groups(['demande:item', 'demande:write', 'demande:list'])]
    private ?Transaction $transaction = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'demandeur:item', 'institut:list', 'institut:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?bool $isDeleted = null;

    #[ORM\ManyToOne]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'demandeur:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?Institut $institutDemandeur = null;

    #[ORM\OneToOne(inversedBy: 'demande', cascade: ['persist', 'remove'])]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'demandeur:item', 'institut:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?Document $document = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'demandeur:item', 'institut:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?Payment $payment = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['demande:item', 'demande:write', 'demande:list', 'demandeur:list', 'demandeur:item', 'institut:list', 'institut:item', 'document:list', 'document:item', 'transaction:list', 'transaction:item'])]
    private ?string $statusPayment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeInterface $dateDemande): self
    {
        $this->dateDemande = $dateDemande;
        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;
        return $this;
    }

    public function getAnneeObtention(): ?string
    {
        return $this->anneeObtention;
    }

    public function setAnneeObtention(string $anneeObtention): self
    {
        $this->anneeObtention = $anneeObtention;
        return $this;
    }

    public function getNameInstitut(): ?string
    {
        return $this->nameInstitut;
    }

    public function setNameInstitut(string $nameInstitut): self
    {
        $this->nameInstitut = $nameInstitut;
        return $this;
    }

    public function getAdresseInstitut(): ?string
    {
        return $this->adresseInstitut;
    }

    public function setAdresseInstitut(string $adresseInstitut): self
    {
        $this->adresseInstitut = $adresseInstitut;
        return $this;
    }

    public function getPhoneInstitut(): ?string
    {
        return $this->phoneInstitut;
    }

    public function setPhoneInstitut(string $phoneInstitut): self
    {
        $this->phoneInstitut = $phoneInstitut;
        return $this;
    }

    public function getEmailInstitut(): ?string
    {
        return $this->emailInstitut;
    }

    public function setEmailInstitut(string $emailInstitut): self
    {
        $this->emailInstitut = $emailInstitut;
        return $this;
    }

    public function getResultat(): ?string
    {
        return $this->resultat;
    }

    public function setResultat(string $resultat): self
    {
        $this->resultat = $resultat;
        return $this;
    }

    public function getPaysInstitut(): ?string
    {
        return $this->paysInstitut;
    }

    public function setPaysInstitut(string $paysInstitut): self
    {
        $this->paysInstitut = $paysInstitut;
        return $this;
    }

    public function getDemandeur(): ?Demandeur
    {
        return $this->demandeur;
    }

    public function setDemandeur(?Demandeur $demandeur): self
    {
        $this->demandeur = $demandeur;
        return $this;
    }

    public function getInstitut(): ?Institut
    {
        return $this->institut;
    }

    public function setInstitut(?Institut $institut): self
    {
        $this->institut = $institut;
        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        // unset the owning side of the relation if necessary
        if ($transaction === null && $this->transaction !== null) {
            $this->transaction->setDemande(null);
        }

        // set the owning side of the relation if necessary
        if ($transaction !== null && $transaction->getDemande() !== $this) {
            $transaction->setDemande($this);
        }

        $this->transaction = $transaction;

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

    public function getInstitutDemandeur(): ?Institut
    {
        return $this->institutDemandeur;
    }

    public function setInstitutDemandeur(?Institut $institutDemandeur): self
    {
        $this->institutDemandeur = $institutDemandeur;
        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getStatusPayment(): ?string
    {
        return $this->statusPayment;
    }

    public function setStatusPayment(?string $statusPayment): self
    {
        $this->statusPayment = $statusPayment;

        return $this;
    }
}
