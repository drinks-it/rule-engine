<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\CachedRuleEntityResourceFactory;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachedRuleEntityResourceFactoryTest extends TestCase
{
    /**
     * @dataProvider cacheClassCases
     * @param mixed $exceptedResult
     */
    public function testCreate(string $className, callable $setMockCache, callable $setMockDecorated, $exceptedResult): void
    {
        $mockCache = $this->createMock(CacheItemPoolInterface::class);
        $setMockCache($mockCache);

        $mockDecorated = $this->createMock(RuleEntityResourceFactoryInterface::class);
        $setMockDecorated($mockDecorated);

        $ruleCachedFactory = new CachedRuleEntityResourceFactory($mockCache, $mockDecorated);

        $this->assertEquals($exceptedResult, $ruleCachedFactory->create($className));

        // Cache is null
        $mockDecorated = $this->createMock(RuleEntityResourceFactoryInterface::class);
        $setMockDecorated($mockDecorated, true);

        $ruleCachedFactory = new CachedRuleEntityResourceFactory(null, $mockDecorated);

        $this->assertEquals($exceptedResult, $ruleCachedFactory->create($className));
    }

    public function cacheClassCases(): array
    {
        return [
            'Empty Result. Cache saved with empty' => [
                'ClassNameEntity',
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(false);

                    $mockItemPool->expects($this->once())->method('set')
                        ->with($this->equalTo(null));

                    $cacheKey = CachedRuleEntityResourceFactory::CACHE_KEY_PREFIX . md5('ClassNameEntity');
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->once())->method('save')->with(
                        $this->equalTo($mockItemPool)
                    );
                },
                function (MockObject $mockDecorated): void {
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameEntity'))->willReturn(null);
                },
                null,
            ],
            'Has resource empty properties. Cached is empty' => [
                'ClassNameEntity',
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(false);

                    $mockItemPool->expects($this->once())->method('set')
                        ->with($this->isInstanceOf(ResourceRuleEntity::class));

                    $cacheKey = CachedRuleEntityResourceFactory::CACHE_KEY_PREFIX . md5('ClassNameEntity');
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->once())->method('save')->with(
                        $this->equalTo($mockItemPool)
                    );
                },
                function (MockObject $mockDecorated): void {
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameEntity'))->willReturn(
                            new ResourceRuleEntity('ClassNameEntity', [])
                        );
                },
                new ResourceRuleEntity('ClassNameEntity', []),
            ],
            'Has resource with properties. Cached is empty' => [
                'ClassNameEntity',
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(false);

                    $mockItemPool->expects($this->once())->method('set')
                        ->with($this->isInstanceOf(ResourceRuleEntity::class));

                    $cacheKey = CachedRuleEntityResourceFactory::CACHE_KEY_PREFIX . md5('ClassNameEntity');
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->once())->method('save')->with(
                        $this->equalTo($mockItemPool)
                    );
                },
                function (MockObject $mockDecorated): void {
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameEntity'))->willReturn(
                            new ResourceRuleEntity('ClassNameEntity', [
                                new PropertyRuleEntity('fieldName', AttributeConditionTypeInterface::class),
                            ])
                        );
                },
                new ResourceRuleEntity('ClassNameEntity', [
                    new PropertyRuleEntity('fieldName', AttributeConditionTypeInterface::class),
                ]),
            ],
            'Empty Result. Cache saved with empty data' => [
                'ClassNameEntity',
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(true);
                    $mockItemPool->expects($this->once())->method('get')->willReturn(null);

                    $mockItemPool->expects($this->never())->method('set');

                    $cacheKey = CachedRuleEntityResourceFactory::CACHE_KEY_PREFIX . md5('ClassNameEntity');
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->never())->method('save');
                },
                function (MockObject $mockDecorated, bool $isLocal = false): void {
                    if ($isLocal) {
                        $mockDecorated->expects($this->once())->method('create')->willReturn(null);

                        return;
                    }
                    $mockDecorated->expects($this->never())->method('create');
                },
                null,
            ],
            'Has resource empty properties. Cached is saved' => [
                'ClassNameEntity',
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(true);
                    $mockItemPool->expects($this->once())->method('get')->willReturn(
                        new ResourceRuleEntity('ClassNameEntity', [])
                    );

                    $mockItemPool->expects($this->never())->method('set');

                    $cacheKey = CachedRuleEntityResourceFactory::CACHE_KEY_PREFIX . md5('ClassNameEntity');
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->never())->method('save');
                },
                function (MockObject $mockDecorated, bool $isLocal = false): void {
                    if (!$isLocal) {
                        $mockDecorated->expects($this->never())->method('create');

                        return;
                    }
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameEntity'))->willReturn(
                            new ResourceRuleEntity('ClassNameEntity', [])
                        );
                },
                new ResourceRuleEntity('ClassNameEntity', []),
            ],
            'Has resource with properties. Cached is saved' => [
                'ClassNameEntity',
                function (MockObject $cache): void {
                    $mockItemPool = $this->createMock(CacheItemInterface::class);
                    $mockItemPool->expects($this->once())->method('isHit')->willReturn(true);
                    $mockItemPool->expects($this->once())->method('get')->willReturn(
                        new ResourceRuleEntity('ClassNameEntity', [
                            new PropertyRuleEntity('fieldName', AttributeConditionTypeInterface::class),
                        ]),
                    );

                    $mockItemPool->expects($this->never())->method('set');

                    $cacheKey = CachedRuleEntityResourceFactory::CACHE_KEY_PREFIX . md5('ClassNameEntity');
                    $cache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($cacheKey))->willReturn($mockItemPool);

                    $cache->expects($this->never())->method('save');
                },
                function (MockObject $mockDecorated, bool $isLocal = false): void {
                    if (!$isLocal) {
                        $mockDecorated->expects($this->never())->method('create');

                        return;
                    }
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameEntity'))->willReturn(
                            new ResourceRuleEntity('ClassNameEntity', [
                                new PropertyRuleEntity('fieldName', AttributeConditionTypeInterface::class),
                            ]),
                        );
                },
                new ResourceRuleEntity('ClassNameEntity', [
                    new PropertyRuleEntity('fieldName', AttributeConditionTypeInterface::class),
                ]),
            ],
        ];
    }
}
