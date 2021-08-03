<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator\Asserts;

use DrinksIt\RuleEngineBundle\Validator\ActionCollectionValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ActionCollection extends Constraint
{
    public string $unsupportedResourceMessage = 'validation.message.rule_engine.resource';

    public string $unsupportedTypeProperty = 'validation.message.rule_engine.type_property';

    public string $unsupportedForEvent = 'validation.message.rule_engine.event';

    public function validatedBy()
    {
        return ActionCollectionValidator::class;
    }
}
