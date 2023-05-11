<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Cache\CacheFactoryTrait;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use Psr\Cache\CacheItemPoolInterface;

final class CachedRuleEntityResourceFactory implements RuleEntityResourceFactoryInterface
{
    use CacheFactoryTrait;

    public const CACHE_KEY_PREFIX = 'rule_engine_class_resource_';

    private ?CacheItemPoolInterface $cacheItemPool;

    private RuleEntityResourceFactoryInterface $decorated;

    public function __construct(?CacheItemPoolInterface $cacheItemPool, RuleEntityResourceFactoryInterface $decorated)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->decorated     = $decorated;
    }

    public function create(string $entityClass): ?ResourceRuleEntity
    {
        return $this->decorated->create($entityClass);
        $cacheKey = self::CACHE_KEY_PREFIX.\md5($entityClass);

        return $this->getCached($cacheKey, fn () => $this->decorated->create($entityClass));
    }

    protected function getCacheItemPool(): ?CacheItemPoolInterface
    {
        return $this->cacheItemPool;
    }
}
