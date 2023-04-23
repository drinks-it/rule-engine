<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator\Asserts;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class TriggerEvent extends Constraint
{
    public string $message = 'rule_engine_event_not_found';

    public function validatedBy(): string
    {
        return 'rule_engine.validator.event';
    }
}
