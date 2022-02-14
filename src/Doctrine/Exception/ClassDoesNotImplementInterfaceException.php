<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Exception;

use Throwable;

class ClassDoesNotImplementInterfaceException extends \RuntimeException implements RuleEngineDoctrineExceptionInterface
{
    public function __construct(string $classInit, string $interfaceName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Class `%s` does not implements interface `%s`', $classInit, $interfaceName), $code, $previous);
    }
}
