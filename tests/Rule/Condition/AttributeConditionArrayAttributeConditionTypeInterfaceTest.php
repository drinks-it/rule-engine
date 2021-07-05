<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\ArrayAttributeConditionTypeInterface;

class AttributeConditionArrayAttributeConditionTypeInterfaceTest extends AttributeConditionTestCase
{
    public function testGetSupportOperators(): void
    {
        $reflectionInterface = new \ReflectionClass(ArrayAttributeConditionTypeInterface::class);
        $operatorsConstants = array_filter($reflectionInterface->getConstants(), fn ($name) => strpos($name, 'OPERATOR_') !== false, ARRAY_FILTER_USE_KEY);

        $this->assertNotEmpty($operatorsConstants);
        $this->assertGreaterThan(0, $this->attributeCondition->getSupportOperators());

        $this->assertEquals($operatorsConstants, $this->attributeCondition->getSupportOperators());

        $this->attributeCondition->setValue(['1', '2', '3']);

        $this->assertTrue($this->attributeCondition->match(['1', '2', '3']));
        $this->assertFalse($this->attributeCondition->match([]));
    }

    public function makeAttributeConditionObject(): AttributeCondition
    {
        return new class(self::class, 'fieldNameTest') extends AttributeCondition implements ArrayAttributeConditionTypeInterface {
            public function setValue($value): AttributeCondition
            {
                if (!\is_array($value)) {
                    $value = [$value];
                }
                $this->value = $value;

                return $this;
            }

            public function getType(): string
            {
                return 'boolean';
            }

            public function match($value): bool
            {
                return $this->getValue() === $value;
            }
        };
    }
}
