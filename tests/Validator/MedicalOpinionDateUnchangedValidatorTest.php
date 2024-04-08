<?php

// tests/Validator/ContainsAlphanumericValidatorTest.php
namespace App\Tests\Validator;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\MedicalOpinion;
use App\Repository\MedicalOpinionRepository;
use App\Validator\MedicalOpinionDateUnchanged;
use App\Validator\MedicalOpinionDateUnchangedValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class MedicalOpinionDateUnchangedValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        $mockRepository = $this->createMock(MedicalOpinionRepository::class);
        $mockRepository->method('findOneBy')->willReturn(null);

        return new MedicalOpinionDateUnchangedValidator($mockRepository);
    }

    public function testNoMedicalOpinionExists(): void
    {
        $testedMedicalOpinion = new MedicalOpinion();
        $testedMedicalOpinion->setPatient(new Patient());
        $testedMedicalOpinion->setDoctor(new Doctor());

        $this->validator->validate($testedMedicalOpinion, new MedicalOpinionDateUnchanged());
        $this->assertNoViolation();
    }

}