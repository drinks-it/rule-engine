<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Serializer;

use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\ConditionsInterface;

final class DenormalizeCondition
{
    private ConditionsInterface $conditions;

    public function __construct(ConditionsInterface $conditions)
    {
        $this->conditions = $conditions;
    }

    public function denormalizeCollection(?ConditionsInterface $conditions = null): array
    {
        $conditions = $conditions ?: $this->conditions;

        return [
            'class_collection' => \get_class($conditions),
            'elements' => array_map(fn (Condition $condition) => $condition, $conditions->getValues()),
        ];
    }

    public function denormalizeElement(Condition $condition): array
    {
        $attributeCondition = null;

        if ($attributeConditionObject = $condition->getAttributeCondition()) {
            $attributeCondition = $this->denormalizeAttributeType($attributeConditionObject);
        }

        return [
            'priority' => $condition->getPriority(),
            'type' => $condition->getType(),
            'result' => $condition->getResultBlock(),
            'attribute_condition' => $attributeCondition,
            'sub_conditions' => $this->denormalizeCollection($condition->getSubConditions()),
        ];
    }

    public function denormalizeAttributeType(AttributeConditionTypeInterface $attributeConditionType): array
    {
        return [
            'class_condition' => \get_class($attributeConditionType),
            'properties' => [
                'class_resource' => $attributeConditionType->getClassResource(),
                'field_name' => $attributeConditionType->getFieldName(),
                'operator' => $attributeConditionType->getOperator(),
                'type' => $attributeConditionType->getType(),
                'value' => $attributeConditionType->getValue(),
            ],
        ];
    }
}
