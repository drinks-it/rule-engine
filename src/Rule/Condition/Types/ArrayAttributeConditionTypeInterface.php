<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Types;

interface ArrayAttributeConditionTypeInterface extends AttributeConditionTypeInterface
{
    public const OPERATOR_EQ = 'EQ';

    public const OPERATOR_CONTAINS = 'CONTAINS';

    public const OPERATOR_KEY_EXISTS = 'KEY_EXISTS';
}
