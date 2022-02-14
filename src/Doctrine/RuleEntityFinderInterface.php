<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine;

use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;

interface RuleEntityFinderInterface
{
    /**
     * @param string $eventClassName
     * @param array $context
     * @return iterable|RuleEntityInterface[]
     */
    public function getRulesByEventName(string $eventClassName, array $context = []): iterable;
}
