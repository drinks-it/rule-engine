<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition\Exception;

use Throwable;

class TypeValueNotSupportedForConditionException extends \RuntimeException implements ConditionExceptionInterface
{
    public function __construct(string $typeSupported, $code = 0, Throwable $previous = null)
    {
        parent::__construct('The value supported only ' . $typeSupported, $code, $previous);
    }
}
