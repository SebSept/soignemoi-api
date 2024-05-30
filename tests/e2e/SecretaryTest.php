<?php

namespace App\Tests\e2e;

use Zenstruck\Browser\Json;
use App\Factory\HospitalStayFactory;
use App\Factory\MedicalOpinionFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecretaryTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;
    use HospitalStays;
    use HasBrowser;

    public function testCanAccessIri(): void
    {
        $ids = $this->makeEntities();
        $proxy = $this->makeSecretary();

        foreach ($this->AllowedIris($ids) as $iri) {
            $this->testAccessOk($iri[0], $proxy);
        }
    }

    /**
     * @x-dataProvider NotAllowedIris
     * pas possible d'utiliser un dataprovider (du moins, je n'ai pas réussi)
     */
    public function testCannotAccessIri(): void
    {
        $this->makeEntities();
        $proxy = $this->makeSecretary();

        foreach ($this->NotAllowedIris() as $iri) {
            $this->testAccessNotAllowedTo($iri[0], $proxy);
        }
    }

    public function testCountTodayEntries(): void
    {
        // Arrange
        PatientFactory::new()->many(10)->create();
        HospitalStayFactory::new()->withExistingPatient()->entryBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->withExistingPatient()->entryToday()->many(5)->create();
        HospitalStayFactory::new()->withExistingPatient()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->withExistingPatient()->entryAfterToday()->many(3)->create();

        // Act
        $secretary = UserFactory::new()->secretary()->create();
        $this->browser()->actingAs($secretary->object())
            ->get('/api/hospital_stays/today_entries', HttpOptions::json())
        // Assert
            ->assertSuccessful()
            ->use(static function (Json $json) : void {
                // Json acts like a proxy of zenstruck/assert Expectation class
                $json->hasCount(5);
            })
        ;
    }

    public function testCountTodayExits(): void
    {
        // Arrange
        PatientFactory::new()->many(10)->create();
        HospitalStayFactory::new()->withExistingPatient()->exitBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->withExistingPatient()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->withExistingPatient()->entryToday()->many(2)->create();
        HospitalStayFactory::new()->withExistingPatient()->exitAfterToday()->many(3)->create();

        // Act
        $secretary = UserFactory::new()->secretary()->create();
        $this->browser()->actingAs($secretary->object())
            ->get('/api/hospital_stays/today_entries', HttpOptions::json())
        // Assert
            ->assertSuccessful()
            ->use(static function (Json $json) : void {
                $json->hasCount(2);
            });
    }

    public function testModifyAnHospitalStay(): void
    {
        $secretaryUser = UserFactory::new()->secretary()->create();
        $this->modifyAnHospitalStay($secretaryUser->object());
    }

    private function AllowedIris(array $ids): array
    {
        return [
            ['/api/hospital_stays/today_entries'],
            ['/api/hospital_stays/today_exits'],
            ['/api/patients/'.$ids['patientId']],
            ['/api/prescriptions'],
            ['/api/prescriptions/'.$ids['prescriptionId']],
            ['/api/medical_opinions'],
            ['/api/medical_opinions/'.$ids['medicalOpinionId']],
        ];
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/doctors'],
            ['/api/patients'],
        ];
    }

    private function testAccessOk(string $uri, Proxy $user): void
    {
        $this->browser()->actingAs($user->object())
            ->get($uri)
            ->assertSuccessful();
    }

    private function testAccessNotAllowedTo(string $url, Proxy $user): void
    {
        $this->browser()
            ->actingAs($user->object())
            ->request('GET', $url)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return array{patientId: int, prescriptionId: int, medicalOpinionId: int}
     */
    private function makeEntities(): array
    {
        return [
            'patientId' => PatientFactory::new()->create()->getId(),
            'prescriptionId' => PrescriptionFactory::new()->create()->getId(),
            'medicalOpinionId' => MedicalOpinionFactory::new()->create()->getId(),
        ];
    }

    private function makeSecretary(): Proxy
    {
        return UserFactory::new()->secretary()->create();
    }
}
