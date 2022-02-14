<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer\Exception;

use Throwable;

class ResourceIsNotIdentifyException extends \RuntimeException implements RuleEngineSerializerExceptionInterface
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Resource is not identify', $code, $previous);
    }
}
