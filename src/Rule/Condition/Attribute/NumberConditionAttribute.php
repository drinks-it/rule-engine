<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\NumberAttributeConditionTypeInterface;

class NumberConditionAttribute extends AttributeCondition implements NumberAttributeConditionTypeInterface
{
    public function getValue()
    {
        return $this->convertValue(parent::getValue());
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'number';
    }

    public function match($value): bool
    {
        $value = $this->convertValue($value);

        switch ($this->getOperator()) {
            case NumberAttributeConditionTypeInterface::OPERATOR_EQ:  return $this->matchEq($value);
            case NumberAttributeConditionTypeInterface::OPERATOR_GT:  return $this->matchGt($value);
            case NumberAttributeConditionTypeInterface::OPERATOR_GTE: return $this->matchGte($value);
            case NumberAttributeConditionTypeInterface::OPERATOR_LT:  return $this->matchLt($value);
            case NumberAttributeConditionTypeInterface::OPERATOR_LTE: return $this->matchLte($value);
        }

        return false;
    }

    public function matchEq($value): bool
    {
        return $this->getValue() === $value;
    }

    public function matchGt($value): bool
    {
        return $this->getValue() > $value;
    }

    public function matchLt($value): bool
    {
        return $this->getValue() < $value;
    }

    public function matchGte($value): bool
    {
        return $this->matchGt($value) || $this->matchEq($value);
    }

    public function matchLte($value): bool
    {
        return $this->matchLt($value) || $this->matchEq($value);
    }

    protected function convertValue($value)
    {
        if (\is_object($value) || \is_array($value) || \is_bool($value)) {
            throw new TypeValueNotSupportedForConditionException($this->getType());
        }

        return (float) $value;
    }
}
