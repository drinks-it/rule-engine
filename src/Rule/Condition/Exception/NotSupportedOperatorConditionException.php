<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Exception;

use Throwable;

class NotSupportedOperatorConditionException extends \RuntimeException implements ConditionExceptionInterface
{
    public function __construct(string $valueType, string $operator, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf("The %s is not supported with operator %s", $valueType, $operator), $code, $previous);
    }
}
