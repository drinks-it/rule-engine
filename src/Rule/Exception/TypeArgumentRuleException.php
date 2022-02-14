<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Exception;

class TypeArgumentRuleException extends \RuntimeException implements RuleExceptionInterface
{
    public function __construct(string $currentType, string $className, string $methodName, string $needType)
    {
        parent::__construct(sprintf(
            '%s is not an accepted argument type for comparison method %s::%s(). Must by %s',
            $currentType,
            $className,
            $methodName,
            $needType
        ));
    }
}
