<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\DoctorFactory;
use App\Factory\UserFactory;
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

    public function testAuthSuccessWithValidToken(): void
    {
        $validToken = 'this-is-a-valid-token-value';
        UserFactory::new()->create([
                'email' => 'test@test.com',
                'password' => 'hello',
                'roles' => ['ROLE_ADMIN'],
                'access_token' => $validToken,
                'token_expiration' => new \DateTime('+30 day'),
            ]
        );
        static::createClient()->request('GET', '/doctors', ['headers' => ['Authorization' => 'Bearer '.$validToken]]);

        $this->assertResponseStatusCodeSame(200);
    }

    // @todo faire des tests avec token existant mais expiré

    public function testAuthFailsWithInValidToken(): void
    {
        UserFactory::new()->create([
                'email' => 'test@test.com',
                'password' => 'hello',
                'roles' => ['ROLE_ADMIN'],
                'access_token' => 'this-is-a-valid-token-value',
                'token_expiration' => new \DateTime('+30 day'),
            ]
        );
        static::createClient()->request('GET', '/doctors', ['headers' => ['Authorization' => 'Bearer invalid-token']]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetDoctors(): void
    {
        $count = 2;
        DoctorFactory::createMany($count);

        static::createClient()->request(',
            GET', '/doctors');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/doctors']);
        $this->assertJsonContains(['hydra:totalItems' => $count]);
    }
}
