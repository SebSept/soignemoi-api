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
use App\ApiResource\Controller\CreatePatient;
use App\Repository\PatientRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            security: "is_granted('ROLE_SECRETARY')",
        ),
        // access pour tous
        new Post(
            security: "is_granted('PUBLIC_ACCESS')",
            controller: CreatePatient::class,
            read: false,
            normalizationContext: ['groups' => 'patient:create'],
        ),
        new Patch(),
    ],
    security: "is_granted('')",
)]
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:token', 'hospital_stay:read', 'prescription:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital_stay:details'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital_stay:details'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $address1 = null;

    #[ORM\Column(length: 255)]
    private string $address2;

    // @todo ce champs est supprimable, il n'est pas utilisé.
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    // champs lors de la création pour la création de l'utilisateur système
    #[Assert\Email]
    #[Groups(['patient:create'])]
    public string $userCreationEmail;

    #[Assert\PasswordStrength(message: 'Mot de passe trop faible.')]
    #[Groups(['patient:create'])]
    public string $userCreationPassword;

    /**
     * @var Collection<int, MedicalOpinion>
     */
    #[ORM\OneToMany(targetEntity: MedicalOpinion::class, mappedBy: 'patient')]
    private Collection $medicalOpinions;

    /**
     * @var Collection<int, HospitalStay>
     */
    #[ORM\OneToMany(targetEntity: HospitalStay::class, mappedBy: 'patient')]
    private Collection $hospitalStays;

    /**
     * @var Collection<int, Prescription>
     */
    #[ORM\OneToMany(targetEntity: Prescription::class, mappedBy: 'patient')]
    private Collection $prescriptions;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'patient')]
    private ?User $user = null;

    public function __construct()
    {
        $this->medicalOpinions = new ArrayCollection();
        $this->hospitalStays = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
    }

    #[Groups(['hospital_stay:read'])]
    public function getFullName(): string
    {
        return sprintf('%s %s', $this->lastname, $this->firstname);
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
        // @todo remove
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        // @todo remove
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
        // set the owning side to null (unless already changed)
        if ($this->medicalOpinions->removeElement($medicalOpinion) && $medicalOpinion->getPatient() === $this) {
            $medicalOpinion->setPatient(null);
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
        // set the owning side to null (unless already changed)
        if ($this->hospitalStays->removeElement($hospitalStay) && $hospitalStay->getPatient() === $this) {
            $hospitalStay->setPatient(null);
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
        // set the owning side to null (unless already changed)
        if ($this->prescriptions->removeElement($prescription) && $prescription->getPatient() === $this) {
            $prescription->setPatient(null);
        }

        return $this;
    }

    //    public function getUser(): ?User
    //    {
    //        return $this->user;
    //    }
    //
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getTodayPrescriptionByDoctor(?Doctor $doctor): ?Prescription
    {
        return $this->getPrescriptions()
            ->filter(static fn (Prescription $p): bool => $p->getDoctor() === $doctor)
            ->filter(static fn (Prescription $prescription): bool => $prescription->getDateTime()->format('Y-m-d') === (new DateTime())->format('Y-m-d'))
            ->first() ?: null;
    }

    public function getTodayMedicalOpinionByDoctor(?Doctor $doctor): ?MedicalOpinion
    {
        return $this->getMedicalOpinions()
            ->filter(static fn (MedicalOpinion $p): bool => $p->getDoctor() === $doctor)
            ->filter(static fn (MedicalOpinion $medicalOpinion): bool => $medicalOpinion->getDateTime()->format('Y-m-d') === (new DateTime())->format('Y-m-d'))
            ->first() ?: null;
    }

    /**
     * @return ReadableCollection<int, Prescription>
     */
    public function getPrescriptionsBetween(DateTimeInterface $checkin, ?DateTimeInterface $checkout): ReadableCollection
    {
        $prescriptions = $this->getPrescriptions()
            ->filter(static fn (Prescription $p): bool => $p->getDateTime()->format('Y-m-d') >= $checkin->format('Y-m-d'));

        if (!is_null($checkout)) {
            $prescriptions
                ->filter(static fn (Prescription $p): bool => $p->getDateTime()->format('Y-m-d') <= $checkout->format('Y-m-d'));
        }

        return $prescriptions;
    }

    /**
     * @return ReadableCollection<int, MedicalOpinion>
     */
    public function getMedicalOpinionsBetween(DateTimeInterface $checkin, ?DateTimeInterface $checkout): ReadableCollection
    {
        $medicalOpinions = $this->getMedicalOpinions()
            ->filter(static fn (MedicalOpinion $p): bool => $p->getDateTime()->format('Y-m-d') >= $checkin->format('Y-m-d'));

        if (!is_null($checkout)) {
            $medicalOpinions
                ->filter(static fn (MedicalOpinion $p): bool => $p->getDateTime()->format('Y-m-d') <= $checkout->format('Y-m-d'));
        }

        return $medicalOpinions;
    }
}
