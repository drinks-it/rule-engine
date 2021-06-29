<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\Types\StringAttributeConditionTypeInterface;

class StringConditionAttribute extends AttributeCondition implements StringAttributeConditionTypeInterface
{
    public function getValue()
    {
        return $this->convertToString($this->value);
    }

    public function getType(): string
    {
        return 'string';
    }

    public function match($value): bool
    {
        $value = $this->convertToString($value);

        switch ($this->getOperator()) {
            case StringAttributeConditionTypeInterface::OPERATOR_EQ:
                return $this->getValue() === $value;
            case StringAttributeConditionTypeInterface::OPERATOR_CONTAINS:
                return strpos($this->getValue(), $value) !== false;
        }

        return false;
    }

    private function convertToString($value): string
    {
        if (\is_object($value)) {
            if (!method_exists($value, '__toString')) {
                throw new TypeValueNotSupportedForConditionException('string or class has method `__toString`');
            }

            return $value->__toString();
        }

        if (\is_array($value) || \is_bool($value)) {
            throw new TypeValueNotSupportedForConditionException('string or class has method `__toString`');
        }

        return (string) $value;
    }
}
