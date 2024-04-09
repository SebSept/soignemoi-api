<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\DoctorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
        ),
        //        new Get(),
        new Post(),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
        ),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    security: "is_granted('')",
)
]
#[UniqueEntity(['firstname', 'lastname'])]
#[UniqueEntity(['employeeId'])]
class Doctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    #[Assert\NoSuspiciousCharacters] // https://symfony.com/doc/current/reference/constraints/NoSuspiciousCharacters.html
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $medicalSpeciality = null;

    #[ORM\Column(length: 25)]
    #[Groups(['read', 'write'])]
    private ?string $employeeId = null;

    // @todo ce champs est supprimable, il n'est pas utilisé.
    #[ORM\Column(length: 255)]
    #[Groups(['write'])]
    #[Assert\PasswordStrength(message: 'Mot de passe trop faible.')]
    private ?string $password = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'doctor')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getMedicalSpeciality(): ?string
    {
        return $this->medicalSpeciality;
    }

    public function setMedicalSpeciality(string $medicalSpeciality): static
    {
        $this->medicalSpeciality = $medicalSpeciality;

        return $this;
    }

    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    public function setEmployeeId(string $employeeId): static
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
