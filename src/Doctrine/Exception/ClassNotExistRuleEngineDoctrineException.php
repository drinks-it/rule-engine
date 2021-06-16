<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Exception;

use Throwable;

class ClassNotExistRuleEngineDoctrineException extends \RuntimeException implements RuleEngineDoctrineExceptionInterface
{
    public function __construct(string $className, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Class not exist `".$className. "`", $code, $previous);
    }
}
