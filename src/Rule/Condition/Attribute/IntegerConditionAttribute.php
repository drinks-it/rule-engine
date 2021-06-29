<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Types\IntegerAttributeConditionTypeInterface;

class IntegerConditionAttribute extends NumberConditionAttribute implements IntegerAttributeConditionTypeInterface
{
    public function convertValue($value): float
    {
        return (int) parent::convertValue($value);
    }
}
