<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Exception;

use Throwable;

class KeyNotFoundInArrayConditionException extends \RuntimeException implements ConditionExceptionInterface
{
    public function __construct($keySearch, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf("The key `%s` doesn't exist in array", $keySearch), $code, $previous);
    }
}
