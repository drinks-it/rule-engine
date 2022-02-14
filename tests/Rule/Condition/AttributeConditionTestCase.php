<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use PHPUnit\Framework\TestCase;

abstract class AttributeConditionTestCase extends TestCase
{
    protected AttributeCondition $attributeCondition;

    abstract public function makeAttributeConditionObject(): AttributeCondition;

    protected function setUp(): void
    {
        $this->attributeCondition = $this->makeAttributeConditionObject();
    }

    public function testDetails(): void
    {
        $this->assertInstanceOf(AttributeCondition::class, $this->attributeCondition->setOperator('EQ'));
        $this->assertEquals('EQ', $this->attributeCondition->getOperator());

        $this->assertInstanceOf(AttributeCondition::class, $this->attributeCondition->setValue('any'));

        $this->assertIsString($this->attributeCondition->getClassResource());

        $this->assertIsString($this->attributeCondition->getFieldName());

        $arrayResult = $this->attributeCondition->toArray();
        $this->assertIsArray($arrayResult);

        $this->assertArrayHasKey('classResource', $arrayResult);
        $this->assertArrayHasKey('fieldName', $arrayResult);
        $this->assertArrayHasKey('operator', $arrayResult);
        $this->assertArrayHasKey('type', $arrayResult);
        $this->assertArrayHasKey('value', $arrayResult);
    }
}
