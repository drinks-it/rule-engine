<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\CachedCollectionRuleEntityResourceNameFactory;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\CollectionRuleEntityResourceNameFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityNameCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachedCollectionRuleEntityResourceNameFactoryTest extends TestCase
{
    /**
     * @dataProvider casesCachedCollections
     * @param mixed $exceptedResult
     */
    public function testCreate(callable $setMockCache, callable $setMockCollection, $exceptedResult): void
    {
        $mockCache = $this->createMock(CacheItemPoolInterface::class);
        $setMockCache($mockCache);

        $mockCollection = $this->createMock(CollectionRuleEntityResourceNameFactoryInterface::class);
        $setMockCollection($mockCollection);

        $ruleFactory = new CachedCollectionRuleEntityResourceNameFactory(
            $mockCache,
            $mockCollection
        );

        $this->assertEquals($exceptedResult, $ruleFactory->create());

        // check local cache
        $this->assertEquals($exceptedResult, $ruleFactory->create());

        // check empty cache
        $mockCollection = $this->createMock(CollectionRuleEntityResourceNameFactoryInterface::class);
        $setMockCollection($mockCollection, true);
        $ruleFactory = new CachedCollectionRuleEntityResourceNameFactory(null, $mockCollection);
        $this->assertEquals($exceptedResult, $ruleFactory->create());
    }

    public function casesCachedCollections(): array
    {
        return [
            'Empty Result. Cache empty' => [
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(false);

                    $mockItemPool->expects($this->once())->method('set')
                        ->with($this->isInstanceOf(ResourceRuleEntityNameCollection::class));

                    $cacheKey = CachedCollectionRuleEntityResourceNameFactory::CACHE_KEY_PREFIX;
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->once())->method('save')->with(
                        $this->equalTo($mockItemPool)
                    );
                },
                function (MockObject $decorated): void {
                    $decorated->expects($this->once())->method('create')
                        ->willReturn(new ResourceRuleEntityNameCollection());
                },
                new ResourceRuleEntityNameCollection(),
            ],
            'Has Result. Cache empty' => [
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(false);

                    $mockItemPool->expects($this->once())->method('set')
                        ->with($this->isInstanceOf(ResourceRuleEntityNameCollection::class));

                    $cacheKey = CachedCollectionRuleEntityResourceNameFactory::CACHE_KEY_PREFIX;
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->once())->method('save')->with(
                        $this->equalTo($mockItemPool)
                    );
                },
                function (MockObject $decorated): void {
                    $decorated->expects($this->once())->method('create')
                        ->willReturn(new ResourceRuleEntityNameCollection([
                            'ClassNameEntity',
                        ]));
                },
                new ResourceRuleEntityNameCollection([
                    'ClassNameEntity',
                ]),
            ],
            'Empty Result. Cache saved with empty' => [
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(true);
                    $mockItemPool->expects($this->once())->method('get')->willReturn(
                        new ResourceRuleEntityNameCollection()
                    );
                    $mockItemPool->expects($this->never())->method('set');

                    $cacheKey = CachedCollectionRuleEntityResourceNameFactory::CACHE_KEY_PREFIX;
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->never())->method('save');
                },
                function (MockObject $decorated, bool $isLocal = false): void {
                    if (!$isLocal) {
                        $decorated->expects($this->never())->method('create');

                        return;
                    }
                    $decorated->expects($this->once())->method('create')
                        ->willReturn(new ResourceRuleEntityNameCollection());
                },
                new ResourceRuleEntityNameCollection(),
            ],
            'Has Result. Cache saved with empty' => [
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(true);
                    $mockItemPool->expects($this->once())->method('get')->willReturn(
                        new ResourceRuleEntityNameCollection(['ClassNameEntity'])
                    );
                    $mockItemPool->expects($this->never())->method('set');

                    $cacheKey = CachedCollectionRuleEntityResourceNameFactory::CACHE_KEY_PREFIX;
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->never())->method('save');
                },
                function (MockObject $decorated, bool $isLocal = false): void {
                    if (!$isLocal) {
                        $decorated->expects($this->never())->method('create');

                        return;
                    }
                    $decorated->expects($this->once())->method('create')
                        ->willReturn(new ResourceRuleEntityNameCollection([
                            'ClassNameEntity',
                        ]));
                },
                new ResourceRuleEntityNameCollection([
                    'ClassNameEntity',
                ]),
            ],
        ];
    }
}
