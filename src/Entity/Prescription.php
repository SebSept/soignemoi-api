<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PrescriptionRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_SECRETARY')",
        ),
        new Get(
            security: "is_granted('ROLE_SECRETARY')",
        ),
        new Post(
            security: "is_granted('ROLE_DOCTOR')",
        ),
        new Patch(
            security: "is_granted('ROLE_DOCTOR')",
        ),
    ],
    normalizationContext: ['groups' => 'prescription:read'],
    denormalizationContext: ['groups' => 'prescription:write'],
    security: "is_granted('')",
)]
class Prescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['prescription:read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: 'prescriptions')]
    #[Groups(['prescription:read','prescription:write'])]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[Groups(['prescription:read','prescription:write'])]
    private ?Doctor $doctor = null;

    #[ORM\OneToMany(targetEntity: PrescriptionItem::class, mappedBy: 'prescription')]
    #[Groups(['prescription:read','prescription:write'])]
    private Collection $items;

    public function __construct()
    {
        $this->date = new DateTime();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    //    public function setDate(\DateTimeInterface $date): static
    //    {
    //        $this->date = $date;
    //
    //        return $this;
    //    }

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

    public function addItem(PrescriptionItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPrescription($this);
        }

        return $this;
    }

    public function removeItem(PrescriptionItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getPrescription() === $this) {
                $item->setPrescription(null);
            }
        }

        return $this;
    }
}
