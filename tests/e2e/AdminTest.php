<?php

namespace App\Tests\e2e;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AdminTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;
    use HasBrowser;
    use HospitalStays;

    public function testModifyAnHospitalStay(): void
    {
        $this->modifyAnHospitalStay($this->makeAdmin()->object());
    }

    public function testUpdateDoctor(): void
    {
        // Arrange
        $admin = $this->makeAdmin();
        $doctor = DoctorFactory::new()->create();

        // Act
        $this->browser()->actingAs($admin->object())
            ->request('PATCH', '/api/doctors/' . $doctor->getId(), [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => [
                    'firstname' => 'ledoc',
                ]
            ])

        // Assert
        ->assertSuccessful()
        ->assertJsonMatches('firstname' , 'ledoc')
        ;
    }

    public function testCreateDoctor(): void
    {
        // Arrange
        $admin = $this->makeAdmin();

        // Act
        $this->browser()->actingAs($admin->object())
            ->post('/api/doctors', [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => [
                    'firstname' => 'mALLICK',
                    'lastname' => 'Doe',
                    'medicalSpeciality' => 'Généraliste',
                    'employeeId' => '123',
                    'password' => 'password-verx-y7-strang',
                ]
            ])

            // Assert
            ->assertSuccessful()
            ->assertJsonMatches('firstname', 'mALLICK')
            ->assertJsonMatches('employeeId', '123')
            ;
    }

    public function testDeleteDoctor(): void
    {
        // Arrange
        $admin = $this->makeAdmin();
        $doctor = DoctorFactory::new()->create();

        // Act
        $this->browser()->actingAs($admin->object())
            ->delete('/api/doctors/' . $doctor->getId())

            // Assert
            ->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testCanAccessIri(): void
    {
        $user = $this->makeAdmin();

        foreach ($this->AllowedIris() as $iri) {
            $this->testAccessOk($iri[0], $user);
        }
    }

    private function AllowedIris(): array
    {
        return [
            ['/api/hospital_stays'],
            ['/api/doctors'],
        ];
    }

    public function testCannotAccessIri(): void
    {
        $user = $this->makeAdmin();

        foreach ($this->NotAllowedIris() as $iri) {
            $this->testAccessNotAllowedTo($iri[0], $user);
        }
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/medical_opinions'],
            ['/api/patients'],
        ];
    }

    private function testAccessOk(string $iri, Proxy $proxy): void
    {
        $this->browser()->actingAs($proxy->object())
            ->get($iri)
            ->assertSuccessful();
    }

    private function testAccessNotAllowedTo(string $string, Proxy $proxy): void
    {
        $this->browser()->actingAs($proxy->object())
            ->get($string)
->assertStatus(Response::HTTP_FORBIDDEN);
    }



    private function makeAdmin(): Proxy|User
    {
        return UserFactory::new()->admin()->create();
    }
}
