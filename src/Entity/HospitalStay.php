<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\HospitalStayTodayEntries;
use App\Controller\HospitalStayTodayExits;
use App\Repository\HospitalStayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HospitalStayRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_DOCTOR') or is_granted('ROLE_PATIENT')",
        ),
        new GetCollection(
            uriTemplate: 'hospital_stays/today_entries',
            controller: HospitalStayTodayEntries::class,
            security: "is_granted('ROLE_SECRETARY') or is_granted('ROLE_DOCTOR')",
        ),
        new GetCollection(
            uriTemplate: 'hospital_stays/today_exits',
            controller: HospitalStayTodayExits::class,
            security: "is_granted('ROLE_SECRETARY')",
        ),
        new Get(),
        new Post(),
        new Patch(),
    ],
    security: "is_granted('')",
    //    paginationItemsPerPage: 5,
)]
//#[ApiFilter(DateFilter::class, properties: ['startDate'])]
class HospitalStay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checkin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checkout = null;

    #[ORM\Column(length: 255)]
    private ?string $reason = null;

    #[ORM\Column(length: 255)]
    private ?string $medicalSpeciality = null;

    #[ORM\ManyToOne(inversedBy: 'hospitalStays')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: 'Doctor')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Doctor $doctor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCheckin(): ?\DateTimeInterface
    {
        return $this->checkin;
    }

    public function setCheckin(?\DateTimeInterface $checkin): static
    {
        $this->checkin = $checkin;

        return $this;
    }

    public function getCheckout(): ?\DateTimeInterface
    {
        return $this->checkout;
    }

    public function setCheckout(?\DateTimeInterface $checkout): static
    {
        $this->checkout = $checkout;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

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
}
