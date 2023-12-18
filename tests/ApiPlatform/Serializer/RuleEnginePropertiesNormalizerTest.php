<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\ApiPlatform\Serializer;

use DrinksIt\RuleEngineBundle\ApiPlatform\Serializer\RuleEnginePropertiesNormalizer;
use DrinksIt\RuleEngineBundle\Event\Factory\RuleEventListenerFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\NumberActionType;
use DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;
use PHPUnit\Framework\TestCase;

class RuleEnginePropertiesNormalizerTest extends TestCase
{
    /**
     * @param $object
     * @param bool $decoratedSupported
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     *
     * @dataProvider providerDataNormalizer
     */
    public function testNormalize($object, bool $decoratedSupported): array
    {
        [$serializerConditions, $serializerActions, $ruleEventFactory] = $this->makeMocks();

        $ruleEngineNormalizer = new RuleEnginePropertiesNormalizer(
            $serializerConditions,
            $serializerActions,
            $ruleEventFactory
        );

        $this->assertIsBool($ruleEngineNormalizer->supportsNormalization($object));
        $this->assertFalse($ruleEngineNormalizer->supportsDenormalization([], \stdClass::class));

        if ($decoratedSupported) {
            $this->expectException(\RuntimeException::class);
        }

        $dataNormalize = $ruleEngineNormalizer->normalize($object);

        if ($object instanceof TriggerEventColumn) {
            $this->assertEquals($object->getEntityClassName(), $dataNormalize);
        } else {
            $this->assertIsArray($dataNormalize);
        }

        $this->assertEmpty($ruleEngineNormalizer->denormalize($dataNormalize, \stdClass::class));

        return [
            $dataNormalize,
            $object::class,
            $decoratedSupported,
        ];
    }

    /**
     * @dataProvider providerDataNormalizer
     * @param mixed $object
     */
    public function testNotNormalizeDecoded($object, bool $decoratedSupported): void
    {
        [$serializerConditions, $serializerActions, $ruleEventFactory] = $this->makeMocks();

        $ruleEngineNormalizer = new RuleEnginePropertiesNormalizer(
            $serializerConditions,
            $serializerActions,
            $ruleEventFactory
        );

        $this->assertEquals(!$decoratedSupported, $ruleEngineNormalizer->supportsNormalization($object));

        if (!$decoratedSupported) {
            if ($object instanceof TriggerEventColumn) {
                $this->assertEquals($object->getEntityClassName(), $ruleEngineNormalizer->normalize($object));
            } else {
                $this->assertIsArray($ruleEngineNormalizer->normalize($object));
            }

            return;
        }

        if ($decoratedSupported) {
            $this->expectException(\RuntimeException::class);
        }

        $this->assertEmpty($ruleEngineNormalizer->normalize($object));
    }

    /**
     * @dataProvider providerDataDenormalizer
     * @param mixed $dataNormalize
     * @param mixed $type
     * @param mixed $exceptedResult
     * @param mixed $decoratedSupported
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalize($dataNormalize, $type, $exceptedResult, $decoratedSupported): void
    {
        [$serializerConditions, $serializerActions, $ruleEventFactory] = $this->makeMocks();

        if ($type === TriggerEventColumn::class) {
            $ruleEventFactory->expects($this->once())->method('create')
                ->willReturn([
                    [
                        'resource' => null,
                    ],
                    [
                        'resource' => 'Test',
                    ],
                ]);
        }

        $ruleEngineNormalizer = new RuleEnginePropertiesNormalizer(
            $serializerConditions,
            $serializerActions,
            $ruleEventFactory
        );
        $this->assertEquals($decoratedSupported, $ruleEngineNormalizer->supportsDenormalization($dataNormalize, $type));

        if ($decoratedSupported) {
            $this->assertInstanceOf($exceptedResult, $ruleEngineNormalizer->denormalize($dataNormalize, $type));
        }

        if (!$exceptedResult) {
            $this->assertEmpty($ruleEngineNormalizer->denormalize($dataNormalize, $type));
        }
    }

    private function makeMocks(): array
    {
        $serializerConditions = $this->createMock(SerializerConditionsFieldInterface::class);
        $serializerConditions->method('encodeItemConditionToArray')
            ->with(
                $this->isInstanceOf(Condition::class),
                $this->isType('array')
            )->willReturn([]);

        $serializerConditions->method('encodeCollectionConditionToArray')
            ->with(
                $this->isInstanceOf(CollectionConditionInterface::class),
                $this->isType('array')
            )->willReturn([]);

        $serializerConditions->method('decodeToConditionCollection')->with(
            $this->equalTo(
                [
                    [
                        'condition_item' => 'test1',
                    ],
                    [
                        'condition_item' => 'test2',
                    ],
                ]
            ),
            $this->isType('array')
        )->willReturn(
            $this->createMock(CollectionConditionInterface::class)
        );

        $serializerActions = $this->createMock(SerializerActionsFieldInterface::class);

        $serializerActions->method('encodeActionToArray')
            ->with(
                $this->isInstanceOf(ActionInterface::class),
                $this->isType('array')
            )->willReturn([]);

        $serializerActions->method('encodeActionCollectionToArray')
            ->with(
                $this->isInstanceOf(CollectionActionsInterface::class),
                $this->isType('array')
            )->willReturn([]);

        $serializerActions->method('decodeToActionCollection')->with(
            $this->equalTo([
                [
                    'resource' => \stdClass::class,
                    'type' => 'any',
                    'field' => 'field_any',
                    'action' => 'hello world',
                ],
            ]),
            $this->isType('array')
        )->willReturn($this->createMock(CollectionActionsInterface::class));

        return  [
            $serializerConditions,
            $serializerActions,
            $this->createMock(RuleEventListenerFactoryInterface::class),
        ];
    }

    public function providerDataNormalizer(): iterable
    {
        yield 'Check False Support' => [
            new \stdClass(),
            true,
        ];

        yield 'Check Condition Item' => [
            new Condition(Condition::TYPE_ALL, 0),
            false,
        ];

        yield 'Check Condition Collection' => [
            new CollectionCondition(),
            false,
        ];

        yield 'Check Action Item' => [
            new NumberActionType('test', \stdClass::class),
            false,
        ];

        yield 'Check Action Collection' => [
            new CollectionActions(),
            false,
        ];

        yield 'Check Trigger Event Column' => [
            new TriggerEventColumn('Test'),
            false,
        ];
    }

    public function providerDataDenormalizer(): iterable
    {
        yield 'Check False Support' => [
            'test any content',
            \stdClass::class,
            null,
            false,
        ];

        yield 'Check Condition Collection' => [
            [
                [
                    'condition_item' => 'test1',
                ],
                [
                    'condition_item' => 'test2',
                ],
            ],
            Condition::class.'[]',
            CollectionConditionInterface::class,
            true,
        ];

        yield 'Check Action Collection' => [
            [
                [
                    'resource' => \stdClass::class,
                    'type' => 'any',
                    'field' => 'field_any',
                    'action' => 'hello world',
                ],
            ],
            ActionInterface::class.'[]',
            CollectionActionsInterface::class,
            true,
        ];

        yield 'Check Trigger Event Column' => [
            'Test',
            TriggerEventColumn::class,
            TriggerEventColumn::class,
            true,
        ];
    }
}
