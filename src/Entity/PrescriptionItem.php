<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\PrescriptionItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PrescriptionItemRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(),
    ],
    security: "is_granted('')",
)]
class PrescriptionItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prescription:read'])]
    private ?string $drug = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prescription:read'])]
    private ?string $dosage = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prescription $prescription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDrug(): ?string
    {
        return $this->drug;
    }

    public function setDrug(string $drug): static
    {
        $this->drug = $drug;

        return $this;
    }

    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    public function setDosage(string $dosage): static
    {
        $this->dosage = $dosage;

        return $this;
    }

    public function getPrescription(): ?Prescription
    {
        return $this->prescription;
    }

    public function setPrescription(?Prescription $prescription): static
    {
        $this->prescription = $prescription;

        return $this;
    }
}
