<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\NumberConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Types\AttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Types\NumberAttributeConditionTypeInterface;

class AttributeConditionNumberAttributeConditionTypeInterfaceTest extends AttributeConditionTestCase
{
    public function testGetSupportOperators(): void
    {
        $reflectionInterface = new \ReflectionClass(NumberAttributeConditionTypeInterface::class);
        $operatorsConstants = array_filter($reflectionInterface->getConstants(), fn ($name) => strpos($name, 'OPERATOR_') !== false, ARRAY_FILTER_USE_KEY);

        $this->assertNotEmpty($operatorsConstants);
        $this->assertNotEmpty($this->attributeCondition->getSupportOperators());

        $this->assertEquals($operatorsConstants, $this->attributeCondition->getSupportOperators());
    }

    public function testBaseObject(): void
    {
        $numberConditionAttribute = new NumberConditionAttribute(self::class, 'fieldBaseTest');

        $this->assertInstanceOf(NumberAttributeConditionTypeInterface::class, $numberConditionAttribute);
        $this->assertInstanceOf(AttributeCondition::class, $numberConditionAttribute);
        $this->assertInstanceOf(AttributeConditionTypeInterface::class, $numberConditionAttribute);

        $numberConditionAttribute->setValue('54654.345');
        $this->assertEquals(54654.345, $numberConditionAttribute->getValue());

        $numberConditionAttribute->setValue('sdf');
        $this->assertEquals(0, $numberConditionAttribute->getValue());

        $numberConditionAttribute->setValue(new \stdClass());
        $this->assertEquals(0, $numberConditionAttribute->getValue());

        $this->assertIsString($numberConditionAttribute->getType());
        $this->assertEquals('number', $numberConditionAttribute->getType());
    }

    public function makeAttributeConditionObject(): AttributeCondition
    {
        return new class(self::class, 'fieldNameTest') extends AttributeCondition implements NumberAttributeConditionTypeInterface {
            public function getType(): string
            {
                return 'number';
            }

            public function match($value): bool
            {
                return $value === $this->getValue();
            }
        };
    }
}
