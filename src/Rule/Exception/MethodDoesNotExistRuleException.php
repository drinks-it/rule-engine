<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Exception;

use Throwable;

class MethodDoesNotExistRuleException extends \RuntimeException implements RuleExceptionInterface
{
    public function __construct(string $className, string $methodName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Method `%s` does not exist in class `%s`', $methodName, $className), $code, $previous);
    }
}
