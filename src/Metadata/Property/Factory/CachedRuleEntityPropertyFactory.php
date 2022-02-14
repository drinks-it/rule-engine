<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Property\Factory;

use DrinksIt\RuleEngineBundle\Cache\CacheFactoryTrait;
use Psr\Cache\CacheItemPoolInterface;

final class CachedRuleEntityPropertyFactory implements RuleEntityPropertyFactoryInterface
{
    use CacheFactoryTrait;

    public const CACHE_KEY_PREFIX = 'rule_engine_class_properties_';

    private ?CacheItemPoolInterface $cacheItemPool;

    private RuleEntityPropertyFactoryInterface $decorated;

    public function __construct(?CacheItemPoolInterface $cacheItemPool, RuleEntityPropertyFactoryInterface $decorated)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->decorated = $decorated;
    }

    public function create(string $entityClass): array
    {
        $cacheKey = self::CACHE_KEY_PREFIX.md5($entityClass);

        return $this->getCached($cacheKey, fn () => $this->decorated->create($entityClass));
    }

    protected function getCacheItemPool(): ?CacheItemPoolInterface
    {
        return $this->cacheItemPool;
    }
}
