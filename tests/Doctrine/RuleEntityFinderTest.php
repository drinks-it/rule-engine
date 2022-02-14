<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Doctrine;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ObjectManager;
use DrinksIt\RuleEngineBundle\Doctrine\RuleEntityFinder;
use DrinksIt\RuleEngineBundle\Doctrine\RuleEntityFinderExtensionInterface;
use DrinksIt\RuleEngineBundle\Doctrine\RuleEntityFinderInterface;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class RuleEntityFinderTest extends TestCase
{
    /**
     * @return MockObject|ManagerRegistry
     */
    private function makeManageRegistry()
    {
        $manageRegistry = $this->createMock(ManagerRegistry::class);
        $objectManager = $this->createMock(ObjectManager::class);

        $ClassMetadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataReturn = [
            (function (MockObject $mockObject) {
                $reflection = $this->createMock(\ReflectionClass::class);
                $reflection->expects($this->once())->method('getInterfaceNames')
                    ->willReturn([
                        RuleEntityInterface::class,
                    ]);

                $mockObject->expects($this->once())->method('getName')->willReturn('ShortName');
                $mockObject->expects($this->once())->method('getReflectionClass')
                    ->willReturn($reflection);

                return $mockObject;
            })($this->createMock(ClassMetadata::class)),
        ];

        $ClassMetadataFactory->expects($this->once())->method('getAllMetadata')
            ->willReturn($manageRegistry)->willReturn($metadataReturn);
        $objectManager->expects($this->once())->method('getMetadataFactory')->willReturn($ClassMetadataFactory);
        $manageRegistry->expects($this->once())->method('getManager')->willReturn($objectManager);

        return $manageRegistry;
    }

    public function testInitWithNotRuleExtension(): void
    {
        $manageRegistry = $this->makeManageRegistry();

        $ruleEntityFinder = new RuleEntityFinder($manageRegistry, [
            new class () {},
        ]);
        $this->assertInstanceOf(RuleEntityFinderInterface::class, $ruleEntityFinder);
    }

    public function testInitWithEmptyRuleExtensionWithoutRepository(): void
    {
        $manageRegistry = $this->makeManageRegistry();
        $manageRegistry->expects($this->once())->method('getRepository')
            ->with($this->equalTo('ShortName'))->willReturn($manageRegistry);

        $ruleEntityFinder = new RuleEntityFinder($manageRegistry, []);
        $rules = $ruleEntityFinder->getRulesByEventName('EventClassName');
        $this->assertEquals([], $rules);
    }

    public function testInitWithRuleExtensionSupportFalse(): void
    {
        $exceptedResult = [
            $this->createMock(RuleEntityInterface::class),
        ];

        $manageRegistry = $this->makeManageRegistry();

        $builder = $this->createMock(QueryBuilder::class);

        $builder->expects($this->once())->method('setParameter')->with(
            $this->equalTo(':classEventName'),
            $this->equalTo('EventClassName')
        )->willReturnSelf();
        $builder->expects($this->once())->method('orderBy')->with($this->isType('string'), $this->isType('string'));

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())->method('getResult')->willReturn([
            $this->createMock(RuleEntityInterface::class),
        ]);
        $builder->expects($this->once())->method('getQuery')->willReturn($query);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('rule'))->willReturn($builder);

        $manageRegistry->expects($this->once())->method('getRepository')
            ->with($this->equalTo('ShortName'))->willReturn($repository);

        $extension = $this->createMock(RuleEntityFinderExtensionInterface::class);

        $extension->expects($this->once())->method('supportExtension')->with('EventClassName')->willReturn(false);

        $ruleEntityFinder = new RuleEntityFinder($manageRegistry, [
            $extension,
        ]);

        $this->assertEquals($exceptedResult, $ruleEntityFinder->getRulesByEventName('EventClassName'));
    }

    public function testInitWithRuleExtensionSupported(): void
    {
        $exceptedResult = [
            $this->createMock(RuleEntityInterface::class),
        ];

        $manageRegistry = $this->makeManageRegistry();

        $builder = $this->createMock(QueryBuilder::class);

        $builder->expects($this->once())->method('setParameter')->with(
            $this->equalTo(':classEventName'),
            $this->equalTo('EventClassName')
        )->willReturnSelf();
        $builder->expects($this->once())->method('orderBy')->with($this->isType('string'), $this->isType('string'));

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())->method('getResult')->willReturn([
            $this->createMock(RuleEntityInterface::class),
        ]);
        $builder->expects($this->once())->method('getQuery')->willReturn($query);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('rule'))->willReturn($builder);

        $manageRegistry->expects($this->once())->method('getRepository')
            ->with($this->equalTo('ShortName'))->willReturn($repository);

        $extension = $this->createMock(RuleEntityFinderExtensionInterface::class);

        $extension->expects($this->once())->method('supportExtension')->with('EventClassName')->willReturn(true);

        $extension->expects($this->once())->method('updateQueryBuilder');

        $ruleEntityFinder = new RuleEntityFinder($manageRegistry, [
            $extension,
        ]);

        $this->assertEquals($exceptedResult, $ruleEntityFinder->getRulesByEventName('EventClassName'));
    }
}
