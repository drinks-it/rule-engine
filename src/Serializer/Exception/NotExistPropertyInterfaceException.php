<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer\Exception;

use Throwable;

class NotExistPropertyInterfaceException extends \RuntimeException implements RuleEngineSerializerExceptionInterface
{
    public function __construct(string $resource, ?string $property, string $exceptedInterface = null, bool $empty = false, $code = 0, Throwable $previous = null)
    {
        if ($empty) {
            parent::__construct("The property `".$property."` in resource `".$resource."` must be object with implement interface `".$exceptedInterface."`", $code, $previous);
        }
        parent::__construct("The property `".$property."` in resource `".$resource."` does not implement interface `".$exceptedInterface."`", $code, $previous);
    }
}
