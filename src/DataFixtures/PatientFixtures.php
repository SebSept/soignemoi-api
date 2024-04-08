<?php

namespace App\DataFixtures;

use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Zenstruck\Foundry\anonymous;

class PatientFixtures extends Fixture
{
    public function load(ObjectManager $objectManager): void
    {
        // @todo utiliser factory
        $generator = Factory::create('fr_FR');
        $factory = anonymous(Patient::class);

        $factory->createMany(
            40,
            static fn (): array => [
                'firstname' => $generator->firstName(),
                'lastname' => $generator->lastName(),
                'address1' => $generator->streetAddress(),
                'address2' => $generator->postcode.' '.$generator->city(),
                'password' => password_hash($generator->password(), null, ['cost' => 4]), // 4 est la plus petite valeur possible.
            ]
        );
    }
}
