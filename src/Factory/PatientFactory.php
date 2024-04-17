<?php

namespace App\Factory;

use App\Entity\Patient;
use App\Repository\PatientRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Patient>
 *
 * @method        Patient|Proxy                     create(array|callable $attributes = [])
 * @method static Patient|Proxy                     createOne(array $attributes = [])
 * @method static Patient|Proxy                     find(object|array|mixed $criteria)
 * @method static Patient|Proxy                     findOrCreate(array $attributes)
 * @method static Patient|Proxy                     first(string $sortedField = 'id')
 * @method static Patient|Proxy                     last(string $sortedField = 'id')
 * @method static Patient|Proxy                     random(array $attributes = [])
 * @method static Patient|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PatientRepository|RepositoryProxy repository()
 * @method static Patient[]|Proxy[]                 all()
 * @method static Patient[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Patient[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Patient[]|Proxy[]                 findBy(array $attributes)
 * @method static Patient[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Patient[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PatientFactory extends ModelFactory
{
    public function withHospitalStays(): self
    {
        return $this->addState([
           'hospitalStays' => HospitalStayFactory::new()->many(7)
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'address1' => self::faker()->postcode().' '.self::faker()->city(),
            'address2' => self::faker()->address(),
            'firstname' => self::faker()->firstName(),
            'lastname' => self::faker()->lastName(),
            'password' => self::faker()->word(),
//            'hospitalStays' => HospitalStayFactory::new()->many(3,5), // cette factory va demander aussi la création d'un patient
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Patient $patient): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Patient::class;
    }
}
