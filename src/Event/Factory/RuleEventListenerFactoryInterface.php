<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Event\Factory;

use DrinksIt\RuleEngineBundle\Event\RuleEventInterface;

interface RuleEventListenerFactoryInterface
{
    /**
     * @return array{event: RuleEventInterface, name: string, resource: string}[]
     */
    public function create(): iterable;
}
