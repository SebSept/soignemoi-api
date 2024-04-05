<?php

namespace App\Factory;

use App\Entity\MedicalOpinion;
use App\Repository\MedicalOpinionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<MedicalOpinion>
 *
 * @method        MedicalOpinion|Proxy                     create(array|callable $attributes = [])
 * @method static MedicalOpinion|Proxy                     createOne(array $attributes = [])
 * @method static MedicalOpinion|Proxy                     find(object|array|mixed $criteria)
 * @method static MedicalOpinion|Proxy                     findOrCreate(array $attributes)
 * @method static MedicalOpinion|Proxy                     first(string $sortedField = 'id')
 * @method static MedicalOpinion|Proxy                     last(string $sortedField = 'id')
 * @method static MedicalOpinion|Proxy                     random(array $attributes = [])
 * @method static MedicalOpinion|Proxy                     randomOrCreate(array $attributes = [])
 * @method static MedicalOpinionRepository|RepositoryProxy repository()
 * @method static MedicalOpinion[]|Proxy[]                 all()
 * @method static MedicalOpinion[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static MedicalOpinion[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static MedicalOpinion[]|Proxy[]                 findBy(array $attributes)
 * @method static MedicalOpinion[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static MedicalOpinion[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class MedicalOpinionFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'description' => self::faker()->text(),
            'patient' => PatientFactory::new(),
            'doctor' => DoctorFactory::new(),
            'title' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(MedicalOpinion $medicalOpinion): void {})
        ;
    }

    protected static function getClass(): string
    {
        return MedicalOpinion::class;
    }
}
