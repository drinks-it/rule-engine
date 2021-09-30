<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer\Property;

interface NormalizeFieldPropertyInterface
{
    public function normalize($value, string $resourceClass, string $propertyName);

    public function support(string $resourceClass, string $propertyName);
}
