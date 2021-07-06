<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\ApiPlatform\Serializer;

use DrinksIt\RuleEngineBundle\ApiPlatform\Serializer\RuleEnginePropertiesNormalizer;
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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RuleEnginePropertiesNormalizerTest extends TestCase
{
    /**
     * @param $object
     * @param bool $decoratedSupported
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     *
     * @dataProvider providerDataNormalizer
     */
    public function testNormalize($object, bool $decoratedSupported): void
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
            $serializerActions
        );

        $this->assertIsBool($ruleEngineNormalizer->supportsNormalization($object));

        if ($object instanceof TriggerEventColumn) {
            $this->assertEquals($object->getEntityClassName(), $ruleEngineNormalizer->normalize($object));
        } else {
            $this->assertIsArray($ruleEngineNormalizer->normalize($object));
        }
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
}
