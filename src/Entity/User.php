<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'Cet identifiant est déjà utilisé par un autre utilisateur')]
#[UniqueEntity(fields: ['email'], message: 'Cet e-mail est déjà utilisé par un autre utilisateur')]



#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['user:item']]),
        new Post(normalizationContext: ['groups' => ['user:write']]),
        new GetCollection(normalizationContext: ['groups' => ['user:list']]),
    ],
    order: ["id" => "DESC"],
    paginationEnabled: false,
)]

#[ORM\Table(name: '`adn_users`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    // Constants for roles
    const ROLE_INSTITUT = 'ROLE_INSTITUT';
    const ROLE_DEMANDEUR = 'ROLE_DEMANDEUR';
    const ROLE_ADMIN = "ROLE_ADMIN";


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:list', 'user:write', 'demandeur:list', 'institut:list'])]
    private ?int $id = null;

    #[Groups(['user:read', 'user:list', 'user:write', 'demandeur:list', 'institut:list'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[Groups(['user:read', 'user:list', 'user:write', 'demandeur:list', 'institut:list'])]
    #[ORM\Column]
    private array $roles = [];


    #[Groups(['user:read', 'user:list', 'user:write', 'demandeur:list', 'institut:list'])]
    #[ORM\Column]
    private ?string $password = null;


    #[Groups(['user:read', 'user:list', 'user:write', 'demandeur:list', 'institut:list'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:list', 'user:write', 'demandeur:list', 'institut:list'])]
    private ?string $avatar = null;

    #[Groups(['user:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private $reset_token;

    #[ORM\Column(nullable: true)]
    private ?bool $enabled = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeted = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenActiveted = null;

    #[ORM\OneToOne(mappedBy: 'compte', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:list', 'user:write', 'demandeur:list', 'document:list'])]
    private ?Demandeur $demandeur = null;

    #[ORM\OneToOne(mappedBy: 'compte', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:list', 'user:write', 'institut:list', 'document:list'])]
    private ?Institut $institut = null;


    public function __construct() {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function setRoles(string $roles): static
    {
        // Validate roles to ensure they are one of the defined constants
        // foreach ($roles as $role) {
        //     if (!in_array($role, [self::ROLE_ETUDIANT, self::ROLE_UNIVERSITE, self::ROLE_AMBASSADE, self::ROLE_ADMIN])) {
        //         throw new \InvalidArgumentException("Invalid role: " . $role);
        //     }
        // }
        // $this->roles = array_unique($roles);


        $this->roles = [];

        if (!in_array($roles, [
            self::ROLE_INSTITUT,
            self::ROLE_DEMANDEUR,
            self::ROLE_ADMIN
        ])) {
            throw new \InvalidArgumentException("Invalid role: " . $roles);
        }
        $this->roles = [$roles]; // Assigner le rôle unique

        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, [self::ROLE_DEMANDEUR, self::ROLE_INSTITUT, self::ROLE_ADMIN])) {
            throw new \InvalidArgumentException("Invalid role: " . $role);
        }
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
        return $this;
    }
    // Method to remove a specific role from the user
    public function removeRole(string $role): static
    {
        if (($key = array_search($role, $this->roles)) !== false) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar !== null ? $avatar : '/avatar/avatar.png';
        return $this;
    }

    public function getImage(): string
    {

        if (str_contains($this->avatar, "avatars")) {
            $data = file_get_contents($this->getAvatar());
            $img_code = "data:image/png;base64,{`base64_encode($data)`}";
            return $img_code;
        } else {
            return $this->avatar;
        }
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isActiveted(): ?bool
    {
        return $this->activeted;
    }

    public function setActiveted(?bool $activeted): self
    {
        $this->activeted = $activeted;

        return $this;
    }

    public function getTokenActiveted(): ?string
    {
        return $this->tokenActiveted;
    }

    public function setTokenActiveted(?string $tokenActiveted): self
    {
        $this->tokenActiveted = $tokenActiveted;

        return $this;
    }

    public function getDemandeur(): ?Demandeur
    {
        return $this->demandeur;
    }

    public function setDemandeur(Demandeur $demandeur): self
    {
        // set the owning side of the relation if necessary
        if ($demandeur->getCompte() !== $this) {
            $demandeur->setCompte($this);
        }

        $this->demandeur = $demandeur;

        return $this;
    }

    public function getInstitut(): ?Institut
    {
        return $this->institut;
    }

    public function setInstitut(Institut $institut): self
    {
        // set the owning side of the relation if necessary
        if ($institut->getCompte() !== $this) {
            $institut->setCompte($this);
        }

        $this->institut = $institut;

        return $this;
    }
}
