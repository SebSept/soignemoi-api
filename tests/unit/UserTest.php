<?php

namespace App\Tests\unit;

use DateTime;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIsValidTokenReturnsFalseIfExpired(): void
    {
        $user = UserFactory::new()->create([
            'accessToken' => 'token',
            'tokenExpiration' => new DateTime('-1 day'),
        ]);

        $this->assertFalse($user->isTokenValid());
    }

    public function testIsValidTokenReturnsTrueIfNotExpired(): void
    {
        $user = UserFactory::new()->create([
            'accessToken' => 'token',
            'tokenExpiration' => new DateTime('+1 day'),
        ]);

        $this->assertTrue($user->isTokenValid());
    }

    public function testIsValidTokenReturnsFalseIfNoExpirationSet(): void
    {
        $user = UserFactory::new()->create([
            'accessToken' => 'token',
            'tokenExpiration' => null,
        ]);

        $this->assertFalse($user->isTokenValid());
    }

    public function testIsValidTokenReturnsFalseIfNoTokenSet(): void
    {
        $user = UserFactory::new()->create([
            'accessToken' => null,
            'tokenExpiration' => new DateTime('+1 day'),
        ]);

        $this->assertFalse($user->isTokenValid());
    }
}
