<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use LogicException;
use RuntimeException;
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
    private string $email;

    /** @var string[] The user roles */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateTime = null;

    #[ORM\OneToOne(targetEntity: Doctor::class, mappedBy: 'user')]
    private ?Doctor $doctor = null;

    /**
     * @var string
     *
     * @todo pas de moyen de faire autrement pour le moment
     * une variable d'environement n'est pas utilisable, le constructeur n'est pas appellé lors du mécanisme d'authentification interne
     * il faudrait passer par un evenement de Doctrine (ou kernel.request pour injecter la variable d'environement, suggéré par copilot)
     * @todo En fait, il suffit de mettre le role dans la base de données.
     */
    private const ADMIN_EMAIL = 'admin@admin.com';

    #[ORM\OneToOne(targetEntity: Patient::class, mappedBy: 'user')]
    private ?Patient $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
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
        return $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // @todo doit être supprimable, pour le moment, on le garde -> security.yaml : access_control
        $roles[] = match (true) {
            $this->isDoctor() => 'ROLE_DOCTOR',
            $this->isPatient() => 'ROLE_PATIENT',
            self::ADMIN_EMAIL === $this->email => 'ROLE_ADMIN',
            default => 'ROLE_SECRETARY'
        };

        $roles = array_filter(array_unique($roles));

        if (count($roles) > 2) {
            throw new LogicException('User lié à plusieurs roles '.var_export($roles, true));
        }

        return $roles;
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

    public function isTokenValid(): bool
    {
        return $this->accessToken && $this->dateTime > new DateTime();
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

    public function getTokenExpiration(): ?DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setTokenExpiration(?DateTimeInterface $tokenExpiration): static
    {
        $this->dateTime = $tokenExpiration;

        return $this;
    }

    public function generateToken(): void
    {
        try {
            $this->accessToken = bin2hex(random_bytes(32));
            $this->dateTime = new DateTime('+'.self::EXPIRATION_DELAY_DAYS.' day');
        } catch (Exception $exception) {
            throw new RuntimeException('Could not generate random token : '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function isDoctor(): bool
    {
        return $this->doctor instanceof Doctor;
    }

    private function isPatient(): bool
    {
        return $this->patient instanceof Patient;
    }
}
