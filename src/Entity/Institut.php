<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use App\Repository\InstitutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\DemandeurRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['institut:item']]),
        new Post(normalizationContext: ['groups' => ['institut:write']]),
        new GetCollection(normalizationContext: ['groups' => ['institut:list']]),
    ],
    order: ["id" => "DESC"],
    paginationEnabled: false,
)]
#[ORM\Table(name: '`adn_instituts`')]

#[ORM\Entity(repositoryClass: InstitutRepository::class)]
class Institut
{

    const TYPE_ECOLE = 'Ecole';
    const TYPE_BANQUE = 'Banque';
    const TYPE_AMBASSADE = 'Ambassade';
    const TYPE_UNIVERSITE = 'Université';

    #[ORM\Id]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list', 'demande:list', 'demande:item',])]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list', 'demande:list', 'demande:item',])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list', 'demande:list', 'demande:item',])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list',  'demande:list', 'demande:item',])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list',  'demande:list', 'demande:item',])]
    private ?string $siteWeb = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list',  'demande:list', 'demande:item',])]
    private ?string $intitule = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list',   'demande:list', 'demande:item',])]
    private ?string $paysResidence = null;

    #[ORM\OneToOne(inversedBy: 'institut', cascade: ['persist', 'remove'])]
    #[Groups(['institut:item', 'institut:write', 'institut:list'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $compte = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list',   'demande:list', 'demande:item',])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list', 'abonnement:list',  'demande:list', 'demande:item',])]
    private ?string $logo = null;

    // #[Groups(['institut:item', 'institut:write', 'institut:list', 'demande:list', 'user:list',  'demande:list',])]
    // #[ORM\OneToMany(mappedBy: 'institut', targetEntity: Document::class)]
    // private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'institut', targetEntity: Abonnement::class)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'demande:list', 'user:list',  'demande:list',])]
    private Collection $abonnements;

    #[ORM\OneToMany(mappedBy: 'institut', targetEntity: Demande::class)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'user:list',])]
    private Collection $demandes;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'demande:list', 'user:list',   'demande:item',])]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDeleted = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['institut:item', 'institut:write', 'institut:list', 'demande:list', 'user:list',   'demande:item',])]
    private ?string $codeUser = null;

    public function __construct()
    {
        // $this->documents = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
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

        $code = 'IN-' . $year . $month . $day . $hours . $munite . $seconds;
        return $code;
    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(string $siteWeb): self
    {
        $this->siteWeb = $siteWeb;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

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
    //         $document->setInstitut($this);
    //     }

    //     return $this;
    // }

    // public function removeDocument(Document $document): self
    // {
    //     if ($this->documents->removeElement($document)) {
    //         // set the owning side to null (unless already changed)
    //         if ($document->getInstitut() === $this) {
    //             $document->setInstitut(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): self
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setInstitut($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): self
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getInstitut() === $this) {
                $abonnement->setInstitut(null);
            }
        }

        return $this;
    }

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
            $demande->setInstitut($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getInstitut() === $this) {
                $demande->setInstitut(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        if (!in_array($type, [
            self::TYPE_ECOLE,
            self::TYPE_BANQUE,
            self::TYPE_AMBASSADE,
            self::TYPE_UNIVERSITE
        ])) {
            throw new \InvalidArgumentException("Invalid type institution: " . $type);
        }
        $this->type = $type;
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
