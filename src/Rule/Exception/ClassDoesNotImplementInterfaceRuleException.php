<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Exception;

use Throwable;

class ClassDoesNotImplementInterfaceRuleException extends \RuntimeException implements RuleExceptionInterface
{
    public function __construct(string $classInit, string $interfaceName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Class `%s` does not implements interface `%s`', $classInit, $interfaceName), $code, $previous);
    }
}
