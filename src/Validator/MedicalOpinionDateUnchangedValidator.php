<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\Validator;

use App\Entity\Doctor;
use App\Entity\MedicalOpinion;
use App\Entity\Patient;
use App\Repository\MedicalOpinionRepository;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @see \App\Tests\Validator\MedicalOpinionDateUnchangedValidatorTest
 */
class MedicalOpinionDateUnchangedValidator extends ConstraintValidator
{
    public function __construct(private readonly MedicalOpinionRepository $medicalOpinionRepository)
    {
    }

    /**
     * @param MedicalOpinion              $value
     * @param MedicalOpinionDateUnchanged $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        assert($value instanceof MedicalOpinion);
        // modification : pas de contrainte
        if (null !== $value->getId()) {
            return;
        }

        // si on a pas de patient et de docteur défini, on ne peut pas valider
        if (!($value->getPatient() instanceof Patient) || !($value->getDoctor() instanceof Doctor)) {
            $this->context->buildViolation('Le patient et le docteur doivent être définis pour valider la medicalOpinion')
                ->addViolation();

            return;
        }

        // recherche d'une medicalOpinion pour le patient et le docteur pour ce jour
        $existingMedicalOpinion = $this->medicalOpinionRepository->findOneBy([
            'patient' => $value->getPatient(),
            'doctor' => $value->getDoctor(),
            'dateTime' => new DateTime('now'),
        ]);

        if (!($existingMedicalOpinion instanceof MedicalOpinion)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
