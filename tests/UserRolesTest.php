<?php

namespace App\Tests;

use App\Factory\DoctorFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRolesTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    public function testUserAssociatedWithADoctorHasROLE_DOCTOR(): void
    {
        $user = UserFactory::new()->create();
        DoctorFactory::new()->create(['user' => $user]);

        $roles = $user->getRoles();
        $this->assertContains('ROLE_DOCTOR', $roles);
        $this->assertNotContains('ROLE_PATIENT', $roles);
        $this->assertNotContains('ROLE_SECRETARY', $roles);
        $this->assertNotContains('ROLE_ADMIN', $roles);
    }
}
