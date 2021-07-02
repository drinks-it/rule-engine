<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource;

final class ResourceRuleEntityCollection implements \IteratorAggregate, \Countable
{
    private array $resources = [];

    public function push(ResourceRuleEntity $ruleEntity): self
    {
        $this->resources[] = $ruleEntity;

        return $this;
    }

    /**
     * @return iterable|ResourceRuleEntity[]
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->resources);
    }

    public function count(): int
    {
        return \count($this->resources);
    }
}
