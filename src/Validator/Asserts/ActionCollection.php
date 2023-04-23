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
class ActionCollection extends Constraint
{
    public string $unsupportedResourceMessage = 'rule_engine_resource_not_found';

    public string $unsupportedTypeProperty = 'rule_engine_type_property';

    public string $unsupportedForEvent = 'rule_engine_event_not_found';

    public string $unsupportedExecutedAction = 'rule_engine_action_unsupported';

    public function validatedBy(): string
    {
        return 'rule_engine.validator.action_collection';
    }
}
