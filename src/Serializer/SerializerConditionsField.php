<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;

final class SerializerConditionsField implements SerializerConditionsFieldInterface
{
    private RuleEntityResourceFactoryInterface $ruleEngineFactory;

    private string $collectionClassName;

    public function __construct(
        string $collectionClassName,
        RuleEntityResourceFactoryInterface $ruleEngineFactory
    ) {
        $this->collectionClassName = $collectionClassName;
        $this->ruleEngineFactory = $ruleEngineFactory;
    }

    public function decodeToConditionCollection(array $conditions): CollectionConditionInterface
    {
        $collection = $this->makeNewCollection();

        if (!$conditions) {
            return $collection;
        }

        foreach (array_values($conditions) as $idx => $conditionItem) {
            $resourceClassName = $conditionItem['resource'];
            $ruleEngineResourceMetaData = $this->ruleEngineFactory->create($resourceClassName);

            if (!$ruleEngineResourceMetaData) {
                throw new \RuntimeException('Can not decode condition. Not found metadata rule engine for resource `'.$resourceClassName.'`');
            }
            $properties  = $ruleEngineResourceMetaData->getProperties();
            $condition   = new Condition($conditionItem['type'], $idx);

            if (Condition::TYPE_ATTRIBUTE !== $condition->getType()) {
                $condition->setSubConditions(
                    $this->decodeToConditionCollection($conditionItem['subConditions'])
                );
                $collection->add($condition);

                continue;
            }

            if (!\array_key_exists($conditionItem['field'], $properties)) {
                throw new \RuntimeException('Property `'.$conditionItem['field'].'` not found in rule engine metadata in class `'.$resourceClassName.'`');
            }

            $propertyMetadata = $properties[$conditionItem['field']];
            $conditionClassNameType = $propertyMetadata->getClassNameAttributeConditionType();
            $attributeCondition     = new $conditionClassNameType($resourceClassName, $propertyMetadata->getName());

            if (!$attributeCondition instanceof AttributeConditionTypeInterface) {
                throw new \RuntimeException('Class `'.$conditionClassNameType.'` must be implement `'.AttributeConditionTypeInterface::class.'`');
            }

            $attributeCondition->setOperator($conditionItem['operator']);
            $attributeCondition->setValue($conditionItem['value']);

            $condition->setAttributeCondition($attributeCondition);
            $collection->add($condition);
        }

        return $collection;
    }

    public function encodeToConditionToArray(CollectionConditionInterface $collectionCondition): array
    {
        if ($collectionCondition->isEmpty()) {
            return [];
        }
        $valuesReturn = [];

        foreach ($collectionCondition as $condition) {
            if (!$condition instanceof Condition) {
                throw new \RuntimeException(sprintf('Element in `%s` collection is not `%s` type', \get_class($collectionCondition), Condition::class));
            }

            if ($condition->getType() !== Condition::TYPE_ATTRIBUTE) {
                $valuesReturn[] = [
                    'type' => $condition->getType(),
                    'exceptedResult' => $condition->getResultBlock(),
                    'subConditions' => $this->encodeToConditionToArray($condition->getSubConditions()),
                ];

                continue;
            }

            $valuesReturn[] = [
                'type' => $condition->getType(),
                'resource' => $condition->getAttributeCondition()->getClassResource(),
                'field' => $condition->getAttributeCondition()->getFieldName(),
                'operator' => $condition->getAttributeCondition()->getOperator(),
                'value' => $condition->getAttributeCondition()->getValue(),
            ];
        }

        return $valuesReturn;
    }

    private function makeNewCollection(): CollectionConditionInterface
    {
        $collectionName = $this->collectionClassName;

        return new $collectionName();
    }
}
