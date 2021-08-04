<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator;

use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Validator\Asserts\ConditionCollection as ConstraintConditionCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\UnsupportedMetadataException;

class ConditionCollectionValidator extends RuleConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConstraintConditionCollection) {
            throw new UnexpectedTypeException($constraint, ConstraintConditionCollection::class);
        }

        if (!$value instanceof CollectionConditionInterface) {
            throw new UnexpectedValueException($value, CollectionActionsInterface::class);
        }

        if (!$this->context->getRoot() instanceof RuleEntityInterface) {
            throw new UnsupportedMetadataException('The entity `'.$this->context->getClassName().'` unsupported for Rule Engine Validation Asserts');
        }

        $eventColumn = $this->context->getRoot()->getTriggerEvent();

        foreach ($value as $idx => $condition) {
            $this->checkConditionRow($constraint, $condition, $eventColumn, $idx);
        }
    }

    public function checkConditionRow(ConstraintConditionCollection $constraint, Condition $condition, TriggerEventColumn $eventColumn, $idx): void
    {
        $idx = (string) $idx;

        if (Condition::TYPE_ATTRIBUTE !== $condition->getType()) {
            if ($condition->getSubConditions()->isEmpty()) {
                $this->context->buildViolation($constraint->messageSubConditionIsEmpty)
                    ->atPath($idx)
                    ->addViolation()
                ;

                return;
            }
            foreach ($condition->getSubConditions() as $subIdx => $subCondition) {
                $this->checkConditionRow($constraint, $subCondition, $eventColumn, $idx.'.'.$subIdx);
            }

            return;
        }
        $attributeCondition = $condition->getAttributeCondition();
        $resourceRuleEntity = $this->getResourceRuleEntity($attributeCondition->getClassResource());

        if (!$resourceRuleEntity) {
            $this->context->buildViolation($constraint->unsupportedResourceMessage)->atPath($idx)->addViolation();

            return;
        }

        if (!empty($resourceRuleEntity->getEvents())) {
            if (!\in_array($eventColumn->getEntityClassName(), $resourceRuleEntity->getEvents())) {
                $this->context->buildViolation($constraint->unsupportedForEvent)->atPath($idx)->addViolation();
            }
        }

        $propertyRule       = $this->getPropertyFromResource($resourceRuleEntity, $attributeCondition->getFieldName());

        if (!$propertyRule) {
            $this->context->buildViolation($constraint->unsupportedTypeProperty)->atPath($idx)->addViolation();

            return;
        }

        if ($propertyRule->getClassNameAttributeConditionType() !== \get_class($attributeCondition)) {
            $this->context->buildViolation($constraint->unsupportedTypeProperty)->atPath($idx)->addViolation();
        }

        try {
            $attributeCondition->getValue();
        } catch (TypeValueNotSupportedForConditionException | \TypeError $exception) {
            $this->context->buildViolation($constraint->unsupportedTypeProperty)->atPath($idx)->addViolation();
        }
    }
}
