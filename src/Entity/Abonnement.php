<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\BooleanFilter;
use App\Repository\AbonnementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['abonnement:item']]),
        new Post(normalizationContext: ['groups' => ['abonnement:write']]),
        new GetCollection(normalizationContext: ['groups' => ['abonnement:list']]),
    ],
    order: ["id" => "DESC"],
    paginationEnabled: false,
    filters: [
        BooleanFilter::class,
    ]
)]
#[ORM\Table(name: '`adn_abonnements`')]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['abonnement:item', 'abonnement:write', 'abonnement:list', 'institut:list'])]

    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['abonnement:item', 'abonnement:write', 'abonnement:list', 'institut:list'])]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['abonnement:item', 'abonnement:write', 'abonnement:list', 'institut:list'])]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column]
    #[Groups(['abonnement:item', 'abonnement:write', 'abonnement:list', 'institut:list'])]
    private ?float $montant = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['abonnement:item', 'abonnement:write', 'abonnement:list', 'institut:list'])]
    private ?bool $isDeleted = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    #[Groups(['abonnement:item', 'abonnement:write', 'abonnement:list'])]
    private ?Institut $institut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
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

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

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
}
