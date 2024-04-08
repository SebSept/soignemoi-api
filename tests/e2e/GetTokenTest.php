<?php

/** @noinspection ALL */

namespace App\Tests\e2e;

use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use DateTime;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetTokenTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    /**
     * Test la génération de token.
     */
    public function testSecretaryGetToken(): void
    {
        // Arrange
        $email = 'hello@api.com';
        $password = 'hello-password';

        $client = static::createClient();
        $user = UserFactory::new()->create([
            'email' => $email,
            'password' => $password,
            'accessToken' => null,
            'tokenExpiration' => null,
        ]);

        // Act
        $response = $client->request(
            'POST',
            '/token',
            ['json' => [
                'email' => $email,
                'password' => $password,
            ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]]
        );
        $user = UserFactory::repository()->first();
        $token = $user->getAccessToken();
        $expiration = $user->getTokenExpiration();

        // Assert
        // check token is generated
        $content = json_decode($response->getContent());
        $fetchedToken = $content->accessToken ?? null;
        $this->assertNotNull($fetchedToken, 'Token not generated');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['accessToken' => $token]);
        $this->assertGreaterThan(new DateTime(), $expiration);
    }

    public function testAdminGetTokenAndRequetDoctors(): void
    {
        // Arrange
        $email = 'admin@admin.com'; // email spécial pour les admins - src/Entity/User.php:ADMIN_EMAIL
        $password = 'admin-password';
        UserFactory::new()->create([
            'email' => $email,
            'password' => $password,
            'accessToken' => null,
            'tokenExpiration' => null,
            //            'roles' => ['ROLE_ADMIN'], // pour le moment on determine l'admin via un mail spécial
        ]);

        // Act 1 - request resource fails without valid token
        $client = static::createClientWithInvalidBearer();
        $client->request('GET', '/api/doctors');
        $this->assertResponseStatusCodeSame(401);

        // Act 2 - request resource succeeds with valid token

        // Act 2.1 request a token
        $client = static::createClient(); // important de refaire un client sans headers sinon echec de l'auth
        $response = $client->request(
            'POST',
            '/token',
            ['json' => [
                'email' => $email,
                'password' => $password,
            ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]]
        );
        $content = json_decode($response->getContent());
        $fetchedToken = $content->accessToken ?? null;

        // Act 2.2 request resource with fetched token
        static::createClientWithBearer($fetchedToken)->request('GET', '/api/doctors');
        $this->assertResponseStatusCodeSame(200);
    }
}
