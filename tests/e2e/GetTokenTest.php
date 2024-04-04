<?php /** @noinspection ALL */

namespace App\Tests\e2e;

use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetTokenTest extends ApiTestCase
{
    use Factories, ResetDatabase;

    /**
     * Test la génération de token
     * Devrait être scindé en deux tests, un unitaire et un fonctionnel
     */
    public function testGetToken(): void
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
        $this->assertGreaterThan(new \DateTime, $expiration);
    }

}
