<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\NotSupportedOperatorConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\ArrayAttributeConditionTypeInterface;

class ArrayConditionAttribute extends AttributeCondition implements ArrayAttributeConditionTypeInterface
{
    public function getValue()
    {
        return $this->convertValue(parent::getValue());
    }

    public static function getType(): string
    {
        return 'array';
    }

    public function match($value): bool
    {
        switch ($this->getOperator()) {
            case ArrayAttributeConditionTypeInterface::OPERATOR_EQ:
                return $this->matchIterableTypeOperatorEq($value);
            case ArrayAttributeConditionTypeInterface::OPERATOR_CONTAINS:
                return $this->matchIterableTypeOperatorContains($value);
            case ArrayAttributeConditionTypeInterface::OPERATOR_KEY_EXISTS:
                if (is_iterable($value)) {
                    throw new NotSupportedOperatorConditionException('iterable', ArrayAttributeConditionTypeInterface::OPERATOR_KEY_EXISTS);
                }

                return \array_key_exists($value, $this->getValue());
        }

        return false;
    }

    protected function matchIterableTypeOperatorEq($value): bool
    {
        if (!is_iterable($value)) {
            return false;
        }

        return $this->getValue() === $value;
    }

    protected function matchIterableTypeOperatorContains($value): bool
    {
        if (\is_null($value) || \is_object($value)) {
            $value = [$value];
        }

        if (is_scalar($value)) {
            return \in_array($value, $this->getValue());
        }

        $isContains = false;
        foreach ($this->getValue() as $valueFromCondition) {
            foreach ($value as $val) {
                if ($valueFromCondition === $val) {
                    $isContains = true;

                    break;
                }
            }

            if ($isContains) {
                break;
            }
        }

        return $isContains;
    }

    protected function convertValue($value): array
    {
        if (!\is_array($value)) {
            throw new TypeValueNotSupportedForConditionException($this->getType());
        }

        return $value;
    }
}
