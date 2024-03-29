<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Types;

interface NumberAttributeConditionTypeInterface extends AttributeConditionTypeInterface
{
    /**
     * Equal.
     */
    public const OPERATOR_EQ = 'EQ';
    /**
     * Not Equal
     */
    public const OPERATOR_NOT_EQ = 'NEQ';
    /**
     * Great then.
     */
    public const OPERATOR_GT = 'GT';

    /**
     * Less then.
     */
    public const OPERATOR_LT = 'LT';

    /**
     * Great than or equal.
     */
    public const OPERATOR_GTE = 'GTE';

    /**
     * Less then or equal.
     */
    public const OPERATOR_LTE = 'LTE';
}
