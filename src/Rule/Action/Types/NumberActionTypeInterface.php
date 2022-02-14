<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action\Types;

use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;

interface NumberActionTypeInterface extends ActionInterface
{
    public const OPERATION_PLUS = '+';

    public const OPERATION_MINUS = '-';

    public const OPERATION_OF = '*';

    public const OPERATION_OF_X = 'x';

    public const OPERATION_DIVIDE = '/';

    public const SPECIAL_SYMBOL_BRACKET_LEFT = '(';

    public const SPECIAL_SYMBOL_BRACKET_RIGHT = ')';
}
