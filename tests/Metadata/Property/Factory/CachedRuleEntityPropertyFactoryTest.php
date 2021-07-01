<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Property\Factory;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Metadata\Property\Factory\CachedRuleEntityPropertyFactory;
use DrinksIt\RuleEngineBundle\Metadata\Property\Factory\RuleEntityPropertyFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CachedRuleEntityPropertyFactoryTest extends TestCase
{
    /**
     * @dataProvider providerDataFactory
     * @param mixed $className
     * @param mixed $exceptedResult
     */
    public function testFactory($className, callable $setMockCache, callable $setMockDecorated, $exceptedResult): void
    {
        $mockCached = $this->createMock(CacheItemPoolInterface::class);
        $setMockCache($mockCached);

        $mockDecorated = $this->createMock(RuleEntityPropertyFactoryInterface::class);
        $setMockDecorated($mockDecorated);

        $cachedFactory = new CachedRuleEntityPropertyFactory($mockCached, $mockDecorated);
        $this->assertEquals($exceptedResult, $cachedFactory->create($className));

        // check in from local storage. Code coverage from trait
        $this->assertEquals($exceptedResult, $cachedFactory->create($className));

        // check cached if item pool is null
        $mockDecorated = $this->createMock(RuleEntityPropertyFactoryInterface::class);
        $setMockDecorated($mockDecorated, true);
        $cachedFactory = new CachedRuleEntityPropertyFactory(null, $mockDecorated);
        $this->assertEquals($exceptedResult, $cachedFactory->create($className));
    }

    public function providerDataFactory(): array
    {
        $rule = new RuleEntityProperty();
        $rule->condition = AttributeConditionTypeInterface::class;

        return [
            'Empty result. By Check in Cache with empty storage' => [
                'ClassNameFactory',
                function (MockObject $mockCache): void {
                    $keyCache = CachedRuleEntityPropertyFactory::CACHE_KEY_PREFIX.md5('ClassNameFactory');

                    $mockCache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($keyCache))->willReturn(
                            $this->createMock(CacheItemInterface::class)
                        );
                },
                function (MockObject $mockDecorated): void {
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameFactory'))->willReturn([]);
                },
                [],
            ],
            'Empty result. By Check in Cache with CacheException' => [
                'ClassNameFactory',
                function (MockObject $mockCache): void {
                    $keyCache = CachedRuleEntityPropertyFactory::CACHE_KEY_PREFIX.md5('ClassNameFactory');
                    $this->expectException(InvalidArgumentException::class);

                    $mockCache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($keyCache))->willThrowException(new class() extends \Exception implements InvalidArgumentException {});
                },
                function (MockObject $mockDecorated): void {
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameFactory'))->willReturn([]);
                },
                [],
            ],
            'Has data in CacheItem. By Check in Cache with another' => [
                'ClassNameFactory',
                function (MockObject $mockCache): void {
                    $keyCache = CachedRuleEntityPropertyFactory::CACHE_KEY_PREFIX.md5('ClassNameFactory');

                    $cacheItemInterface =  $this->createMock(CacheItemInterface::class);

                    $mockCache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($keyCache))->willReturn($cacheItemInterface);

                    $cacheItemInterface->expects($this->once())->method('isHit')->willReturn(true);

                    $cacheItemInterface->expects($this->once())->method('get')
                        ->willReturn([
                            new PropertyRuleEntity('firstProperty', AttributeConditionTypeInterface::class),
                        ]);
                },
                function (MockObject $mockDecorated, $isLocal = false): void {
                    if ($isLocal) {
                        $mockDecorated->expects($this->once())->method('create')
                            ->with($this->equalTo('ClassNameFactory'))->willReturn([
                                new PropertyRuleEntity('firstProperty', AttributeConditionTypeInterface::class),
                            ]);

                        return;
                    }
                    $mockDecorated->expects($this->never())->method('create')
                        ->with($this->equalTo('ClassNameFactory'))->willReturn([]);
                },
                [
                    new PropertyRuleEntity('firstProperty', AttributeConditionTypeInterface::class),
                ],
            ],
            'Has data in CacheItem. By Check in Cache no data. isHit false' => [
                'ClassNameFactory',
                function (MockObject $mockCache): void {
                    $keyCache = CachedRuleEntityPropertyFactory::CACHE_KEY_PREFIX.md5('ClassNameFactory');

                    $cacheItemInterface =  $this->createMock(CacheItemInterface::class);

                    $mockCache->expects($this->once())->method('getItem')
                        ->with($this->equalTo($keyCache))->willReturn($cacheItemInterface);

                    $cacheItemInterface->expects($this->once())->method('isHit')->willReturn(false);

                    $cacheItemInterface->expects($this->never())->method('get');
                },
                function (MockObject $mockDecorated): void {
                    $mockDecorated->expects($this->once())->method('create')
                        ->with($this->equalTo('ClassNameFactory'))->willReturn([
                            new PropertyRuleEntity('firstProperty', AttributeConditionTypeInterface::class),
                        ]);
                },
                [
                    new PropertyRuleEntity('firstProperty', AttributeConditionTypeInterface::class),
                ],
            ],
        ];
    }
}
