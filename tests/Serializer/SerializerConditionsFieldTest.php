<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\IntegerConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\NumberConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Serializer\Exception\NotExistPropertyInterfaceException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\PropertyNotFoundException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceIsNotIdentifyException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceNotFoundException;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsField;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SerializerConditionsFieldTest extends TestCase
{
    public const COLLECTION_CONDITION = CollectionCondition::class;

    public function testInit(): void
    {
        $this->assertInstanceOf(SerializerConditionsFieldInterface::class, $this->makeSerializerConditionsFieldInterface());
    }

    /**
     * @dataProvider providerDecodeToConditionCollection
     * @dataProvider providerDecodeToConditionCollectionException
     * @param null|mixed $exceptedException
     * @throws
     */
    public function testDecodeToConditionCollection(SerializerConditionsFieldInterface $serializerConditionsField, array $conditions, array $context, $exceptedException = null): void
    {
        if ($exceptedException) {
            $this->expectException($exceptedException);
        }
        $result = $serializerConditionsField->decodeToConditionCollection($conditions, $context);
        $this->assertInstanceOf(CollectionConditionInterface::class, $result);

        if (\array_key_exists('rule_default_resource', $context)) {
            foreach ($conditions as &$condition) {
                if (!\array_key_exists('subConditions', $condition)) {
                    continue;
                }

                foreach ($condition['subConditions'] as &$conditionSub) {
                    $conditionSub['resource'] = $context['rule_default_resource'];
                }
            }
        }

        $this->assertEquals($conditions, $serializerConditionsField->encodeCollectionConditionToArray($result));
    }

    public function providerDecodeToConditionCollection(): iterable
    {
        yield 'Empty' => [
            $this->makeSerializerConditionsFieldInterface(),
            [],
            [],
            null,
        ];

        yield 'Check. Empty context' => [
            $this->makeSerializerConditionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')
                    ->with($this->equalTo('ResourceName'))
                    ->willReturn(
                        new ResourceRuleEntity('ResourceName', [
                            'test' => new PropertyRuleEntity('test', IntegerConditionAttribute::class),
                        ])
                    );
            }),
            [
                [
                    'type' => Condition::TYPE_ANY,
                    'exceptedResult' => true,
                    'subConditions' => [
                        [
                            'resource' => 'ResourceName',
                            'type' => Condition::TYPE_ATTRIBUTE,
                            'field' => 'test',
                            'operator' => 'EQ',
                            'value' => 2,
                        ],
                    ],
                ],
            ],
            [],
        ];

        yield 'Check. With default resource in context' => [
            $this->makeSerializerConditionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')
                    ->with($this->equalTo('ResourceName'))
                    ->willReturn(
                        new ResourceRuleEntity('ResourceName', [
                            'test' => new PropertyRuleEntity('test', IntegerConditionAttribute::class),
                        ])
                    );
            }),
            [
                [
                    'type' => Condition::TYPE_ANY,
                    'exceptedResult' => false,
                    'subConditions' => [
                        [
                            'type' => Condition::TYPE_ATTRIBUTE,
                            'field' => 'test',
                            'operator' => 'EQ',
                            'value' => 2,
                        ],
                    ],
                ],
            ],
            [
                'rule_default_resource' => 'ResourceName',
            ],
        ];
    }

    public function providerDecodeToConditionCollectionException(): iterable
    {
        yield 'Resource Is Not Identify Exception' => [
            $this->makeSerializerConditionsFieldInterface(),
            [
                [
                    'type' => Condition::TYPE_ATTRIBUTE,
                    'field' => 'test',
                    'operator' => 'EQ',
                    'value' => 2,
                ],
            ],
            [],
            ResourceIsNotIdentifyException::class,
        ];

        yield 'Resource Not Found Exception. Empty' => [
            $this->makeSerializerConditionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')
                    ->with(
                        $this->equalTo('ResourceClassName')
                    )->willReturn(null);
            }),
            [
                [
                    'type' => Condition::TYPE_ATTRIBUTE,
                    'field' => 'test',
                    'operator' => 'EQ',
                    'value' => 2,
                ],
            ],
            [
                'rule_default_resource' => 'ResourceClassName',
            ],
            ResourceNotFoundException::class,
        ];

        yield 'PropertyNotFoundException' => [
            $this->makeSerializerConditionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')
                    ->with(
                        $this->equalTo('ResourceClassName')
                    )->willReturn(new ResourceRuleEntity('ResourceClassName', []));
            }),
            [
                [
                    'type' => Condition::TYPE_ATTRIBUTE,
                    'field' => 'test',
                    'operator' => 'EQ',
                    'value' => 2,
                ],
            ],
            [
                'rule_default_resource' => 'ResourceClassName',
            ],
            PropertyNotFoundException::class,
        ];

        yield 'Not Exist Property Interface Exception. Empty' => [
            $this->makeSerializerConditionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')
                    ->with(
                        $this->equalTo('ResourceClassName')
                    )->willReturn(new ResourceRuleEntity('ResourceClassName', [
                        'test' => new PropertyRuleEntity('test', null),
                    ]));
            }),
            [
                [
                    'type' => Condition::TYPE_ATTRIBUTE,
                    'field' => 'test',
                    'operator' => 'EQ',
                    'value' => 2,
                ],
            ],
            [
                'rule_default_resource' => 'ResourceClassName',
            ],
            NotExistPropertyInterfaceException::class,
        ];

        yield 'Not Exist Property Interface Exception' => [
            $this->makeSerializerConditionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')
                    ->with(
                        $this->equalTo('ResourceClassName')
                    )->willReturn(new ResourceRuleEntity('ResourceClassName', [
                        'test' => new PropertyRuleEntity('test', \stdClass::class),
                    ]));
            }),
            [
                [
                    'type' => Condition::TYPE_ATTRIBUTE,
                    'field' => 'test',
                    'operator' => 'EQ',
                    'value' => 2,
                ],
            ],
            [
                'rule_default_resource' => 'ResourceClassName',
            ],
            NotExistPropertyInterfaceException::class,
        ];
    }

    /**
     * @param Condition[]|array $conditions
     * @dataProvider providerEncodeCollectionCondition
     * @see SerializerConditionsFieldInterface::encodeCollectionConditionToArray()
     * @see SerializerConditionsFieldInterface::encodeItemConditionToArray()
     */
    public function testEncodeCollectionConditionToArray(iterable $conditions, array $context = [], string $exceptedException = null): void
    {
        $serializerConditionsField = $this->makeSerializerConditionsFieldInterface(function (MockObject $mockObject): void {
            $mockObject->method('create')->with($this->equalTo(\stdClass::class))
                ->willReturn(new ResourceRuleEntity(\stdClass::class, [
                    'field1' => new PropertyRuleEntity('field1', StringConditionAttribute::class),
                    'field2' => new PropertyRuleEntity('field2', NumberConditionAttribute::class),
                ]));
        });

        if ($exceptedException) {
            $this->expectException($exceptedException);
        }

        $resultDecoded = $serializerConditionsField->encodeCollectionConditionToArray($conditions, $context);

        if (!$resultDecoded) {
            $this->assertEmpty($conditions);

            return;
        }

        $this->assertEquals($conditions, $serializerConditionsField->decodeToConditionCollection($resultDecoded));
    }

    public function providerEncodeCollectionCondition(): iterable
    {
        yield 'Empty' => [
            [],
            [],
            null,
        ];

        yield 'Conditions' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    0,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            0,
                            new StringConditionAttribute(\stdClass::class, 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ANY,
                            1,
                            null,
                            new CollectionCondition([
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    0,
                                    new StringConditionAttribute(\stdClass::class, 'field1', StringConditionAttribute::OPERATOR_EQ, 'EkoHolo')
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\stdClass::class, 'field2', NumberConditionAttribute::OPERATOR_EQ, 3.0)
                                ),
                                new Condition(
                                    Condition::TYPE_ALL,
                                    2,
                                    null,
                                    new CollectionCondition([
                                        new Condition(
                                            Condition::TYPE_ATTRIBUTE,
                                            0,
                                            new StringConditionAttribute(\stdClass::class, 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                                        ),
                                        new Condition(
                                            Condition::TYPE_ANY,
                                            1,
                                            null,
                                            new CollectionCondition([
                                                new Condition(
                                                    Condition::TYPE_ATTRIBUTE,
                                                    0,
                                                    new StringConditionAttribute(\stdClass::class, 'field1', StringConditionAttribute::OPERATOR_EQ, 'EkoHolo')
                                                ),
                                                new Condition(
                                                    Condition::TYPE_ATTRIBUTE,
                                                    1,
                                                    new NumberConditionAttribute(\stdClass::class, 'field2', NumberConditionAttribute::OPERATOR_EQ, 3.0)
                                                ),
                                            ]),
                                            Condition::IS_FALSE
                                        ),
                                    ]),
                                    Condition::IS_TRUE
                                ),
                            ]),
                            Condition::IS_FALSE
                        ),
                    ]),
                    Condition::IS_TRUE
                ),
            ]),
            [],
            null,
        ];

        yield 'Condition Exception' => [
            [
                '',
            ],
            [],
            \RuntimeException::class,
        ];
    }

    public function makeSerializerConditionsFieldInterface(callable $mockSet = null): SerializerConditionsFieldInterface
    {
        $ruleEntityResource = $this->createMock(RuleEntityResourceFactoryInterface::class);

        if ($mockSet) {
            $mockSet($ruleEntityResource);
        }

        return new SerializerConditionsField(
            self::COLLECTION_CONDITION,
            $ruleEntityResource
        );
    }
}
