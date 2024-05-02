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
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\HospitalStayDoctorToday;
use App\Controller\HospitalStayTodayEntries;
use App\Controller\HospitalStayTodayExits;
use App\Repository\HospitalStayRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: HospitalStayRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            // @todo trop de droits ?
            security: "is_granted('ROLE_DOCTOR') or is_granted('ROLE_PATIENT') or is_granted('ROLE_ADMIN')",
        ),
        new GetCollection(
            uriTemplate: '/hospital_stays/today_entries',
            controller: HospitalStayTodayEntries::class,
            security: "is_granted('ROLE_SECRETARY') or is_granted('ROLE_DOCTOR')",
        ),
        new GetCollection(
            uriTemplate: '/hospital_stays/today_exits',
            controller: HospitalStayTodayExits::class,
            security: "is_granted('ROLE_SECRETARY')",
        ),
        new GetCollection(
            uriTemplate: '/doctors/{doctor_id}/hospital_stays/today',
            uriVariables: [
                'doctor_id' => new Link(fromClass: Doctor::class),
            ],
            controller: HospitalStayDoctorToday::class,
            security: "is_granted('ROLE_DOCTOR')",
            normalizationContext: ['groups' => 'hospital_stay:read'],
        ),
        new GetCollection(
            uriTemplate: '/patients/{patient_id}/hospital_stays/',
            uriVariables: [
                'patient_id' => new Link(
                    fromProperty: 'hospitalStays',
                    fromClass: Patient::class
                ),
            ],
            normalizationContext: ['groups' => 'hospital_stay:read'],
            security: "is_granted('ROLE_PATIENT')",
        ),
        new Get(),
        new Post(
            security: "is_granted('ROLE_PATIENT')",
        ),
        new Patch(
            security: "is_granted('ROLE_SECRETARY') or is_granted('ROLE_ADMIN')",
        ),
    ],
    security: "is_granted('')",
    //    paginationItemsPerPage: 5,
)]
// #[ApiFilter(DateFilter::class, properties: ['startDate'])]
class HospitalStay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital_stay:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['hospital_stay:read'])]
    private ?DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['hospital_stay:read'])]
    private ?DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['hospital_stay:read'])]
    private ?DateTimeInterface $checkin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['hospital_stay:read'])]
    private ?DateTimeInterface $checkout = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital_stay:read'])]
    private ?string $reason = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital_stay:read'])]
    private ?string $medicalSpeciality = null;

    #[ORM\ManyToOne(inversedBy: 'hospitalStays')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hospital_stay:read'])]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hospital_stay:read'])]
    private ?Doctor $doctor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['hospital_stay:read'])]
    public function getTodayPrescription(): ?Prescription
    {
        if (is_null($this->patient)) {
            return null;
        }

        return $this->patient->getTodayPrescriptionByDoctor($this->doctor);
    }

    #[Groups(['hospital_stay:read'])]
    public function getTodayMedicalOpinion(): ?MedicalOpinion
    {
        return null;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCheckin(): ?DateTimeInterface
    {
        return $this->checkin;
    }

    public function setCheckin(?DateTimeInterface $checkin): static
    {
        $this->checkin = $checkin;

        return $this;
    }

    public function getCheckout(): ?DateTimeInterface
    {
        return $this->checkout;
    }

    public function setCheckout(?DateTimeInterface $checkout): static
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
