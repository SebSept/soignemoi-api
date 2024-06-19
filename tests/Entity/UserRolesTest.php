<?php

namespace App\Tests\Entity;

use App\Factory\DoctorFactory;
use App\Factory\PatientFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRolesTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testUserAssociatedWithADoctorHasROLEDOCTOR(): void
    {
        $user = UserFactory::new()->create();
        DoctorFactory::new()->create(['user' => $user]);

        $roles = $user->getRoles();
        $this->assertContains('ROLE_DOCTOR', $roles);
        $this->assertNotContains('ROLE_PATIENT', $roles);
        $this->assertNotContains('ROLE_SECRETARY', $roles);
        $this->assertNotContains('ROLE_ADMIN', $roles);
    }

    public function testUserAssociatedWithAPatientHasROLEPATIENT(): void
    {
        $user = UserFactory::new()->create();
        PatientFactory::new()->create(['user' => $user]);

        $roles = $user->getRoles();
        $this->assertContains('ROLE_PATIENT', $roles);
        $this->assertNotContains('ROLE_DOCTOR', $roles);
        $this->assertNotContains('ROLE_SECRETARY', $roles);
        $this->assertNotContains('ROLE_ADMIN', $roles);
    }

    public function testUserNotAssociatedHasROLESECRETARY(): void
    {
        $user = UserFactory::new()->create();

        $roles = $user->getRoles();
        $this->assertContains('ROLE_SECRETARY', $roles);
        $this->assertNotContains('ROLE_DOCTOR', $roles);
        $this->assertNotContains('ROLE_PATIENT', $roles);
        $this->assertNotContains('ROLE_ADMIN', $roles);
    }

    public function testUserWithSpecialMailHasROLEADMIN(): void
    {
        $user = UserFactory::new()->create(['email' => 'admin@admin.com']);

        $roles = $user->getRoles();
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertNotContains('ROLE_DOCTOR', $roles);
        $this->assertNotContains('ROLE_PATIENT', $roles);
        $this->assertNotContains('ROLE_SECRETARY', $roles);
    }
}
