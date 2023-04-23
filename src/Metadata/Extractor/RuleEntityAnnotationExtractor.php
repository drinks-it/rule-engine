<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Extractor;

use Doctrine\Common\Annotations\Reader;
use DrinksIt\RuleEngineBundle\Helper\ClassHelper;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;

final class RuleEntityAnnotationExtractor implements RuleEntityExtractorInterface
{
    private ?Reader $annotationReader;

    public function __construct(Reader $annotationReader = null)
    {
        $this->annotationReader = $annotationReader;
    }

    public function getRuleEntityResourceAnnotation(string $class): ?RuleEntityResource
    {
        if (!$this->annotationReader) {
            return null;
        }

        if (!ClassHelper::exist($class)) {
            return null;
        }

        $classResource = new \ReflectionClass($class);

        $ruleEntity = null;

        if (\PHP_VERSION_ID >= 80000) {
            $ruleEntity = $classResource->getAttributes(RuleEntityResource::class);
        }

        if (!$ruleEntity) {
            $ruleEntity = $this->annotationReader->getClassAnnotation($classResource, RuleEntityResource::class);
        }

        if ($ruleEntity instanceof RuleEntityResource) {
            return $ruleEntity;
        }

        return null;
    }

    public function getRuleEntityPropertyAnnotation(string $classResource, string $propertyName): ?RuleEntityProperty
    {
        if (!$this->annotationReader) {
            return null;
        }

        if (!ClassHelper::exist($classResource)) {
            return null;
        }

        $class = new \ReflectionClass($classResource);
        $property = $class->getProperty($propertyName);

        $ruleProperty = $this->annotationReader->getPropertyAnnotation($property, RuleEntityProperty::class);

        if ($ruleProperty instanceof RuleEntityProperty) {
            return $ruleProperty;
        }

        return null;
    }

    public function getRuleEntityPropertiesNames(string $classResource): array
    {
        if (!ClassHelper::exist($classResource)) {
            return [];
        }

        $class = new \ReflectionClass($classResource);
        $properties = array_filter(
            $class->getProperties(),
            fn (\ReflectionProperty $property) => $property->getDocComment() !== false
        );

        if (!$properties) {
            return [];
        }

        return array_map(fn (\ReflectionProperty $property) => $property->getName(), $properties);
    }

    public function getRuleEntityClassNameFromRelationField(string $classResource, string $propertyRelationName): ?string
    {
        if (!ClassHelper::exist($classResource)) {
            return null;
        }

        $class = new \ReflectionClass($classResource);
        $properties = array_filter(
            $class->getProperties(),
            fn (\ReflectionProperty $reflectionProperty) => $reflectionProperty->getName() === $propertyRelationName
        );

        if (!$properties) {
            return null;
        }

        $propertyRelationReflection = array_pop($properties);

        if (!$propertyRelationReflection instanceof \ReflectionProperty) {
            return null;
        }

        $mappingRelation = null;
        foreach (RuleEntityExtractorInterface::RELATION_CLASS_NAMES as $mappingRelationClassName) {
            $mappingRelation = $this->annotationReader->getPropertyAnnotation($propertyRelationReflection, $mappingRelationClassName);

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
