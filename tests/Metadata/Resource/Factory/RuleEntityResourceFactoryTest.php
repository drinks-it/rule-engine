<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityExtractorInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\Factory\RuleEntityPropertyFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactory;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RuleEntityResourceFactoryTest extends TestCase
{
    /**
     * @dataProvider createClassCases
     * @param mixed $exceptedResult
     */
    public function testCreate(string $className, callable $setMockExtractor, callable $setMockProperty, $exceptedResult): void
    {
        $mockExtractor = $this->createMock(RuleEntityExtractorInterface::class);
        $setMockExtractor($mockExtractor);

        $mockPropertyFactory = $this->createMock(RuleEntityPropertyFactoryInterface::class);
        $setMockProperty($mockPropertyFactory);

        $ruleResourceFactory = new RuleEntityResourceFactory($mockExtractor, $mockPropertyFactory);

        $this->assertEquals($exceptedResult, $ruleResourceFactory->create($className));
    }

    public function createClassCases(): array
    {
        return [
            'Null Result' => [
                'ClassNameEntity',
                function (MockObject $extractor): void {
                    $extractor->expects($this->once())->method('getRuleEntityResourceAnnotation')
                        ->with($this->equalTo('ClassNameEntity'))
                        ->willReturn(null);
                },
                function (MockObject $property): void {
                    $property->expects($this->never())->method('create');
                },
                null,
            ],
            'Has resource empty properties' => [
                'ClassNameEntity',
                function (MockObject $extractor): void {
                    $extractor->expects($this->once())->method('getRuleEntityResourceAnnotation')
                        ->with($this->equalTo('ClassNameEntity'))
                        ->willReturn(
                            new RuleEntityResource()
                        );
                },
                function (MockObject $property): void {
                    $property->expects($this->once())->method('create')->willReturn([]);
                },
                new ResourceRuleEntity('ClassNameEntity', []),
            ],
            'Has resource with properties' => [
                'ClassNameEntity',
                function (MockObject $extractor): void {
                    $extractor->expects($this->once())->method('getRuleEntityResourceAnnotation')
                        ->with($this->equalTo('ClassNameEntity'))
                        ->willReturn(
                            new RuleEntityResource()
                        );
                },
                function (MockObject $property): void {
                    $property->expects($this->once())->method('create')->willReturn([
                        new PropertyRuleEntity('field_test', AttributeConditionTypeInterface::class),
                    ]);
                },
                new ResourceRuleEntity('ClassNameEntity', [
                    new PropertyRuleEntity('field_test', AttributeConditionTypeInterface::class),
                ]),
            ],
        ];
    }
}
