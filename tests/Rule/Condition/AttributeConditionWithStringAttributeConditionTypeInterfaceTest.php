<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\StringAttributeConditionTypeInterface;

class AttributeConditionWithStringAttributeConditionTypeInterfaceTest extends AttributeConditionTestCase
{
    public function testGetSupportOperators(): void
    {
        $reflectionInterface = new \ReflectionClass(StringAttributeConditionTypeInterface::class);
        $operatorsConstants = array_filter($reflectionInterface->getConstants(), fn ($name) => str_contains($name, 'OPERATOR_'), ARRAY_FILTER_USE_KEY);

        $this->assertNotEmpty($operatorsConstants);
        $this->assertNotEmpty($this->attributeCondition->getSupportOperators());

        $this->assertEquals($operatorsConstants, $this->attributeCondition->getSupportOperators());
    }

    public function testBaseObject(): void
    {
        $stringCondition = new StringConditionAttribute(self::class, 'fieldBaseTest');
        $this->assertEquals('string', $stringCondition->getType());

        $stringCondition->setValue(new class () {
            public function __toString(): string
            {
                return 'HelloString';
            }
        });

        $this->assertEquals('HelloString', $stringCondition->getValue());

        $stringCondition->setValue(new class () {});
        $this->assertIsString($stringCondition->getValue());

        $stringCondition->setValue([]);
        $this->assertEquals('[array]', $stringCondition->getValue());

        $stringCondition->setValue('4');
        $this->assertEquals('4', $stringCondition->getValue());
    }

    public function makeAttributeConditionObject(): AttributeCondition
    {
        return new class (self::class, 'fieldNameTest') extends AttributeCondition implements StringAttributeConditionTypeInterface {
            public static function getType(): string
            {
                return 'string';
            }

            public function match($value): bool
            {
                return $this->getValue() === $value;
            }
        };
    }
}
