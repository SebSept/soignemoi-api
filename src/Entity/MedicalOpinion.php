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
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MedicalOpinionRepository;
use App\Validator as AssertCustom;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MedicalOpinionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_SECRETARY')",
        ),
        new Get(
            security: "is_granted('ROLE_SECRETARY') or is_granted('ROLE_DOCTOR')",
        ),
        new Post(
            security: "is_granted('ROLE_DOCTOR')",
        ),
        new Patch(
            denormalizationContext: ['groups' => 'medicalOpinion:update'],
            security: "is_granted('ROLE_DOCTOR')",
        ),
    ],
    normalizationContext: ['groups' => 'medicalOpinion:read'],
    denormalizationContext: ['groups' => 'medicalOpinion:write'],
    security: "is_granted('')",
)
]
#[AssertCustom\MedicalOpinionDateUnchanged]
class MedicalOpinion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital_stay:read', 'medicalOpinion:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['medicalOpinion:read', 'hospital_stay:details'])]
    private DateTimeInterface $dateTime;

    #[ORM\Column(length: 255)]
    #[Groups(['medicalOpinion:read', 'medicalOpinion:write', 'medicalOpinion:update', 'hospital_stay:details'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['medicalOpinion:read', 'medicalOpinion:write', 'medicalOpinion:update', 'hospital_stay:details'])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[Groups(['medicalOpinion:read', 'medicalOpinion:write'])]
    #[AssertCustom\UserIsDoctor]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: 'medicalOpinions')]
    #[Groups(['medicalOpinion:read', 'medicalOpinion:write'])]
    private ?Patient $patient = null;

    public function __construct()
    {
        $this->dateTime = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): static
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }
}
