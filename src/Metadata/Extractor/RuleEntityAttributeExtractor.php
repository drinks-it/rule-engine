<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Extractor;

use DrinksIt\RuleEngineBundle\Helper\ClassHelper;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;

final class RuleEntityAttributeExtractor implements RuleEntityExtractorInterface
{
    private RuleEntityExtractorInterface $decorated;
    /**
     * @inheritDoc
     */
    public function __construct(RuleEntityExtractorInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getRuleEntityResourceAnnotation(string $class): ?RuleEntityResource
    {
        if (!ClassHelper::exist($class)) {
            return null;
        }

        if (\PHP_VERSION_ID < 80000) {
            return $this->decorated->getRuleEntityResourceAnnotation($class);
        }

        $classResource = new \ReflectionClass($class);

        $ruleEntityResource = $classResource->getAttributes(RuleEntityResource::class);

        if (!$ruleEntityResource) {
            return $this->decorated->getRuleEntityResourceAnnotation($class);
        }

        $ruleEntityResource = $ruleEntityResource[0]->newInstance();

        if ($ruleEntityResource instanceof RuleEntityResource) {
            return $ruleEntityResource;
        }

        return $this->decorated->getRuleEntityResourceAnnotation($class);
    }

    public function getRuleEntityPropertiesNames(string $classResource): array
    {
        if (\PHP_VERSION_ID < 80000) {
            return $this->decorated->getRuleEntityPropertiesNames($classResource);
        }

        if (!ClassHelper::exist($classResource)) {
            return [];
        }

        $class = new \ReflectionClass($classResource);
        $properties = array_filter(
            $class->getProperties(),
            fn (\ReflectionProperty $property) => !!$property->getAttributes(RuleEntityProperty::class)
        );

        if (!$properties) {
            return $this->decorated->getRuleEntityPropertiesNames($classResource);
        }

        return array_map(fn (\ReflectionProperty $property) => $property->getName(), $properties);
    }

    public function getRuleEntityPropertyAnnotation(string $classResource, string $propertyName): ?RuleEntityProperty
    {
        if (\PHP_VERSION_ID < 80000) {
            return $this->decorated->getRuleEntityPropertyAnnotation($classResource, $propertyName);
        }

        if (!ClassHelper::exist($classResource)) {
            return null;
        }

        $class = new \ReflectionClass($classResource);
        $property = $class->getProperty($propertyName);

        $ruleProperty = $property->getAttributes(RuleEntityProperty::class);

        if (!$ruleProperty) {
            return $this->decorated->getRuleEntityPropertyAnnotation($classResource, $propertyName);
        }

        $ruleProperty = $ruleProperty[0]->newInstance();

        if ($ruleProperty instanceof RuleEntityProperty) {
            return $ruleProperty;
        }

        return null;
    }

    public function getRuleEntityClassNameFromRelationField(string $classResource, string $propertyRelationName): ?string
    {
        if (\PHP_VERSION_ID < 80000) {
            return $this->decorated->getRuleEntityClassNameFromRelationField($classResource, $propertyRelationName);
        }

        $class = new \ReflectionClass($classResource);
        $properties = array_filter(
            $class->getProperties(),
            fn (\ReflectionProperty $reflectionProperty) => $reflectionProperty->getName() === $propertyRelationName
        );

        if (!$properties) {
            return $this->decorated->getRuleEntityClassNameFromRelationField($classResource, $propertyRelationName);
        }

        $propertyRelationReflection = array_pop($properties);

        if (!$propertyRelationReflection instanceof \ReflectionProperty) {
            return $this->decorated->getRuleEntityClassNameFromRelationField($classResource, $propertyRelationName);
        }

        $mappingRelation = null;
        foreach (RuleEntityExtractorInterface::RELATION_CLASS_NAMES as $mappingRelationClassName) {
            $mappingRelation = $propertyRelationReflection->getAttributes($mappingRelationClassName);

            if (!$mappingRelation) {
                continue;
            }
            $mappingRelation = $mappingRelation[0]->newInstance();

            if ($mappingRelation instanceof $mappingRelationClassName) {
                break;
            }
        }

        if (!$mappingRelation) {
            return null;
        }

        if (!property_exists($mappingRelation, 'targetEntity')) {
            return null;
        }

        if (!class_exists($mappingRelation->targetEntity)) {
            $targetEntityClassName = $class->getNamespaceName() . '\\' . $mappingRelation->targetEntity;
        } else {
            $targetEntityClassName = $mappingRelation->targetEntity;
        }

        if (!class_exists($targetEntityClassName)) {
            return null;
        }

        return $targetEntityClassName;
    }
}
