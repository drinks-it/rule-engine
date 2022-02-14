<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Serializer;

use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;

final class DenormalizeCondition
{
    private CollectionConditionInterface $conditions;

    public function __construct(CollectionConditionInterface $conditions)
    {
        $this->conditions = $conditions;
    }

    public function denormalizeCollection(?CollectionConditionInterface $conditions = null, bool $internal = false): ?array
    {
        if ($internal) {
            if (!$conditions) {
                return null;
            }
        } else {
            $conditions = $this->conditions;
        }

        return [
            'class_collection' => \get_class($conditions),
            'elements' => array_map(fn (Condition $condition) => $this->denormalizeElement($condition), $conditions->getValues() ?? []),
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
            'sub_conditions' => $this->denormalizeCollection($condition->getSubConditions(), true),
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
