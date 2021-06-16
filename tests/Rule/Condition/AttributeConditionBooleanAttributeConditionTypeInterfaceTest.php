<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Types\BooleanAttributeConditionTypeInterface;

class AttributeConditionBooleanAttributeConditionTypeInterfaceTest extends AttributeConditionTestCase
{
    public function testGetSupportOperators(): void
    {
        $reflectionInterface = new \ReflectionClass(BooleanAttributeConditionTypeInterface::class);
        $operatorsConstants = array_filter($reflectionInterface->getConstants(), fn ($name) => strpos($name, 'OPERATOR_') !== false, ARRAY_FILTER_USE_KEY);

        $this->assertNotEmpty($operatorsConstants);
        $this->assertNotEmpty($this->attributeCondition->getSupportOperators());

        $this->assertEquals($operatorsConstants, $this->attributeCondition->getSupportOperators());
    }

    public function makeAttributeConditionObject(): AttributeCondition
    {
        return new class(self::class, 'fieldNameTest') extends AttributeCondition implements BooleanAttributeConditionTypeInterface {
            public function getType(): string
            {
                return 'boolean';
            }
        };
    }
}
