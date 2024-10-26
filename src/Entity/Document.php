<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DocumentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['document:item']]),
        new Post(normalizationContext: ['groups' => ['document:write']]),
        new GetCollection(normalizationContext: ['groups' => ['document:list']]),
    ],
    order: ["id" => "DESC"],
    paginationEnabled: false,
)]
#[ORM\Table(name: '`adn_documents`')]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?string $codeAdn = null;

    #[ORM\Column(length: 255)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?string $typeDocument = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?\DateTimeInterface $dateObtention = null;

    #[ORM\Column(length: 4)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?string $anneeObtention = null;

    #[ORM\Column(length: 255)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?string $statut = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?string $intitule = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?bool $isDeleted = null;

    #[ORM\OneToOne(mappedBy: 'document', cascade: ['persist', 'remove'])]
    #[Groups(['document:item', 'document:write', 'document:list'])]
    private ?Demande $demande = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['document:item', 'document:write', 'document:list', 'demande:list', 'demande:item'])]
    private ?string $mention = null;

    public function __construct() {}

    public function generateCode(): string
    {
        $date = new \DateTime();
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');
        $hours = $date->format('H');
        $minutes =   $date->format('i');
        return 'ADN-' . $year . $month . $day . $hours . $minutes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeAdn(): ?string
    {
        return $this->codeAdn;
    }

    public function setCodeAdn(string $codeAdn): self
    {
        $this->codeAdn = $codeAdn;
        return $this;
    }

    public function getTypeDocument(): ?string
    {
        return $this->typeDocument;
    }

    public function setTypeDocument(string $typeDocument): self
    {
        $this->typeDocument = $typeDocument;
        return $this;
    }

    public function getDateObtention(): ?\DateTimeInterface
    {
        return $this->dateObtention;
    }

    public function setDateObtention(\DateTimeInterface $dateObtention): self
    {
        $this->dateObtention = $dateObtention;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(?string $intitule): self
    {
        $this->intitule = $intitule;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
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

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        if ($demande === null && $this->demande !== null) {
            $this->demande->setDocument(null);
        }
        if ($demande !== null && $demande->getDocument() !== $this) {
            $demande->setDocument($this);
        }
        $this->demande = $demande;
        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(?string $mention): self
    {
        $this->mention = $mention;

        return $this;
    }
}
