<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Types\StringAttributeConditionTypeInterface;

class StringConditionAttribute extends AttributeCondition implements StringAttributeConditionTypeInterface
{
    public function getValue()
    {
        if (\is_object($this->value)) {
            if (!method_exists($this->value, '__toString')) {
                return \get_class($this->value);
            }

            return $this->value->__toString();
        }

        if (\is_array($this->value)) {
            return '[array]';
        }

        return (string) $this->value;
    }

    public function getType(): string
    {
        return 'string';
    }
}
