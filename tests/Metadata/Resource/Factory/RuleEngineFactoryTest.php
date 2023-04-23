<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\CollectionRuleEntityResourceNameFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEngineFactory;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityCollection;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityNameCollection;
use PHPUnit\Framework\TestCase;

class RuleEngineFactoryTest extends TestCase
{
    /**
     * @param array $classNames
     * @param array $nullClasses
     * @dataProvider dataProviderCases
     */
    public function testCreate(array $classNames, array $nullClasses = []): void
    {
        $mockCollectionNames = $this->createMock(CollectionRuleEntityResourceNameFactoryInterface::class);
        $mockCollectionNames->expects($this->once())->method('create')
            ->willReturn(new ResourceRuleEntityNameCollection($classNames));

        $mockRuleEntityResource = $this->createMock(RuleEntityResourceFactoryInterface::class);

        $mockRuleEntityResource->method('create')->with($this->isType('string'))
            ->willReturnCallback(fn ($className) => \in_array($className, $nullClasses) ? null : new ResourceRuleEntity($className, []));

        $ruleEngine = new RuleEngineFactory($mockCollectionNames, $mockRuleEntityResource);
        $resourcesRuleEntity = $ruleEngine->create();

        $this->assertInstanceOf(ResourceRuleEntityCollection::class, $resourcesRuleEntity);
        $this->assertIsInt($resourcesRuleEntity->count());
        $this->assertEquals(\count($resourcesRuleEntity), \count($classNames) - \count($nullClasses));
    }

    public function dataProviderCases(): array
    {
        return [
            'Empty' => [
                [],
                [],
            ],
            'Fill' => [
                ['ClassName1', 'ClassName2', 'ClassName3'],
                [],
            ],
            'Exclude' => [
                ['ClassName1', 'ClassName2', 'ClassName3'],
                ['ClassName2'],
            ],
        ];
    }
}
