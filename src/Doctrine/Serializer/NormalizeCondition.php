<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Serializer;

use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassDoesNotImplementInterfaceException;
use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassNotExistRuleEngineDoctrineException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\ConditionsInterface;

final class NormalizeCondition
{
    private array $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function normalizeCollection(?array $conditions = []): ConditionsInterface
    {
        $classCollection = $conditions ? $conditions['class_collection'] : $this->conditions['class_collection'];

        if (!class_exists($classCollection)) {
            throw new ClassNotExistRuleEngineDoctrineException($classCollection);
        }

        return new $classCollection(
            array_map(
                fn ($element) => $this->normalizeElement($element),
                $this->conditions['elements'] ?? []
            )
        );
    }

    public function normalizeElement(array $element): Condition
    {
        $attributeType = null;
        $subConditions = null;

        if ($element['attribute_condition']) {
            $attributeType = $this->normalizeAttributeType($element['attribute_condition']);
        }

        if ($element['sub_conditions']) {
            $subConditions = $this->normalizeCollection($element['sub_conditions']);
        }

        return new Condition($element['type'], $element['priority'], $attributeType, $subConditions, $element['result']);
    }

    public function normalizeAttributeType(array $attributeCondition): AttributeConditionTypeInterface
    {
        $classConditionAttributeType = $attributeCondition['class_condition'];

        if (!class_exists($classConditionAttributeType)) {
            throw new ClassNotExistRuleEngineDoctrineException($classConditionAttributeType);
        }

        $objectConditionAttribute = new $classConditionAttributeType(
            $attributeCondition['class_resource'],
            $attributeCondition['field_name']
        );

        if (!$objectConditionAttribute instanceof AttributeConditionTypeInterface) {
            throw new ClassDoesNotImplementInterfaceException($classConditionAttributeType, AttributeConditionTypeInterface::class);
        }

        $objectConditionAttribute->setOperator($attributeCondition['operator']);
        $objectConditionAttribute->setValue($attributeCondition['value']);

        return $objectConditionAttribute;
    }
}
