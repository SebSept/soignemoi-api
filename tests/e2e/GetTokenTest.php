<?php

/** @noinspection ALL */

namespace App\Tests\e2e;

use App\Factory\DoctorFactory;
use App\Factory\PatientFactory;
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
     * @refacto test mixé, on vérifie le résultat d'une requete et un état en base de données. ça peut surement être séparé.
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

        $user = UserFactory::repository()->first();
        $role = $user->getRoles()[0];
        $token = $user->getAccessToken();

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['role' => 'ROLE_SECRETARY']);
        $this->assertJsonContains(['accessToken' => $token]);
    }

    public function testAdminGetTokenAndRequetDoctors(): void
    {
        // Arrange
        $email = 'admin@admin.com'; // email spécial pour les admins - src/Entity/User.php:ADMIN_EMAIL
        $password = 'admin-password';
        $user = UserFactory::new()->create([
            'email' => $email,
            'password' => $password,
            'accessToken' => null,
            'tokenExpiration' => null,
        ]);
        DoctorFactory::new()->create(['user' => $user]);


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

        $user = UserFactory::repository()->first();
        $role = $user->getRoles()[0];
        $token = $user->getAccessToken();
//        $expiration = $user->getTokenExpiration();

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['role' => 'ROLE_DOCTOR']);
        $this->assertJsonContains(['accessToken' => $token]);
    }

    public function testPatientGetToken(): void
    {
        // Arrange
        $email = 'patient@patient.com';
        $password = 'hello';

        $client = static::createClient();
        $user = UserFactory::new()->create(
            [
                'email' => $email,
                'password' => $password,
                'roles' => [],
                'access_token' => UserFactory::VALID_PATIENT_TOKEN,
                'token_expiration' => new DateTime('+30 day'),
            ]
        );
        PatientFactory::new()->create(['user' => $user]);

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
        $role = $user->getRoles()[0];
        $token = $user->getAccessToken();
        $expiration = $user->getTokenExpiration();

        // Assert
        // check token is generated
        $content = json_decode($response->getContent());
        $fetchedToken = $content->accessToken ?? null;
        $this->assertNotNull($fetchedToken, 'Token not generated');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['accessToken' => $token]);
        $this->assertJsonContains(['role' => 'ROLE_PATIENT']);
        
        // test bd
        $this->assertGreaterThan(new DateTime(), $expiration);
        $this->assertEquals('ROLE_PATIENT', $role);
    }
}
