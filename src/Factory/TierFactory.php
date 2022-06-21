<?php

namespace App\Factory;

use App\Entity\Tier;
use App\Repository\TierRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Tier>
 *
 * @method static Tier|Proxy createOne(array $attributes = [])
 * @method static Tier[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Tier|Proxy find(object|array|mixed $criteria)
 * @method static Tier|Proxy findOrCreate(array $attributes)
 * @method static Tier|Proxy first(string $sortedField = 'id')
 * @method static Tier|Proxy last(string $sortedField = 'id')
 * @method static Tier|Proxy random(array $attributes = [])
 * @method static Tier|Proxy randomOrCreate(array $attributes = [])
 * @method static Tier[]|Proxy[] all()
 * @method static Tier[]|Proxy[] findBy(array $attributes)
 * @method static Tier[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Tier[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TierRepository|RepositoryProxy repository()
 * @method Tier|Proxy create(array|callable $attributes = [])
 */
final class TierFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'nombre' => self::faker()->text(),
            'almacenamiento' => self::faker()->randomNumber(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Tier $tier): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Tier::class;
    }
}
