<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
#[ApiResource]
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $address1 = null;

    #[ORM\Column(length: 255)]
    private ?string $address2 = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: MedicalOpinion::class, mappedBy: 'patient')]
    private Collection $medicalOpinions;

    #[ORM\OneToMany(targetEntity: HospitalStay::class, mappedBy: 'patient')]
    private Collection $hospitalStays;

    #[ORM\OneToMany(targetEntity: Prescription::class, mappedBy: 'patient')]
    private Collection $prescriptions;

    public function __construct()
    {
        $this->medicalOpinions = new ArrayCollection();
        $this->hospitalStays = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
    }

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

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): static
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): string
    {
        return $this->address2;
    }

    public function setAddress2(string $address2): static
    {
        $this->address2 = $address2;

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

    /**
     * @return Collection<int, MedicalOpinion>
     */
    public function getMedicalOpinions(): Collection
    {
        return $this->medicalOpinions;
    }

    public function addMedicalOpinion(MedicalOpinion $medicalOpinion): static
    {
        if (!$this->medicalOpinions->contains($medicalOpinion)) {
            $this->medicalOpinions->add($medicalOpinion);
            $medicalOpinion->setPatient($this);
        }

        return $this;
    }

    public function removeMedicalOpinion(MedicalOpinion $medicalOpinion): static
    {
        if ($this->medicalOpinions->removeElement($medicalOpinion)) {
            // set the owning side to null (unless already changed)
            if ($medicalOpinion->getPatient() === $this) {
                $medicalOpinion->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HospitalStay>
     */
    public function getHospitalStays(): Collection
    {
        return $this->hospitalStays;
    }

    public function addHospitalStay(HospitalStay $hospitalStay): static
    {
        if (!$this->hospitalStays->contains($hospitalStay)) {
            $this->hospitalStays->add($hospitalStay);
            $hospitalStay->setPatient($this);
        }

        return $this;
    }

    public function removeHospitalStay(HospitalStay $hospitalStay): static
    {
        if ($this->hospitalStays->removeElement($hospitalStay)) {
            // set the owning side to null (unless already changed)
            if ($hospitalStay->getPatient() === $this) {
                $hospitalStay->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prescription>
     */
    public function getPrescriptions(): Collection
    {
        return $this->prescriptions;
    }

    public function addPrescription(Prescription $prescription): static
    {
        if (!$this->prescriptions->contains($prescription)) {
            $this->prescriptions->add($prescription);
            $prescription->setPatient($this);
        }

        return $this;
    }

    public function removePrescription(Prescription $prescription): static
    {
        if ($this->prescriptions->removeElement($prescription)) {
            // set the owning side to null (unless already changed)
            if ($prescription->getPatient() === $this) {
                $prescription->setPatient(null);
            }
        }

        return $this;
    }
}
