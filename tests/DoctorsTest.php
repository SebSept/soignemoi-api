<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\DoctorFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DoctorsTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testAuthRequired(): void
    {
        static::createClient()->request('GET', '/doctors');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetDoctors(): void
    {
        $count = 2;
        DoctorFactory::createMany($count);

        static::createClient()->request('GET', '/doctors');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/doctors']);
        $this->assertJsonContains(['hydra:totalItems' => $count]);
    }
}
