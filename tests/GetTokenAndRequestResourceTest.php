<?php

namespace App\Tests;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetTokenAndRequestResourceTest extends ApiTestCase
{
    use Factories, ResetDatabase;

    public function testAdminGetDoctors(): void
    {
        // Arrange
        $email = 'admin@admin.com';
        $password = 'admin-password';
        /** @var \App\Entity\User $admin */
        $admin = UserFactory::new()->create([
            'email' => $email,
            'password' => $password,
            'accessToken' => null,
            'tokenExpiration' => null,
            'roles' => ['ROLE_ADMIN'],
        ]);

        // Act 1 - request resource fails without valid token
        $client = static::createClientWithInvalidBearer();
        $client->request('GET', '/api/doctors');
        $this->assertResponseStatusCodeSame(401);

        // Act 2 - request resource succeeds with valid token

        // Act 2.1 request a token
        $client = static::createClient(); // important de refaire un client sans headers sinon echec de l'auth
        $response = $client->request('POST', '/token',
            ['json' => [
                'email' => $email,
                'password' => $password,
            ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]]
        );
        $content = json_decode($response->getContent());
        $fetchedToken = $content->accessToken ?? null;

        // Act 2.2 request resource with fetched token
        static::createClientWithBearer($fetchedToken)->request('GET', '/api/doctors');
        $this->assertResponseStatusCodeSame(200);
    }
}
