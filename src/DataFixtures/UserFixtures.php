<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->many(10)->create();
        UserFactory::new()->create(
            [
                'email' => 'test@test.com',
                'password' => 'hello',
                'roles' => ['ROLE_ADMIN'],
                'access_token' => str_repeat('a', 32),
                'token_expiration' => new \DateTime('+30 day'),
            ]
        );
    }
}
