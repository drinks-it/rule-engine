<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\BooleanAttributeConditionTypeInterface;

class AttributeConditionBooleanAttributeConditionTypeInterfaceTest extends AttributeConditionTestCase
{
    public function testGetSupportOperators(): void
    {
        $reflectionInterface = new \ReflectionClass(BooleanAttributeConditionTypeInterface::class);
        $operatorsConstants = array_filter($reflectionInterface->getConstants(), fn ($name) => str_contains($name, 'OPERATOR_'), ARRAY_FILTER_USE_KEY);

        $this->assertNotEmpty($operatorsConstants);
        $this->assertNotEmpty($this->attributeCondition->getSupportOperators());

        $this->assertEquals($operatorsConstants, $this->attributeCondition->getSupportOperators());

        $this->attributeCondition->setValue(false);

        $this->assertTrue($this->attributeCondition->match(false));
        $this->assertFalse($this->attributeCondition->match(true));
    }

    public function makeAttributeConditionObject(): AttributeCondition
    {
        return new class (self::class, 'fieldNameTest') extends AttributeCondition implements BooleanAttributeConditionTypeInterface {
            public static function getType(): string
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
