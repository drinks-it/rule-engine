<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\AttributeCondition;
use DrinksIt\RuleEngineBundle\Rule\Types\NumberAttributeConditionTypeInterface;

class NumberConditionAttribute extends AttributeCondition implements NumberAttributeConditionTypeInterface
{
    public function getValue()
    {
        if (!is_numeric($this->value)) {
            return 0;
        }

        return (float) $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'number';
    }
}
