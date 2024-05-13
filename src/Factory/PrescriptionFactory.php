<?php

namespace App\Factory;

use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Prescription>
 *
 * @method        Prescription|Proxy                     create(array|callable $attributes = [])
 * @method static Prescription|Proxy                     createOne(array $attributes = [])
 * @method static Prescription|Proxy                     find(object|array|mixed $criteria)
 * @method static Prescription|Proxy                     findOrCreate(array $attributes)
 * @method static Prescription|Proxy                     first(string $sortedField = 'id')
 * @method static Prescription|Proxy                     last(string $sortedField = 'id')
 * @method static Prescription|Proxy                     random(array $attributes = [])
 * @method static Prescription|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PrescriptionRepository|RepositoryProxy repository()
 * @method static Prescription[]|Proxy[]                 all()
 * @method static Prescription[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Prescription[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Prescription[]|Proxy[]                 findBy(array $attributes)
 * @method static Prescription[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Prescription[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PrescriptionFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'doctor' => DoctorFactory::new(),
//            'patient' => PatientFactory::new(),
            'items' => PrescriptionItemFactory::new()->many(random_int(1, 5)),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Prescription $prescription): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Prescription::class;
    }
}
