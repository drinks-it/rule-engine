<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Mapping;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class RuleEntityProperty
{
    /**
     * @var string
     * @Required
     * @see AttributeConditionTypeInterface
     */
    public string $interfaceType;
}
