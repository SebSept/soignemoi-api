<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\HospitalStayFactory;
use DateTime;
use DateTimeImmutable;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

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

    public function testCountStays()
    {
        // Arrange
        // hospital stays starting before Today
        // @todo a mettre dans la factory
        HospitalStayFactory::createMany(3, [
            'startDate' => new DateTime('-' . rand(1, 25) . ' days')
        ]);

        HospitalStayFactory::createMany(3 , [
            'startDate' => new DateTime('+' . rand(1, 25) . ' days')
        ]);

        HospitalStayFactory::createMany( 5, [
            'startDate' => new DateTime()
        ]);

        // Act
        static::createClient()->request('GET', 'hospital_stays/today_entries');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['hydra:totalItems' => 5]);
    }
}
