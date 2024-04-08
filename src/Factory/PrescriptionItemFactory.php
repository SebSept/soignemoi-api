<?php

namespace App\Factory;

use App\Entity\PrescriptionItem;
use App\Repository\PrescriptionItemRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<PrescriptionItem>
 *
 * @method        PrescriptionItem|Proxy                     create(array|callable $attributes = [])
 * @method static PrescriptionItem|Proxy                     createOne(array $attributes = [])
 * @method static PrescriptionItem|Proxy                     find(object|array|mixed $criteria)
 * @method static PrescriptionItem|Proxy                     findOrCreate(array $attributes)
 * @method static PrescriptionItem|Proxy                     first(string $sortedField = 'id')
 * @method static PrescriptionItem|Proxy                     last(string $sortedField = 'id')
 * @method static PrescriptionItem|Proxy                     random(array $attributes = [])
 * @method static PrescriptionItem|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PrescriptionItemRepository|RepositoryProxy repository()
 * @method static PrescriptionItem[]|Proxy[]                 all()
 * @method static PrescriptionItem[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static PrescriptionItem[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static PrescriptionItem[]|Proxy[]                 findBy(array $attributes)
 * @method static PrescriptionItem[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PrescriptionItem[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PrescriptionItemFactory extends ModelFactory
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
            'dosage' => self::faker()->text(255),
            'drug' => self::faker()->text(255),
//            'prescription' => PrescriptionFactory::new(), // récurssion via la cascade doctrine
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(PrescriptionItem $prescriptionItem): void {})
        ;
    }

    protected static function getClass(): string
    {
        return PrescriptionItem::class;
    }
}
