<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
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

    private const RELATION_CLASS_NAMES = [
        'Doctrine\ORM\Mapping\ManyToOne',
        'Doctrine\ORM\Mapping\OneToOne',
        'Doctrine\ORM\Mapping\OneToMany',
        'Doctrine\ORM\Mapping\ManyToMany',
    ];

    public function __construct(Reader $annotationReader = null)
    {
        $this->annotationReader = $annotationReader;
    }

    public function getRuleEntityResourceAnnotation(string $classResource): ?RuleEntityResource
    {
        if (!$this->annotationReader) {
            return null;
        }

        if (!ClassHelper::exist($classResource)) {
            return null;
        }

        $class = new \ReflectionClass($classResource);

        $ruleEntity = null;

        if (\PHP_VERSION_ID >= 80000) {
            $ruleEntity = $class->getAttributes(RuleEntityResource::class);
        }

        if (!$ruleEntity) {
            $ruleEntity = $this->annotationReader->getClassAnnotation($class, RuleEntityResource::class);
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
            fn (\ReflectionProperty $property) => $property->getDocComment() === false
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
        $properties = $class->getProperties(
            fn (\ReflectionProperty $reflectionProperty) => $reflectionProperty->getName() === $propertyRelationName
        );

        if (!$properties) {
            return null;
        }

        $propertyRelationReflection = $properties[0];

        if (!$propertyRelationReflection instanceof \ReflectionProperty) {
            return null;
        }

        $mappingRelation = null;
        foreach (self::RELATION_CLASS_NAMES as $mappingRelationClassName) {
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
            return null;
        }

        return $mappingRelation->targetEntity;
    }
}
