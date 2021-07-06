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
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\NumberActionType;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\StringActionType;
use DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;
use DrinksIt\RuleEngineBundle\Serializer\Exception\NotExistPropertyInterfaceException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\PropertyNotFoundException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceIsNotIdentifyException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceNotFoundException;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsField;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SerializerActionsFieldTest extends TestCase
{
    public const COLLECTION_ACTIONS_CLASS_NAME = CollectionActions::class;

    public function testInit(): SerializerActionsFieldInterface
    {
        $serializer = $this->makeSerializerActionsFieldInterface();

        $this->assertInstanceOf(SerializerActionsFieldInterface::class, $serializer);

        return $serializer;
    }

    /**
     * @param SerializerActionsFieldInterface $serializerActionsField
     * @param array $actions
     * @param array $context
     * @param null|mixed $exceptedException
     *
     * @dataProvider providerActionsDecodeToCollection
     * @dataProvider providerActionsDecodeToCollectionExceptions
     */
    public function testDecodeToActionCollection(SerializerActionsFieldInterface $serializerActionsField, array $actions, array $context, $exceptedException = null): void
    {
        if ($exceptedException) {
            $this->expectException($exceptedException);
        }
        $result = $serializerActionsField->decodeToActionCollection($actions, $context);
        $this->assertInstanceOf(CollectionActionsInterface::class, $result);
        $this->assertEquals(\count($actions), $result->count());
    }

    public function providerActionsDecodeToCollection(): iterable
    {
        yield 'Empty case' => [
            $this->makeSerializerActionsFieldInterface(),
            [],
            [],
            null,
        ];

        yield 'Resource from context' => [
            $this->makeSerializerActionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')->with($this->equalTo('DefaultResourceName'))
                    ->willReturn(new ResourceRuleEntity('DefaultResourceName', [
                        'dataField' => new PropertyRuleEntity(
                            'dataField',
                            StringConditionAttribute::class,
                            StringActionType::class
                        ),
                    ]));
            }),
            [
                [
                    'field' => 'dataField',
                    'action' => '%dataField% check another',
                ],
            ],
            [
                'rule_default_resource' => 'DefaultResourceName',
            ],
        ];
    }

    public function providerActionsDecodeToCollectionExceptions(): iterable
    {
        yield 'Empty context. Exception Resource Not Identify' => [
            $this->makeSerializerActionsFieldInterface(),
            [
                [
                    'field' => 'dataField',
                    'action' => '%dataField% + executeAction',
                ],
            ],
            [],
            ResourceIsNotIdentifyException::class,
        ];

        yield 'Empty context. Exception Resource not found' => [
            $this->makeSerializerActionsFieldInterface(),
            [
                [
                    'resource' => 'ClassResourceName',
                    'field' => 'dataField',
                    'action' => '%dataField% + executeAction',
                ],
            ],
            [],
            ResourceNotFoundException::class,
        ];

        yield 'Empty context. Exception Property not found' => [
            $this->makeSerializerActionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')->with($this->equalTo('ClassResourceName'))
                    ->willReturn(new ResourceRuleEntity('ClassResourceName', []));
            }),
            [
                [
                    'resource' => 'ClassResourceName',
                    'field' => 'dataField',
                    'action' => '%dataField% + executeAction',
                ],
            ],
            [],
            PropertyNotFoundException::class,
        ];

        yield 'Empty context. Exception property action is null' => [
            $this->makeSerializerActionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')->with($this->equalTo('ClassResourceName'))
                    ->willReturn(new ResourceRuleEntity(
                        'ClassResourceName',
                        [
                            'dataField' => new PropertyRuleEntity('dataField', null, null),
                        ],
                    ));
            }),
            [
                [
                    'resource' => 'ClassResourceName',
                    'field' => 'dataField',
                    'action' => '%dataField% + executeAction',
                ],
            ],
            [],
            NotExistPropertyInterfaceException::class,
        ];

        yield 'Empty context. Exception property action is not ActionInterface' => [
            $this->makeSerializerActionsFieldInterface(function (MockObject $mockObject): void {
                $mockObject->expects($this->once())->method('create')->with($this->equalTo('ClassResourceName'))
                    ->willReturn(new ResourceRuleEntity(
                        'ClassResourceName',
                        [
                            'dataField' => new PropertyRuleEntity('dataField', null, \stdClass::class),
                        ],
                    ));
            }),
            [
                [
                    'resource' => 'ClassResourceName',
                    'field' => 'dataField',
                    'action' => '%dataField% + executeAction',
                ],
            ],
            [],
            NotExistPropertyInterfaceException::class,
        ];

        yield 'Resource from context. Exception' => [
            $this->makeSerializerActionsFieldInterface(),
            [
                [
                    'field' => 'dataField',
                    'action' => '%dataField% check another',
                ],
            ],
            [
                'rule_default_resource' => 'DefaultResourceName',
            ],
            ResourceNotFoundException::class,
        ];
    }

    /**
     * @see SerializerActionsFieldInterface::encodeActionCollectionToArray()
     * @see SerializerActionsFieldInterface::encodeActionToArray()
     * @param ActionInterface[] $actions
     * @param null|mixed $executedException
     *
     * @dataProvider providerEncodeActionCollections
     */
    public function testEncodeActionCollectionToArray(array $actions, array $context, $executedException = null): void
    {
        if ($executedException) {
            $this->expectException($executedException);
        }
        $serializerActionsField = $this->makeSerializerActionsFieldInterface();

        $resultDecoded = $serializerActionsField->encodeActionCollectionToArray($actions, $context);

        if (!$resultDecoded) {
            $this->assertEmpty($actions);

            return;
        }
        $this->assertEquals(\count($actions), \count($resultDecoded));
        foreach ($actions as $idx => $action) {
            $this->assertArrayHasKey($idx, $resultDecoded);
            $this->assertEquals(
                [
                    'resource' => $action->getResourceClass(),
                    'type' => $action->getType(),
                    'field' => $action->getFieldName(),
                    'action' => $action->getPatternExecute(),
                ],
                $resultDecoded[$idx]
            );
        }
    }

    public function providerEncodeActionCollections(): iterable
    {
        yield 'Empty Collections' => [
            [],
            [],
            null,
        ];

        yield 'Check encoder' => [
            [
                new NumberActionType('test', 'ResourceClass', '1 + 1'),
                new NumberActionType('test', 'ResourceClass', '1 + 2'),
                new NumberActionType('test', 'ResourceClass', '%test% + 2'),
            ],
            [],
            null,
        ];

        yield 'Case with Type Error Exception' => [
            [
                new StringConditionAttribute('ResourceClass', 'test', '=', '4'),
            ],
            [],
            \TypeError::class,
        ];
    }

    private function makeSerializerActionsFieldInterface(callable $mockRuleEntityResourceFactoryInterface = null)
    {
        $ruleEngineFactory = $this->createMock(RuleEntityResourceFactoryInterface::class);

        if ($mockRuleEntityResourceFactoryInterface) {
            $mockRuleEntityResourceFactoryInterface($ruleEngineFactory);
        }

        return new SerializerActionsField(self::COLLECTION_ACTIONS_CLASS_NAME, $ruleEngineFactory);
    }
}
