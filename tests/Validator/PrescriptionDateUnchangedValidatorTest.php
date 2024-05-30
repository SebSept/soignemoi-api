<?php

// tests/Validator/ContainsAlphanumericValidatorTest.php

namespace App\Tests\Validator;

use Override;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use App\Validator\PrescriptionDateUnchanged;
use App\Validator\PrescriptionDateUnchangedValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PrescriptionDateUnchangedValidatorTest extends ConstraintValidatorTestCase
{
    #[Override]
    protected function createValidator(): ConstraintValidatorInterface
    {
        $mockRepository = $this->createMock(PrescriptionRepository::class);
        $mockRepository->method('findOneBy')->willReturn(null);

        return new PrescriptionDateUnchangedValidator($mockRepository);
    }

    public function testNoPrescriptionExists(): void
    {
        $testedPrescription = new Prescription();
        $testedPrescription->setPatient(new Patient());
        $testedPrescription->setDoctor(new Doctor());

        $this->validator->validate($testedPrescription, new PrescriptionDateUnchanged());
        $this->assertNoViolation();
    }
}
