<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Doctrine\Serializer;

use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassDoesNotImplementInterfaceException;
use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassNotExistRuleEngineDoctrineException;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\DenormalizeCondition;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\NormalizeCondition;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\NumberConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use PHPUnit\Framework\TestCase;

class SerializerConditionTest extends TestCase
{
    /**
     * @dataProvider dataConditionsObjects
     * @dataProvider dataConditionsExceptions
     */
    public function testSerialize(
        CollectionConditionInterface $conditions,
        string $exceptExceptionClass = null,
        callable $modifyArrayDecoded = null
    ): void {
        if ($exceptExceptionClass) {
            $this->expectException($exceptExceptionClass);
        }
        $denormalize = new DenormalizeCondition($conditions);
        $arrayDecoded = $denormalize->denormalizeCollection();

        $this->assertIsArray($arrayDecoded);
        $this->assertNotEmpty($arrayDecoded);

        if ($modifyArrayDecoded) {
            $arrayDecoded =  $modifyArrayDecoded($arrayDecoded);
        }

        $normalize = new NormalizeCondition($arrayDecoded);
        $newCollectionConditions = $normalize->normalizeCollection();

        $this->assertEquals($conditions, $newCollectionConditions);
    }

    public function dataConditionsObjects(): iterable
    {
        yield 'Level 1' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\stdClass::class, 'test', NumberConditionAttribute::OPERATOR_EQ, 2)
                        ),
                    ])
                ),
            ]),
        ];

        yield 'Level 2' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\stdClass::class, 'test', NumberConditionAttribute::OPERATOR_EQ, 2)
                        ),
                        new Condition(
                            Condition::TYPE_ANY,
                            1,
                            null,
                            new CollectionCondition([
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test', NumberConditionAttribute::OPERATOR_EQ, 2)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test1', NumberConditionAttribute::OPERATOR_LT, 3)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test2', NumberConditionAttribute::OPERATOR_GTE, 4)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test3', NumberConditionAttribute::OPERATOR_LT, 5)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test4', NumberConditionAttribute::OPERATOR_EQ, 6)
                                ),
                            ])
                        ),
                    ])
                ),
            ]),
        ];

        yield 'Level 3' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\stdClass::class, 'test', NumberConditionAttribute::OPERATOR_EQ, 2)
                        ),
                        new Condition(
                            Condition::TYPE_ANY,
                            1,
                            null,
                            new CollectionCondition([
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test', NumberConditionAttribute::OPERATOR_EQ, 2)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test1', NumberConditionAttribute::OPERATOR_LT, 3)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test2', NumberConditionAttribute::OPERATOR_GTE, 4)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test3', NumberConditionAttribute::OPERATOR_LT, 5)
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'test4', NumberConditionAttribute::OPERATOR_EQ, 6)
                                ),
                                new Condition(
                                    Condition::TYPE_ALL,
                                    1,
                                    null,
                                    new CollectionCondition([
                                        new Condition(
                                            Condition::TYPE_ALL,
                                            1,
                                            null,
                                            new CollectionCondition([
                                                new Condition(
                                                    Condition::TYPE_ATTRIBUTE,
                                                    1,
                                                    new NumberConditionAttribute(\stdClass::class, 'test', NumberConditionAttribute::OPERATOR_EQ, 2)
                                                ),
                                                new Condition(
                                                    Condition::TYPE_ANY,
                                                    1,
                                                    null,
                                                    new CollectionCondition([
                                                        new Condition(
                                                            Condition::TYPE_ATTRIBUTE,
                                                            1,
                                                            new NumberConditionAttribute(\stdClass::class, 'test', NumberConditionAttribute::OPERATOR_EQ, 2)
                                                        ),
                                                        new Condition(
                                                            Condition::TYPE_ATTRIBUTE,
                                                            1,
                                                            new NumberConditionAttribute(\stdClass::class, 'test1', NumberConditionAttribute::OPERATOR_LT, 3)
                                                        ),
                                                        new Condition(
                                                            Condition::TYPE_ATTRIBUTE,
                                                            1,
                                                            new NumberConditionAttribute(\stdClass::class, 'test2', NumberConditionAttribute::OPERATOR_GTE, 4)
                                                        ),
                                                        new Condition(
                                                            Condition::TYPE_ATTRIBUTE,
                                                            1,
                                                            new NumberConditionAttribute(\stdClass::class, 'test3', NumberConditionAttribute::OPERATOR_LT, 5)
                                                        ),
                                                        new Condition(
                                                            Condition::TYPE_ATTRIBUTE,
                                                            1,
                                                            new NumberConditionAttribute(\stdClass::class, 'test4', NumberConditionAttribute::OPERATOR_EQ, 6)
                                                        ),
                                                    ])
                                                ),
                                            ])
                                        ),
                                    ]),
                                ),
                            ])
                        ),
                    ])
                ),
            ]),
        ];
    }

    public function dataConditionsExceptions(): iterable
    {
        yield 'Normalizer: ClassNotExistRuleEngineDoctrineException' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ATTRIBUTE,
                    1,
                    new NumberConditionAttribute(\stdClass::class, 'fieldName', 'EQ', 1)
                ),
            ]),
            ClassNotExistRuleEngineDoctrineException::class,
            function (array $decoded): array {
                $this->assertArrayHasKey('elements', $decoded);
                $this->assertArrayHasKey(0, $decoded['elements']);
                $this->assertArrayHasKey('attribute_condition', $decoded['elements'][0]);
                $this->assertArrayHasKey('class_condition', $decoded['elements'][0]['attribute_condition']);
                $this->assertEquals(NumberConditionAttribute::class, $decoded['elements'][0]['attribute_condition']['class_condition']);

                $decoded['elements'][0]['attribute_condition']['class_condition'] = 'NullClass';

                return $decoded;
            },
        ];

        yield 'Normalizer: ClassDoesNotImplementInterfaceException' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ATTRIBUTE,
                    1,
                    new NumberConditionAttribute(\stdClass::class, 'fieldName', 'EQ', 1)
                ),
            ]),
            ClassDoesNotImplementInterfaceException::class,
            function (array $decoded): array {
                $this->assertArrayHasKey('elements', $decoded);
                $this->assertArrayHasKey(0, $decoded['elements']);
                $this->assertArrayHasKey('attribute_condition', $decoded['elements'][0]);
                $this->assertArrayHasKey('class_condition', $decoded['elements'][0]['attribute_condition']);
                $this->assertEquals(NumberConditionAttribute::class, $decoded['elements'][0]['attribute_condition']['class_condition']);

                $decoded['elements'][0]['attribute_condition']['class_condition'] = \stdClass::class;

                return $decoded;
            },
        ];

        yield 'Normalizer Collection: ClassNotExistRuleEngineDoctrineException' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ATTRIBUTE,
                    1,
                    new NumberConditionAttribute(\stdClass::class, 'fieldName', 'EQ', 1)
                ),
            ]),
            ClassNotExistRuleEngineDoctrineException::class,
            function (array $decoded): array {
                $this->assertArrayHasKey('class_collection', $decoded);
                $decoded['class_collection'] = 'NullClassName';

                return $decoded;
            },
        ];
    }
}
