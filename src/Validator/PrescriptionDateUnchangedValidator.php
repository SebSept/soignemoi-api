<?php

namespace App\Validator;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @see \App\Tests\Validator\PrescriptionDateUnchangedValidatorTest
 */
class PrescriptionDateUnchangedValidator extends ConstraintValidator
{
    public function __construct(private readonly PrescriptionRepository $prescriptionRepository)
    {
    }

    /**
     * @param Prescription              $value
     * @param PrescriptionDateUnchanged $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        assert($value instanceof Prescription);
        // modification : pas de contrainte
        if (null !== $value->getId()) {
            return;
        }

        // si on a pas de patient et de docteur défini, on ne peut pas valider
        if (!($value->getPatient() instanceof Patient) || !($value->getDoctor() instanceof Doctor)) {
            $this->context->buildViolation('Le patient et le docteur doivent être définis pour valider la prescription')
                ->addViolation();

            return;
        }

        // recherche d'une prescription pour le patient et le docteur pour ce jour
        $existingPrescription = $this->prescriptionRepository->findOneBy([
            'patient' => $value->getPatient(),
            'doctor' => $value->getDoctor(),
            'dateTime' => new DateTime('now'),
        ]);

        if (!($existingPrescription instanceof Prescription)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
