<?php

namespace App\Tests;

use App\Factory\DoctorFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DoctorsTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testAuthRequired(): void
    {
        static::createClient()->request('GET', '/api/doctors');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testAuthSuccessWithValidToken(): void
    {
        static::createClientWithValidAuthHeaders()->request('GET', '/api/doctors');
        $this->assertResponseStatusCodeSame(200);
    }

    // @todo faire des tests avec token existant mais expiré

    public function testAuthFailsWithInValidToken(): void
    {
        static::createClientWithInvalidAuthHeaders()->request('GET', '/api/doctors');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetDoctors(): void
    {
        $count = 2;
        DoctorFactory::new()->many($count)->create();

        static::createClientWithValidAuthHeaders()->request('GET', '/api/doctors');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/api/doctors']);
        $this->assertJsonContains(['hydra:totalItems' => $count]);
    }
}
