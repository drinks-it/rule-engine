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
            $decoded = $this->decodeAnnotationProperty($entityClass, $propertyName);

            if ($decoded  instanceof PropertyRuleEntity) {
                $rulePropertiesReturn[$propertyName] = $decoded;

                continue;
            }

            if (!$decoded) {
                continue;
            }

            foreach ($this->entityExtractor->getRuleEntityPropertiesNames($decoded) as $relationProperty) {
                $decodedRelation = $this->decodeAnnotationProperty(
                    $decoded,
                    $relationProperty,
                    $propertyName.'.'.$relationProperty
                );

                if ($decodedRelation instanceof PropertyRuleEntity) {
                    $rulePropertiesReturn[$propertyName.'.'.$relationProperty] = $decodedRelation;
                }
            }
        }

        return $rulePropertiesReturn;
    }

    private function decodeAnnotationProperty(string $className, string $propertyName, string $setPropertyName = null)
    {
        $annotationProperty = $this->entityExtractor->getRuleEntityPropertyAnnotation($className, $propertyName);

        if (!$annotationProperty) {
            return [];
        }

        if ($annotationProperty->relationObject) {
            return $this->entityExtractor->getRuleEntityClassNameFromRelationField($className, $propertyName);
        }

        return new PropertyRuleEntity($setPropertyName ?: $propertyName, $annotationProperty->condition, $annotationProperty->action);
    }
}
