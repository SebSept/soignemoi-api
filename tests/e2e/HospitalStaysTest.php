<?php

namespace App\Tests\e2e;

use App\Factory\HospitalStayFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class HospitalStaysTest extends ApiTestCase
{
    use Factories, ResetDatabase;

    public function testIRIreachable(): void
    {
        static::createClientAndUserWithValidAuthHeaders()
            ->request('GET', '/api/hospital_stays/today_entries');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/api/hospital_stays/today_entries']);
    }

    public function testCountTodayEntries()
    {
        // Arrange
        HospitalStayFactory::new()->entryBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->entryToday()->many(5)->create();
        HospitalStayFactory::new()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->entryAfterToday()->many(3)->create();

        // Act
        static::createClientAndUserWithValidAuthHeaders()
            ->request('GET', '/api/hospital_stays/today_entries');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['hydra:totalItems' => 5]);
    }

    public function testCountTodayExits()
    {
        // Arrange
        HospitalStayFactory::new()->exitBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->entryToday()->many(2)->create();
        HospitalStayFactory::new()->exitAfterToday()->many(3)->create();

        // Act
        static::createClientAndUserWithValidAuthHeaders()->request('GET', '/api/hospital_stays/today_exits');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }
}
