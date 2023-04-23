<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

interface NormalizerPropertyInterface
{
    /**
     * @param $value
     * @param string $resourceClass
     * @param string $propertyName
     * @param array{eventResourceClass: string} $context
     * @return mixed
     */
    public function normalize($value, string $resourceClass, string $propertyName, array $context = []);
}
