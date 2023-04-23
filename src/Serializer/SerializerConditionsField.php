<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Serializer\Exception\NotExistPropertyInterfaceException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\PropertyNotFoundException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceIsNotIdentifyException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceNotFoundException;

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

    public function decodeToConditionCollection(array $conditions, array $context = []): CollectionConditionInterface
    {
        $collection = $this->makeNewCollection();

        if (!$conditions) {
            return $collection;
        }

        foreach (array_values($conditions) as $idx => $conditionItem) {
            $condition   = new Condition($conditionItem['type'], $idx);

            if (Condition::TYPE_ATTRIBUTE !== $condition->getType()) {
                $condition->setSubConditions(
                    $this->decodeToConditionCollection($conditionItem['subConditions'], $context)
                );
                $condition->setResult((bool) $conditionItem['exceptedResult']);
                $collection->add($condition);

                continue;
            }

            $resourceClassName = $conditionItem['resource'] ?? null;

            if (!$resourceClassName && isset($context['rule_default_resource'])) {
                $resourceClassName = $context['rule_default_resource'];
            }

            if (!$resourceClassName) {
                throw new ResourceIsNotIdentifyException();
            }

            $ruleEngineResourceMetaData = $this->ruleEngineFactory->create($resourceClassName);

            if (!$ruleEngineResourceMetaData) {
                throw new ResourceNotFoundException($resourceClassName);
            }
            $properties  = $ruleEngineResourceMetaData->getProperties();

            if (!\array_key_exists($conditionItem['field'], $properties)) {
                throw new PropertyNotFoundException($resourceClassName, $conditionItem['field']);
            }

            $propertyMetadata = $properties[$conditionItem['field']];
            $conditionClassNameType = $propertyMetadata->getClassNameAttributeConditionType();

            if (!$conditionClassNameType) {
                throw new NotExistPropertyInterfaceException($resourceClassName, $propertyMetadata->getName(), AttributeConditionTypeInterface::class, true);
            }

            $attributeCondition     = new $conditionClassNameType($resourceClassName, $propertyMetadata->getName());

            if (!$attributeCondition instanceof AttributeConditionTypeInterface) {
                throw new NotExistPropertyInterfaceException($resourceClassName, $propertyMetadata->getName(), AttributeConditionTypeInterface::class);
            }

            $attributeCondition->setOperator($conditionItem['operator']);
            $attributeCondition->setValue($conditionItem['value']);

            if (isset($conditionItem['resourceShortName'])) {
                $attributeCondition->setResourceShortName($conditionItem['resourceShortName']);
            }

            $condition->setAttributeCondition($attributeCondition);
            $collection->add($condition);
        }

        return $collection;
    }

    public function encodeCollectionConditionToArray(iterable $collectionCondition, array $context = []): array
    {
        if (!$collectionCondition) {
            return [];
        }
        $valuesReturn = [];

        foreach ($collectionCondition as $condition) {
            if (!$condition instanceof Condition) {
                throw new \RuntimeException(sprintf('Element in `%s` collection is not `%s` type', \is_object($collectionCondition) ? $collectionCondition::class : \gettype($collectionCondition), Condition::class));
            }

            $valuesReturn[] = $this->encodeItemConditionToArray($condition, $context);
        }

        return $valuesReturn;
    }

    public function encodeItemConditionToArray(Condition $condition, array $context = []): array
    {
        if ($condition->getType() !== Condition::TYPE_ATTRIBUTE) {
            return [
                'type' => $condition->getType(),
                'exceptedResult' => $condition->getResultBlock(),
                'subConditions' => $this->encodeCollectionConditionToArray($condition->getSubConditions(), $context),
            ];
        }

        return [
            'type' => $condition->getType(),
            'resource' => $condition->getAttributeCondition()->getClassResource(),
            'field' => $condition->getAttributeCondition()->getFieldName(),
            'operator' => $condition->getAttributeCondition()->getOperator(),
            'value' => $condition->getAttributeCondition()->getValue(),
            'resourceShortName' => $condition->getAttributeCondition()->getResourceShortName(),
        ];
    }

    private function makeNewCollection(): CollectionConditionInterface
    {
        $collectionName = $this->collectionClassName;

        return new $collectionName();
    }
}
