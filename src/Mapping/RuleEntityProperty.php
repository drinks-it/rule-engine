<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Mapping;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class RuleEntityProperty
{
    public ?string $action = null;
    public ?string $condition = null;
    public array $onlyEvents = [];
    public bool $relationObject = false;

    /**
     * @param string|null $action ActionInterface
     * @param string|null $condition AttributeConditionTypeInterface
     */
    public function __construct(
        ?string $condition = null,
        ?string $action = null,
        array $onlyEvents = [],
        bool $relationObject = false
    ) {
        $this->relationObject = $relationObject;
        $this->onlyEvents = $onlyEvents;
        $this->condition = $condition;
        $this->action = $action;
    }
}
