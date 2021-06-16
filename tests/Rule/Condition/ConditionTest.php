<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    public function testInit(): void
    {
        $mockAttribute = $this->createMock(AttributeConditionTypeInterface::class);
        $mockAttribute->expects($this->once())->method('toArray')->willReturn([]);

        $collectionMock = $this->createMock(CollectionConditionInterface::class);
        $collectionMock->expects($this->once())->method('toArray')->willReturn([]);

        $condition = new Condition(
            Condition::TYPE_ATTRIBUTE,
            0,
            $mockAttribute,
            $collectionMock,
            Condition::IS_TRUE
        );

        $this->assertInstanceOf(Condition::class, $condition);

        $this->assertIsBool($condition->getResultBlock());
        $this->assertIsString($condition->getType());

        $this->assertIsInt($condition->getPriority());
        $this->assertEquals(0, $condition->getPriority());

        $this->assertEquals(Condition::TYPE_ATTRIBUTE, $condition->getType());
        $this->assertIsBool($condition->isNotDefaultResult());

        $this->assertInstanceOf(AttributeConditionTypeInterface::class, $condition->getAttributeCondition());
        $this->assertInstanceOf(CollectionConditionInterface::class, $condition->getSubConditions());

        $arrayData = $condition->toArray();
        $this->assertIsArray($arrayData);

        $this->assertArrayHasKey('priority', $arrayData);
        $this->assertEquals(0, $arrayData['priority']);

        $this->assertArrayHasKey('type', $arrayData);
        $this->assertEquals(Condition::TYPE_ATTRIBUTE, $arrayData['type']);

        $this->assertArrayHasKey('result', $arrayData);
        $this->assertNull($arrayData['result']);

        $this->assertArrayHasKey('attribute_condition', $arrayData);
        $this->assertIsArray($arrayData['attribute_condition']);

        $this->assertArrayHasKey('sub_conditions', $arrayData);
        $this->assertIsArray($arrayData['sub_conditions']);
    }
}
