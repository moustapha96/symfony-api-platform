<?php

namespace App\Entity;

use App\Repository\DemandeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DemandeurRepository::class)]

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['demandeur:item']]),
        new Post(normalizationContext: ['groups' => ['demandeur:write']]),
        new GetCollection(normalizationContext: ['groups' => ['demandeur:list']]),
    ],
    order: ["id" => "DESC"],
    paginationEnabled: false,
)]
#[ORM\Table(name: '`adn_demandeurs`')]
class Demandeur
{
    #[ORM\Id]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $name = null;

    #[ORM\Column(length: 30)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $intitule = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $lieuNaissance = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $profession = null;

    #[ORM\Column(length: 10)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $paysResidence = null;

    #[ORM\OneToOne(inversedBy: 'demandeur', cascade: ['persist', 'remove'])]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'user:list',])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $compte = null;

    #[ORM\OneToMany(mappedBy: 'demandeur', targetEntity: Demande::class)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'user:list',])]
    private Collection $demandes;

    #[ORM\Column(nullable: true)]
    private ?bool $isDeleted = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['demandeur:item', 'demandeur:write', 'demandeur:list', 'demande:list', 'demande:item', 'demande:item', 'user:list', 'institut:item', 'transaction:list', 'transaction:item'])]
    private ?string $codeUser = null;

    public function __construct()
    {
        // $this->documents = new ArrayCollection();
        $this->demandes = new ArrayCollection();
    }


    public function generateCode()
    {
        $date = new \DateTime();
        $year = $date->format('Y');
        $day = $date->format('d');
        $month = $date->format('m');
        $seconds = $date->format('s');
        $hours = $date->format('H');
        $munite = $date->format('i');

        // You can modify this further if you need a unique identifier or a counter.
        return 'D-' . $year . $month . $day . $hours . $munite . $seconds;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): self
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getPaysResidence(): ?string
    {
        return $this->paysResidence;
    }

    public function setPaysResidence(string $paysResidence): self
    {
        $this->paysResidence = $paysResidence;

        return $this;
    }

    public function getCompte(): ?User
    {
        return $this->compte;
    }

    public function setCompte(User $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    // /**
    //  * @return Collection<int, Document>
    //  */
    // public function getDocuments(): Collection
    // {
    //     return $this->documents;
    // }

    // public function addDocument(Document $document): self
    // {
    //     if (!$this->documents->contains($document)) {
    //         $this->documents->add($document);
    //         $document->setDemandeur($this);
    //     }

    //     return $this;
    // }

    // public function removeDocument(Document $document): self
    // {
    //     if ($this->documents->removeElement($document)) {
    //         // set the owning side to null (unless already changed)
    //         if ($document->getDemandeur() === $this) {
    //             $document->setDemandeur(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setDemandeur($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getDemandeur() === $this) {
                $demande->setDemandeur(null);
            }
        }

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

    public function getCodeUser(): ?string
    {
        return $this->codeUser;
    }

    public function setCodeUser(?string $codeUser): self
    {
        $this->codeUser = $codeUser;

        return $this;
    }
}
