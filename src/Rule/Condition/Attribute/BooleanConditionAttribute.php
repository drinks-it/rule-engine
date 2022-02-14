<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\BooleanAttributeConditionTypeInterface;

class BooleanConditionAttribute extends AttributeCondition implements BooleanAttributeConditionTypeInterface
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'boolean';
    }

    public function match($value): bool
    {
        if ($this->getOperator() === BooleanAttributeConditionTypeInterface::OPERATOR_NEQ) {
            return $this->convertValue($value) !== $this->getValue();
        }

        return $this->convertValue($value) === $this->getValue();
    }

    public function getValue()
    {
        return $this->convertValue(parent::getValue());
    }

    protected function convertValue($value)
    {
        if (!$value) {
            return false;
        }

        if (!\is_bool($value) && !is_numeric($value)) {
            throw new TypeValueNotSupportedForConditionException('must be type of boolean, number, (empty)');
        }

        return (bool) $value;
    }
}
