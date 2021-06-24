<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Cache\CacheFactoryTrait;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityNameCollection;
use Psr\Cache\CacheItemPoolInterface;

final class CachedCollectionRuleEntityResourceNameFactory implements CollectionRuleEntityResourceNameFactoryInterface
{
    use CacheFactoryTrait;

    public const CACHE_KEY_PREFIX = 'rule_entity_collection_names';

    private ?CacheItemPoolInterface $cacheItemPool;

    private CollectionRuleEntityResourceNameFactoryInterface $decorated;

    public function __construct(
        ?CacheItemPoolInterface $cacheItemPool,
        CollectionRuleEntityResourceNameFactoryInterface $decorated
    ) {
        $this->cacheItemPool = $cacheItemPool;
        $this->decorated = $decorated;
    }

    /**
     * @inheritDoc
     */
    public function create(): ResourceRuleEntityNameCollection
    {
        return $this->getCached(self::CACHE_KEY_PREFIX, fn () => $this->decorated->create());
    }

    protected function getCacheItemPool(): ?CacheItemPoolInterface
    {
        return $this->cacheItemPool;
    }
}
