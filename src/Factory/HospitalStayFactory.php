<?php

namespace App\Factory;

use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use DateTime;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<HospitalStay>
 *
 * @method        HospitalStay|Proxy                     create(array|callable $attributes = [])
 * @method static HospitalStay|Proxy                     createOne(array $attributes = [])
 * @method static HospitalStay|Proxy                     find(object|array|mixed $criteria)
 * @method static HospitalStay|Proxy                     findOrCreate(array $attributes)
 * @method static HospitalStay|Proxy                     first(string $sortedField = 'id')
 * @method static HospitalStay|Proxy                     last(string $sortedField = 'id')
 * @method static HospitalStay|Proxy                     random(array $attributes = [])
 * @method static HospitalStay|Proxy                     randomOrCreate(array $attributes = [])
 * @method static HospitalStayRepository|RepositoryProxy repository()
 * @method static HospitalStay[]|Proxy[]                 all()
 * @method static HospitalStay[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static HospitalStay[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static HospitalStay[]|Proxy[]                 findBy(array $attributes)
 * @method static HospitalStay[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static HospitalStay[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class HospitalStayFactory extends ModelFactory
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
            'doctor' => DoctorFactory::new(),
            'startDate' => self::faker()->dateTime(),
            'endDate' => self::faker()->dateTime(),
            'medicalSpeciality' => self::faker()->text(255),
            'patient' => PatientFactory::new(),
            'reason' => self::faker()->text(255),
        ];
    }

    public function entryBeforeToday(): self
    {
        return $this->addState(['startDate' => new DateTime('-' . rand(1, 25) . ' days')]);
    }

    public function entryAfterToday(): self
    {
        return $this->addState(['startDate' => new DateTime('+' . rand(1, 25) . ' days')]);
    }

    public function entryToday(): self
    {
        return $this->addState(['startDate' => new DateTime()]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(HospitalStay $hospitalStay): void {})
        ;
    }

    protected static function getClass(): string
    {
        return HospitalStay::class;
    }
}
