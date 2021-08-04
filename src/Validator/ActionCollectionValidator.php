<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator;

use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Validator\Asserts\ActionCollection as ConstraintActionCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\UnsupportedMetadataException;

class ActionCollectionValidator extends RuleConstraintValidator
{
    public const CODE = 'rule_engine.action';

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConstraintActionCollection) {
            throw new UnexpectedTypeException($constraint, ConstraintActionCollection::class);
        }

        if (!$value instanceof CollectionActionsInterface) {
            throw new UnexpectedValueException($value, CollectionActionsInterface::class);
        }

        if (!$this->context->getRoot() instanceof RuleEntityInterface) {
            throw new UnsupportedMetadataException('The entity `'.$this->context->getClassName().'` unsupported for Rule Engine Validation Asserts');
        }

        $eventColumn = $this->context->getRoot()->getTriggerEvent();

        foreach ($value as $action) {
            $this->checkAction($constraint, $action, $eventColumn);
        }
    }

    private function checkAction(ConstraintActionCollection $constraint, ActionInterface $action, TriggerEventColumn $eventColumn): void
    {
        $resourceRuleEntity = $this->getResourceRuleEntity($action->getResourceClass());

        if (!$resourceRuleEntity) {
            $this->context->buildViolation($constraint->unsupportedResourceMessage)->setCode(self::CODE)->addViolation();

            return;
        }

        if (!empty($resourceRuleEntity->getEvents())) {
            if (!\in_array($eventColumn->getEntityClassName(), $resourceRuleEntity->getEvents())) {
                $this->context->buildViolation($constraint->unsupportedForEvent)->setCode(self::CODE)->addViolation();
            }
        }

        $propertyRule       = $this->getPropertyFromResource($resourceRuleEntity, $action->getFieldName());

        if (!$propertyRule) {
            $this->context->buildViolation($constraint->unsupportedTypeProperty)->setCode(self::CODE)->addViolation();

            return;
        }

        if ($propertyRule->getClassNameActionFieldType() !== \get_class($action)) {
            $this->context->buildViolation($constraint->unsupportedTypeProperty)->setCode(self::CODE)->addViolation();
        }
    }
}
