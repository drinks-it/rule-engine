<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Property\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityExtractorInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;

final class RuleEntityPropertyFactory implements RuleEntityPropertyFactoryInterface
{
    private RuleEntityExtractorInterface $entityExtractor;

    public function __construct(
        RuleEntityExtractorInterface $entityExtractor
    ) {
        $this->entityExtractor = $entityExtractor;
    }

    public function create(string $entityClass): array
    {
        $rulePropertiesReturn = [];
        foreach ($this->entityExtractor->getRuleEntityPropertiesNames($entityClass) as $propertyName) {
            $annotationProperty = $this->entityExtractor->getRuleEntityPropertyAnnotation($entityClass, $propertyName);

            if (!$annotationProperty) {
                continue;
            }

            $rulePropertiesReturn[$propertyName] = new PropertyRuleEntity($propertyName, $annotationProperty->condition, $annotationProperty->action);
        }

        return $rulePropertiesReturn;
    }
}
