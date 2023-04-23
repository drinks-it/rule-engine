<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition;

use Doctrine\Common\Collections\ArrayCollection;
use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\KeyNotFoundInArrayConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\MethodDoesNotExistException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\ConditionPropertyNormalizerInterface;
use DrinksIt\RuleEngineBundle\Serializer\NormalizerPropertyInterface;

class CollectionCondition extends ArrayCollection implements CollectionConditionInterface, ConditionPropertyNormalizerInterface
{
    private ?NormalizerPropertyInterface $normalizerProperty = null;

    public function isMatched($objectEntity, array $context = []): bool
    {
        $contextType = $context['type'] ?? null;
        $exceptedResult = $context['result'] ?? null;

        $attributesResults = [];
        /** @var Condition $condition */
        foreach ($this->getIterator() as $condition) {
            if ($condition->getType() === Condition::TYPE_ATTRIBUTE) {
                $resourceClass = $condition->getAttributeCondition()->getClassResource();

                if (
                    (\is_object($objectEntity) && $objectEntity::class === $resourceClass)
                    || \is_array($objectEntity)
                ) {
                    $attributesResults[] = $this->checkAttributesConditions(
                        $objectEntity,
                        $condition->getAttributeCondition()
                    );

                    continue;
                }

                $getShortNameResource = mb_substr($resourceClass, mb_strrpos($resourceClass, '\\') + 1);
                $methodName           = StrEntity::getGetterNameMethod($getShortNameResource);

                if (method_exists($objectEntity, $methodName)) {
                    $attributesResults[] = $this->checkAttributesConditions(
                        $objectEntity->{$methodName}(),
                        $condition->getAttributeCondition()
                    );

                    continue;
                }
            }

            $subConditions = $condition->getSubConditions();

            if ($subConditions instanceof ConditionPropertyNormalizerInterface) {
                $subConditions->setNormalizer($this->normalizerProperty);
            }

            $attributesResults[] = $subConditions->isMatched($objectEntity, [
                'type' => $condition->getType(),
                'result' => $condition->getResultBlock(),
            ]);
        }

        if (!\is_bool($exceptedResult)) {
            return \count(array_filter($attributesResults)) === $this->count();
        }

        if ($contextType === Condition::TYPE_ALL) {
            return (\count(array_filter($attributesResults)) === $this->count()) === $exceptedResult;
        }
        // Type Any Check
        foreach ($attributesResults as $result) {
            if ($exceptedResult !== $result) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @param Condition $attributeCondition
     * @param mixed $objectEntity
     * @return bool
     */
    private function checkAttributesConditions($objectEntity, AttributeConditionTypeInterface $attributeCondition): bool
    {
        $value = null;
        $fieldName = $attributeCondition->getFieldName();

        if (\is_object($objectEntity)) {
            $methodName = StrEntity::getGetterNameMethod($fieldName);

            if (!method_exists($objectEntity, $methodName)) {
                throw new MethodDoesNotExistException($objectEntity::class, $methodName);
            }
            $value = $objectEntity->{$methodName}();
        }

        if (\is_array($objectEntity)) {
            if (!\array_key_exists($fieldName, $objectEntity)) {
                throw new KeyNotFoundInArrayConditionException($fieldName);
            }

            $value = $objectEntity[$fieldName];
        }

        if ($this->normalizerProperty) {
            $value = $this->normalizerProperty->normalize($value, $attributeCondition->getClassResource(), $fieldName);
        }

        return $attributeCondition->match($value);
    }

    public function setNormalizer(NormalizerPropertyInterface $normalizerProperty): self
    {
        $this->normalizerProperty = $normalizerProperty;

        return $this;
    }
}
