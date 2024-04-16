<?php

namespace App\Factory;

use App\Entity\Doctor;
use App\Repository\DoctorRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Doctor>
 *
 * @method        Doctor|Proxy                     create(array|callable $attributes = [])
 * @method static Doctor|Proxy                     createOne(array $attributes = [])
 * @method static Doctor|Proxy                     find(object|array|mixed $criteria)
 * @method static Doctor|Proxy                     findOrCreate(array $attributes)
 * @method static Doctor|Proxy                     first(string $sortedField = 'id')
 * @method static Doctor|Proxy                     last(string $sortedField = 'id')
 * @method static Doctor|Proxy                     random(array $attributes = [])
 * @method static Doctor|Proxy                     randomOrCreate(array $attributes = [])
 * @method static DoctorRepository|RepositoryProxy repository()
 * @method static Doctor[]|Proxy[]                 all()
 * @method static Doctor[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Doctor[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Doctor[]|Proxy[]                 findBy(array $attributes)
 * @method static Doctor[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Doctor[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class DoctorFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'firstname' => self::faker()->firstName(),
            'lastname' => self::faker()->lastName(),
            'medicalSpeciality' => self::faker()
                ->randomElement([
                    'cardilolgie', 'oncologie', 'dermatologie', 'pédiatrie', 'gynécologie', 'urologie', 'neurologie', 'psychiatrie', 'ophtalmologie', 'ORL']),
            'employeeId' => self::faker()->numerify('##-####-###'),
            'password' => password_hash(self::faker()->unique()->password(), null, ['cost' => 4]),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Doctor $doctor): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Doctor::class;
    }
}
