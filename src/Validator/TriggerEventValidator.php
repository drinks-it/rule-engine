<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator;

use DrinksIt\RuleEngineBundle\Event\Factory\RuleEventListenerFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;
use DrinksIt\RuleEngineBundle\Validator\Asserts\TriggerEvent as ConstraintTriggerEvent;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Exception\UnsupportedMetadataException;

class TriggerEventValidator extends ConstraintValidator
{
    private RuleEventListenerFactoryInterface $eventListenerFactory;

    public function __construct(
        RuleEventListenerFactoryInterface $eventListenerFactory
    ) {
        $this->eventListenerFactory = $eventListenerFactory;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConstraintTriggerEvent) {
            throw new UnexpectedTypeException($constraint, ConstraintTriggerEvent::class);
        }

        if (!$value instanceof TriggerEventColumn) {
            throw new UnexpectedValueException($value, TriggerEventColumn::class);
        }

        if (!$this->context->getRoot() instanceof RuleEntityInterface) {
            throw new UnsupportedMetadataException('The entity `'.$this->context->getClassName().'` unsupported for Rule Engine Validation Asserts');
        }

        foreach ($this->eventListenerFactory->create() as $eventData) {
            if ($eventData['resource'] === $value->getEntityClassName()) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
