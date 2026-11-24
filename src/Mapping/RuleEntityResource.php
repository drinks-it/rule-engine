<?php

/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2026 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Mapping;

#[\Attribute(\Attribute::TARGET_CLASS)]
class RuleEntityResource
{
    /**
     * Class names events, if empty access to all events
     * @var array
     */
    public array $events = [];

    public function __construct(array $events = [])
    {
        $this->events = $events;
    }
}
