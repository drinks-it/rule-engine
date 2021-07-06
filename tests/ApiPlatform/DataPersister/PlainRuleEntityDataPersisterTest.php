<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\ApiPlatform\DataPersister;

use DrinksIt\RuleEngineBundle\ApiPlatform\DataPersister\PlainRuleEntityDataPersister;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\PlainFieldRuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Serializer\SerializerActionsFieldInterface;
use DrinksIt\RuleEngineBundle\Serializer\SerializerConditionsFieldInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PlainRuleEntityDataPersisterTest extends TestCase
{
    /**
     *
     * @dataProvider providerPersists
     * @param mixed|PlainFieldRuleEntityInterface|MockObject $dataEntity
     */
    public function testDataPersister($dataEntity): void
    {
        $context = [
            'rule_entity_class_name' => \get_class($dataEntity),
            'rule_default_resource' => null,
        ];

        if ($defaultClass = $dataEntity->getDefaultResourceClassName()) {
            $context['rule_default_resource'] = $defaultClass;
        }
        $serializerAction = $this->createMock(SerializerActionsFieldInterface::class);
        $serializerAction->method('decodeToActionCollection')
            ->with(
                $this->isType('array'),
                $this->equalTo($context)
            )
            ->willReturn($this->createMock(CollectionActionsInterface::class));

        $serializerConditions = $this->createMock(SerializerConditionsFieldInterface::class);
        $serializerConditions->method('decodeToConditionCollection')->with(
            $this->isType('array'),
            $this->equalTo($context)
        )->willReturn($this->createMock(CollectionConditionInterface::class));

        $plainRuleEntityDataPersister = new PlainRuleEntityDataPersister(
            $serializerAction,
            $this->createMock(SerializerConditionsFieldInterface::class)
        );

        $this->assertIsBool($plainRuleEntityDataPersister->supports($dataEntity));

        if (!$plainRuleEntityDataPersister->supports($dataEntity)) {
            return;
        }

        $dataEntity->expects($this->once())->method('clearPlains');
        $this->assertEquals($dataEntity, $plainRuleEntityDataPersister->persist($dataEntity));
        $this->assertEquals($dataEntity, $plainRuleEntityDataPersister->remove($dataEntity));
        $this->assertTrue($plainRuleEntityDataPersister->resumable());
    }

    public function providerPersists(): iterable
    {
        $inputConditions = $this->createMock(PlainFieldRuleEntityInterface::class);
        $inputConditions->method('getPlainConditions')->willReturn([]);
        $inputConditions->method('getPlainActions')->willReturn([]);
        $inputConditions->method('getPlainTriggerEvent')->willReturn('StingContent');

        $inputConditions->method('setConditions')
            ->with($this->isInstanceOf(CollectionConditionInterface::class))
            ->willReturnSelf();

        $inputConditions->method('setActions')->willReturnSelf()
            ->with($this->isInstanceOf(CollectionActionsInterface::class))
            ->willReturnSelf();

        $inputConditions->method('setTriggerEvent')
            ->with($this->isInstanceOf(TriggerEventColumn::class))
            ->willReturnSelf();

        yield 'Empty entity with plain' => [
            $inputConditions,
        ];

        $inputConditions = $this->createMock(PlainFieldRuleEntityInterface::class);
        $inputConditions->method('getPlainConditions')->willReturn([
            'test data',
        ]);
        $inputConditions->method('getPlainActions')->willReturn([
            'test data',
        ]);
        $inputConditions->method('getPlainTriggerEvent')->willReturn('StingContent');

        $inputConditions->method('setConditions')
            ->with($this->isInstanceOf(CollectionConditionInterface::class))
            ->willReturnSelf();

        $inputConditions->method('setActions')->willReturnSelf()
            ->with($this->isInstanceOf(CollectionActionsInterface::class))
            ->willReturnSelf();

        $inputConditions->method('setTriggerEvent')
            ->with($this->isInstanceOf(TriggerEventColumn::class))
            ->willReturnSelf();

        $inputConditions->method('getDefaultResourceClassName')
            ->willReturn('ClassResource');

        yield 'Input data from request' => [
            $inputConditions,
        ];
    }
}
