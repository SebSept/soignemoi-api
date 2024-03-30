<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\RecentDoctors;
use App\Repository\DoctorRepository;
use ContainerUWOM2sD\getMedicalOpinionRepositoryService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
//#[GetCollection(uriTemplate: '/doctors/recent', controller: RecentDoctors::class, normalizationContext: ['groups' => ['read']], name: 'recent',)]
#[ApiResource(
    operations: [
        new GetCollection(),
        // non défini ici, ne fonctionne pas, finalement oui... mais uniquement si on a pas de Get() avant.
        new GetCollection(
            uriTemplate: '/doctors/recent',
            controller: RecentDoctors::class,
            name: 'recent', // method
        ),
        new Get(),
        new Post(),
        new Patch(),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)

]
class Doctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
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

    #[ORM\Column(length: 255)]
    #[Groups(['write'])]
    private ?string $password = null;

    // @todo supprimer tout simplement ?
    #[ORM\OneToMany(targetEntity: MedicalOpinion::class, mappedBy: 'doctor')]
    private Collection $medicalOpinions;

    // @todo supprimer tout simplement ?
    #[ORM\OneToMany(targetEntity: HospitalStay::class, mappedBy: 'doctor')]
    private Collection $hospitalStays;

    // @todo supprimer tout simplement ?
    #[ORM\OneToMany(targetEntity: Prescription::class, mappedBy: 'doctor')]
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
            $medicalOpinion->setDoctor($this);
        }

        return $this;
    }

    public function removeMedicalOpinion(MedicalOpinion $medicalOpinion): static
    {
        if ($this->medicalOpinions->removeElement($medicalOpinion)) {
            // set the owning side to null (unless already changed)
            if ($medicalOpinion->getDoctor() === $this) {
                $medicalOpinion->setDoctor(null);
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
            $hospitalStay->setDoctor($this);
        }

        return $this;
    }

    public function removeHospitalStay(HospitalStay $hospitalStay): static
    {
        if ($this->hospitalStays->removeElement($hospitalStay)) {
            // set the owning side to null (unless already changed)
            if ($hospitalStay->getDoctor() === $this) {
                $hospitalStay->setDoctor(null);
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
            $prescription->setDoctor($this);
        }

        return $this;
    }

    public function removePrescription(Prescription $prescription): static
    {
        if ($this->prescriptions->removeElement($prescription)) {
            // set the owning side to null (unless already changed)
            if ($prescription->getDoctor() === $this) {
                $prescription->setDoctor(null);
            }
        }

        return $this;
    }
}
