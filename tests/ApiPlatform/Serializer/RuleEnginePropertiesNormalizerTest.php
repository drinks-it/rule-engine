<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

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

        $decoratedNormalizer = $this->createMock(NormalizerInterface::class);

        $decoratedNormalizer->method('supportsNormalization')->willReturn($decoratedSupported);

        if ($decoratedSupported) {
            $decoratedNormalizer->expects($this->once())->method('normalize')
                ->with(
                    $this->equalTo($object),
                    $this->equalTo(null),
                    $this->isType('array')
                )->willReturn([
                    1,
                ]);
        }

        $ruleEngineNormalizer = new RuleEnginePropertiesNormalizer(
            $decoratedNormalizer,
            $serializerConditions,
            $serializerActions,
            $ruleEventFactory
        );

        $this->assertIsBool($ruleEngineNormalizer->supportsNormalization($object));
        $this->assertFalse($ruleEngineNormalizer->supportsDenormalization([], \stdClass::class));

        $dataNormalize = $ruleEngineNormalizer->normalize($object);

        if ($object instanceof TriggerEventColumn) {
            $this->assertEquals($object->getEntityClassName(), $dataNormalize);
        } else {
            $this->assertIsArray($dataNormalize);
        }

        $this->assertEmpty($ruleEngineNormalizer->denormalize($dataNormalize, \stdClass::class));

        return [
            $dataNormalize,
            \get_class($object),
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
            $this->createMock(DenormalizerInterface::class),
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

        $this->assertEmpty($ruleEngineNormalizer->normalize($object));
    }

    public function testSetSerializer(): void
    {
        [$serializerConditions, $serializerActions, $ruleEventFactory] = $this->makeMocks();

        $serializerChecked = $this->createMock(SerializerInterface::class);

        $decorated = $this->createMock(SerializerAwareInterface::class);
        $decorated->expects($this->once())->method('setSerializer')->with(
            $this->equalTo($serializerChecked)
        );
        $ruleEngineNormalizer = new RuleEnginePropertiesNormalizer(
            $decorated,
            $serializerConditions,
            $serializerActions,
            $ruleEventFactory
        );

        $ruleEngineNormalizer->setSerializer($serializerChecked);
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

        $decorated = $this->createMock(DenormalizerInterface::class);

        $decorated
            ->expects($decoratedSupported ? $this->never() : $this->once())
            ->method('supportsDenormalization')
            ->willReturn($decoratedSupported);

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
            $decorated,
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
