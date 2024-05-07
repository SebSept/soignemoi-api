<?php

namespace App\Factory;

use App\Entity\Doctor;
use App\Entity\HospitalStay;
use App\Entity\Patient;
use App\Repository\HospitalStayRepository;
use App\Repository\PatientRepository;
use DateTime;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;
use function Zenstruck\Foundry\faker;
use function Zenstruck\Foundry\repository;

/**
 * @extends ModelFactory<HospitalStay>
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

    public function withExistingPatient(): self
    {
        return $this->addState(
            fn() => ['patient' => PatientFactory::repository()->random()]
        );

    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        $randomDateGenerator = static fn() => faker()->dateTimeBetween('-4 months', '4 months');

        $doctorRepository = repository(Doctor::class);

        $startDate = $randomDateGenerator();
        $endDate = (clone $startDate)->modify('+' . faker()->numberBetween(0, 5) . ' days');
        $checkIn = null;
        $checkOut = null;
        if ($startDate <= new DateTime()) {
            $checkIn = (clone $startDate)->modify('+' . faker()->numberBetween(6, 12) . ' hours');
        }

        if ($endDate <= (new DateTime())->modify('+1 day')) {
            $checkOut = (clone $endDate)->modify('+' . faker()->numberBetween(13, 23) . ' hours');
        }

        if (DoctorFactory::repository()->count() === 0) {
            DoctorFactory::new()->create();
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'doctor' => $doctorRepository->random(),
            'medicalSpeciality' => self::faker()
                ->randomElement(['cardilolgie', 'oncologie', 'dermatologie', 'pédiatrie', 'gynécologie', 'urologie', 'neurologie', 'psychiatrie', 'ophtalmologie', 'ORL']),
            'reason' => self::faker()->text(255),
        ];
    }

    public function entryBeforeToday(): self
    {
        return $this->addState( function () {
            $startDate = new DateTime('-' . random_int(1, 25) . ' days');
            $checkinDate = (clone $startDate)->modify('+ '.random_int(1, 8).' hours');
            return [
                'startDate' => $startDate,
                'checkin' => $checkinDate
            ];
        });
    }

    public function entryAfterToday(): self
    {
        return $this->addState( function () {
            $startDate = new DateTime('+' . random_int(1, 25) . ' days');
            return ['startDate' => $startDate];
        });
    }

    public function entryToday(): self
    {
        return $this->addState( function () {
         return [
             'startDate' => new DateTime(),
             'checkin' => new DateTime('+' . random_int(1, 25) . ' hours')
             ];
        });
    }

    public
    function exitBeforeToday(): self
    {
        return $this->addState( function() {
            $endDate = new DateTime('-' . random_int(1, 25) . ' days');
            $checkout = (clone $endDate)->modify('+ '.random_int(1,7).' hours');
            return [
                'endDate' => $endDate,
                'checkout' => $checkout
            ];
        }
        );
    }

    public
    function exitAfterToday(): self
    {
        return $this->addState(fn() => ['endDate' => new DateTime('+' . random_int(1, 25) . ' days')]);
    }

    public
    function exitToday(): self
    {
        return $this->addState(['endDate' => new DateTime()]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected
    function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(HospitalStay $hospitalStay): void {})
        ;
    }

    protected
    static function getClass(): string
    {
        return HospitalStay::class;
    }
}
