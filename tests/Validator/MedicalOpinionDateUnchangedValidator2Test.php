<?php

namespace App\Tests\Validator;

use App\Entity\Doctor;
use App\Entity\MedicalOpinion;
use App\Entity\Patient;
use App\Repository\MedicalOpinionRepository;
use App\Validator\MedicalOpinionDateUnchanged;
use App\Validator\MedicalOpinionDateUnchangedValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class MedicalOpinionDateUnchangedValidator2Test extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        $existingMedicalOpinion = new MedicalOpinion();
        $mockRepository = $this->createMock(MedicalOpinionRepository::class);
        $mockRepository->method('findOneBy')->willReturn($existingMedicalOpinion);

        return new MedicalOpinionDateUnchangedValidator($mockRepository);
    }

    public function testMedicalOpinionExists(): void
    {
        $testedMedicalOpinion = new MedicalOpinion();
        $testedMedicalOpinion->setPatient(new Patient());
        $testedMedicalOpinion->setDoctor(new Doctor());

        $this->validator->validate($testedMedicalOpinion, new MedicalOpinionDateUnchanged());
        $this->buildViolation('La création de cet objet est limitée à 1 par jour par patient et par docteur')
            ->assertRaised();
    }
}
