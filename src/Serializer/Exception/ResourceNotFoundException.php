<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer\Exception;

use Throwable;

class ResourceNotFoundException extends \RuntimeException implements RuleEngineSerializerExceptionInterface
{
    public function __construct(?string $resource, $code = 0, Throwable $previous = null)
    {
        parent::__construct("The resource `".$resource."` not found", $code, $previous);
    }
}
