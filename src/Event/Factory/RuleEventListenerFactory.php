<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Event\Factory;

use DrinksIt\RuleEngineBundle\Event\RuleEventInterface;

class RuleEventListenerFactory implements RuleEventListenerFactoryInterface
{
    private iterable $handlerTagRuleEvents;

    public function __construct(iterable $handlerTagRuleEvents)
    {
        $this->handlerTagRuleEvents = $handlerTagRuleEvents;
    }

    public function create(): iterable
    {
        $events = [];
        foreach ($this->handlerTagRuleEvents as $ruleEvent) {
            if (!$ruleEvent instanceof RuleEventInterface) {
                continue;
            }
            $events[] = [
                'event' => $ruleEvent,
                'name' => $ruleEvent->getName(),
                'resource' => \get_class($ruleEvent),
            ];
        }

        return $events;
    }
}
