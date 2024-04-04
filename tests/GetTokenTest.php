<?php /** @noinspection ALL */

namespace App\Tests;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
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

    public function testIsValidTokenReturnsFalseIfExpired()
    {
        $user = UserFactory::new()->create([
            'accessToken' => 'token',
            'tokenExpiration' => new \DateTime('-1 day'),
        ]);

        $this->assertFalse($user->isTokenValid());
    }
    public function testIsValidTokenReturnsTrueIfNotExpired()
    {
        $user = UserFactory::new()->create([
            'accessToken' => 'token',
            'tokenExpiration' => new \DateTime('+1 day'),
        ]);

        $this->assertTrue($user->isTokenValid());
    }

    public function testIsValidTokenReturnsFalseIfNoExpirationSet()
    {
        $user = UserFactory::new()->create([
            'accessToken' => 'token',
            'tokenExpiration' => null,
        ]);

        $this->assertFalse($user->isTokenValid());
    }

    public function testIsValidTokenReturnsFalseIfNoTokenSet()
    {
        $user = UserFactory::new()->create([
            'accessToken' => null,
            'tokenExpiration' => new \DateTime('+1 day'),
        ]);

        $this->assertFalse($user->isTokenValid());
    }
}
