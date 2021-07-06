<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer\Exception;

use Throwable;

class PropertyNotFoundException extends \RuntimeException implements RuleEngineSerializerExceptionInterface
{
    public function __construct(string $resource, string $property, $code = 0, Throwable $previous = null)
    {
        parent::__construct("The property `" . $property . "` not in class `" . $resource . "`", $code, $previous);
    }
}
