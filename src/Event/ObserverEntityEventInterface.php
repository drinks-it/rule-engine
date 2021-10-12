<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Event;

interface ObserverEntityEventInterface
{
    /**
     * @return iterable
     */
    public function getObservedEntities(): iterable;

    /**
     * @return string
     */
    public function getClassNameRuleEventInterface(): string;

    public function getContext(): array;
}
