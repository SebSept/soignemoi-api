<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const EXPIRATION_DELAY_DAYS = '30';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /** @var string[] The user roles */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tokenExpiration = null;

    #[ORM\OneToOne(targetEntity: Doctor::class, mappedBy: 'user')]
    private ?Doctor $doctor = null;

    private readonly string $adminEmail;

    public function __construct()
    {
        // @todo il faudrait utiliser les evenement doctrine pour faire cette affectation
        // en utilisant une variable de configuration de services.yml
        // https://symfony.com/doc/current/doctrine/events.html
        if(!isset($_ENV['admin_email'])) {
            throw new \RuntimeException('Définir la variable admin_email dans le fichier d\'environement');
        }
        $this->adminEmail = $_ENV['admin_email'];
    }

    #[ORM\OneToOne(targetEntity: Patient::class, mappedBy: 'user')]
    private ?Patient $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // @todo doit être supprimable
        $roles[] = match (true) {
            $this->isDoctor() => 'ROLE_DOCTOR',
            $this->isPatient() => 'ROLE_PATIENT',
            $this->email === $this->adminEmail => 'ROLE_ADMIN',
            default => 'ROLE_SECRETARY'
        };

        if(count($roles) > 2) {
            throw new \LogicException('User lié à plusieurs roles ' . var_export($roles, true));
        }

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isTokenValid()
    {
        return true; // @todo implement me
    }

    #[Groups('user:token')]
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getTokenExpiration(): ?\DateTimeInterface
    {
        return $this->tokenExpiration;
    }

    public function setTokenExpiration(?\DateTimeInterface $tokenExpiration): static
    {
        $this->tokenExpiration = $tokenExpiration;

        return $this;
    }

    public function generateToken(): void
    {
        try {
            $this->accessToken = bin2hex(random_bytes(32));
            $this->tokenExpiration = new \DateTime('+' . self::EXPIRATION_DELAY_DAYS . ' day');
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not generate random token : '. $e->getMessage());
        }
    }

    private function isDoctor(): bool
    {
        return $this->doctor !== null;
    }

    private function isPatient(): bool
    {
        return $this->patient !== null;
    }
}
