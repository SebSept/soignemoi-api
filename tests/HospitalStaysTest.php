<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\HospitalStayFactory;
use Zenstruck\Foundry\Test\Factories;

class HospitalStaysTest extends ApiTestCase
{
    use Factories;
//        , ResetDatabase;

    public function testIRIreachable(): void
    {
        static::createClient()->request('GET', 'hospital_stays/today_entries');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/hospital_stays/today_entries']);
    }

    public function testCountTodayEntries()
    {
        // Arrange
        HospitalStayFactory::new()->entryBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->entryToday()->many(5)->create();
        HospitalStayFactory::new()->entryAfterToday()->many(3)->create();

        // Act
        static::createClient()->request('GET', 'hospital_stays/today_entries');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['hydra:totalItems' => 5]);
    }

    public function testCountTodayExits()
    {
        // Arrange
        HospitalStayFactory::new()->exitBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->exitAfterToday()->many(3)->create();

        // Act
        static::createClient()->request('GET', 'hospital_stays/today_exits');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['hydra:totalItems' => 5]);
    }
}
