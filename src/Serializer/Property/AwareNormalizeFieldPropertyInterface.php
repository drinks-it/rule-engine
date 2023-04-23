<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer\Property;

interface AwareNormalizeFieldPropertyInterface extends NormalizeFieldPropertyInterface
{
    public function normalize($value, string $resourceClass, string $propertyName, array $context = []);

    public function support(string $resourceClass, string $propertyName, array $context = []);
}
