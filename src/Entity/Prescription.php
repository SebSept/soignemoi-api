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
use App\Repository\PrescriptionRepository;
use App\Validator as AssertCustom;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
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
            denormalizationContext: ['groups' => 'prescription:update'],
            security: "is_granted('ROLE_DOCTOR')",
        ),
    ],
    normalizationContext: ['groups' => ['prescription:read']],
    denormalizationContext: ['groups' => 'prescription:write'],
    security: "is_granted('')",
)]
#[AssertCustom\PrescriptionDateUnchanged]
class Prescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital_stay:read', 'prescription:read', 'hospital_stay:details'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['prescription:read', 'hospital_stay:details'])]
    private DateTimeInterface $dateTime;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: 'prescriptions')]
    #[Groups(['prescription:read', 'prescription:write'])]
    #[Assert\NotBlank]
    private ?Patient $patient = null;  // @todo supprimer les nullables

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[Groups(['prescription:read', 'prescription:write'])]
    #[Assert\NotBlank]
    #[AssertCustom\UserIsDoctor]
    private ?Doctor $doctor = null;

    /**
     * @var Collection<int, PrescriptionItem>
     */
    #[ORM\OneToMany(targetEntity: PrescriptionItem::class, mappedBy: 'prescription', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['prescription:read', 'prescription:write', 'prescription:update', 'hospital_stay:details'])]
    #[Assert\NotBlank]
    #[Assert\Valid]
    private Collection $items;

    public function __construct()
    {
        $this->dateTime = new DateTime();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
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

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): static
    {
        $this->doctor = $doctor;

        return $this;
    }

    /**
     * @return Collection<int, PrescriptionItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(PrescriptionItem $prescriptionItem): static
    {
        if (!$this->items->contains($prescriptionItem)) {
            $this->items->add($prescriptionItem);
            $prescriptionItem->setPrescription($this);
        }

        return $this;
    }

    public function removeItem(PrescriptionItem $prescriptionItem): static
    {
        // set the owning side to null (unless already changed)
        if ($this->items->removeElement($prescriptionItem) && $prescriptionItem->getPrescription() === $this) {
            $prescriptionItem->setPrescription(null);
        }

        return $this;
    }
}
