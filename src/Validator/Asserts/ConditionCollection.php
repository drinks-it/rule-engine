<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator\Asserts;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConditionCollection extends Constraint
{
    public string $messageSubConditionIsEmpty = 'rule_engine_condition_empty_subs';

    public string $unsupportedResourceMessage = 'rule_engine_resource_not_found';

    public string $unsupportedTypeProperty = 'rule_engine_type_property';

    public string $unsupportedForEvent = 'rule_engine_event_not_found';

    public function validatedBy()
    {
        return 'rule_engine.validator.condition_collection';
    }
}
