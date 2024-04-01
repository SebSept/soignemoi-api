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

    public function testGetDoctors(): void
    {
        $count = 2;
        DoctorFactory::createMany($count); // @todo utiliser des stories pour générer des données variables éventuellement

        static::createClient()->request('GET', '/doctors');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/doctors']);
        $this->assertJsonContains(['hydra:totalItems' => $count]);
    }
}
