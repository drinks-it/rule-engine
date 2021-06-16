<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * @internal
 */
trait CacheFactoryTrait
{
    private array $localStorageCache = [];

    private function getCached(string $key, callable $getValue)
    {
        if (\array_key_exists($key, $this->localStorageCache)) {
            return $this->localStorageCache[$key];
        }

        $cacheItemPool = $this->getCacheItemPool();

        if (!$cacheItemPool) {
            return $this->localStorageCache[$key] = $getValue();
        }

        try {
            $cachePool = $cacheItemPool->getItem($key);
        } catch (InvalidArgumentException $e) {
            return $this->localStorageCache[$key] = $getValue();
        }

        if ($cachePool->isHit()) {
            return $this->localStorageCache[$key] = $cachePool->get();
        }

        $this->localStorageCache[$key] = $getValue();
        $cachePool->set($this->localStorageCache[$key]);
        $cacheItemPool->save($cachePool);

        return $this->localStorageCache[$key];
    }

    abstract protected function getCacheItemPool(): ?CacheItemPoolInterface;
}
