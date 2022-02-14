<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Types;

interface StringAttributeConditionTypeInterface extends AttributeConditionTypeInterface
{
    public const OPERATOR_EQ = 'EQ';

    public const OPERATOR_NOT_EQ = 'NEQ';

    public const OPERATOR_CONTAINS = 'CONTAINS';

    public const OPERATOR_NOT_CONTAINS = 'NOT_CONTAINS';
}
