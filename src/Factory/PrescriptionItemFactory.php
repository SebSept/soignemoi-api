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
 *
 * @phpstan-method        Proxy<PrescriptionItem> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<PrescriptionItem> createOne(array $attributes = [])
 * @phpstan-method static Proxy<PrescriptionItem> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<PrescriptionItem> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<PrescriptionItem> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<PrescriptionItem> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<PrescriptionItem> random(array $attributes = [])
 * @phpstan-method static Proxy<PrescriptionItem> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<PrescriptionItem> repository()
 * @phpstan-method static list<Proxy<PrescriptionItem>> all()
 * @phpstan-method static list<Proxy<PrescriptionItem>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<PrescriptionItem>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<PrescriptionItem>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<PrescriptionItem>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<PrescriptionItem>> randomSet(int $number, array $attributes = [])
 */
final class PrescriptionItemFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'dosage' => self::faker()->text(255),
            'drug' => self::faker()->text(255),
            'prescription' => PrescriptionFactory::new(),
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
