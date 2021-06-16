<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Extractor;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;

interface RuleEntityExtractorInterface
{
    public function getRuleEntityResourceAnnotation(string $class): ?RuleEntityResource;

    public function getRuleEntityPropertiesNames(string $classResource): array;

    public function getRuleEntityPropertyAnnotation(string $classResource, string $propertyName): ?RuleEntityProperty;
}
