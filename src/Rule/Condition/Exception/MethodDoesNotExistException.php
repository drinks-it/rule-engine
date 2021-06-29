<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Exception;

use Throwable;

class MethodDoesNotExistException extends \RuntimeException implements ConditionExceptionInterface
{
    public function __construct(string $className, string $methodName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Method `%s` in class `%s` does not exist', $className, $methodName), $code, $previous);
    }
}
