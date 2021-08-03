<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator\Asserts;

use DrinksIt\RuleEngineBundle\Validator\TriggerEventValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TriggerEvent extends Constraint
{
    public string $message = 'validation.message.rule_engine.event_not_found';

    public function validatedBy()
    {
        return TriggerEventValidator::class;
    }
}
