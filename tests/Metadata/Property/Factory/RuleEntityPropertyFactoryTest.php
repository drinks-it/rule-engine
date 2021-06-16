<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Property\Factory;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityExtractorInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\Factory\RuleEntityPropertyFactory;
use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Rule\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RuleEntityPropertyFactoryTest extends TestCase
{
    /**
     * @dataProvider providerDataFactory
     * @param mixed $classEntity
     * @param mixed $resultExcept
     */
    public function testFactory($classEntity, callable $setMockEvents, $resultExcept = []): void
    {
        $mockExtractor = $this->createMock(RuleEntityExtractorInterface::class);
        $setMockEvents($mockExtractor);

        $ruleEntityPropertyFactory = new RuleEntityPropertyFactory(
            $mockExtractor
        );

        $actualResult = $ruleEntityPropertyFactory->create($classEntity);
        $this->assertIsArray($actualResult);
        $this->assertEquals($resultExcept, $actualResult);
    }

    public function providerDataFactory(): array
    {
        $rule = new RuleEntityProperty();
        $rule->interfaceType = AttributeConditionTypeInterface::class;

        return [
            'Empty result' => [
                'ClassNameFactory',
                function (MockObject $extractor): void {
                    $extractor->expects($this->once())->method('getRuleEntityPropertiesNames')
                        ->with($this->equalTo('ClassNameFactory'))->willReturn([]);
                },
                [],
            ],
            'Empty result. Has resource doesn\'t have properties' => [
                'ClassNameFactory',
                function (MockObject $extractor): void {
                    $extractor->expects($this->once())->method('getRuleEntityPropertiesNames')
                        ->with($this->equalTo('ClassNameFactory'))->willReturn([
                            'firstProperty',
                            'secondProperty',
                        ]);

                    $extractor->expects($this->exactly(2))->method('getRuleEntityPropertyAnnotation')
                        ->with(
                            $this->equalTo('ClassNameFactory'),
                            $this->isType('string')
                        )->willReturn(null);
                },
                [],
            ],
            'Check result' => [
                'ClassNameFactory',
                function (MockObject $extractor) use ($rule): void {
                    $extractor->expects($this->once())->method('getRuleEntityPropertiesNames')
                        ->with($this->equalTo('ClassNameFactory'))->willReturn([
                            'firstProperty',
                            'secondProperty',
                        ]);

                    $extractor->expects($this->exactly(2))->method('getRuleEntityPropertyAnnotation')
                        ->with(
                            $this->equalTo('ClassNameFactory'),
                            $this->isType('string')
                        )->willReturnCallback(fn ($className, $property) => $rule);
                },
                [
                    new PropertyRuleEntity('firstProperty', $rule->interfaceType),
                    new PropertyRuleEntity('secondProperty', $rule->interfaceType),
                ],
            ],
        ];
    }
}
