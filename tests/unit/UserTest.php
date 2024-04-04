<?php

namespace App\Tests\unit;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserTest extends KernelTestCase
{
    use Factories, ResetDatabase;

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
