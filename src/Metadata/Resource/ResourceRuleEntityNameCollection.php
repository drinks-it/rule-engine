<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource;

/**
 * Class ResourceRuleEntityNameCollection
 * @package DrinksIt\RuleEngineBundle\Metadata\Resource
 */
final class ResourceRuleEntityNameCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array<class-string>
     */
    private array $classes;

    /**
     * @param string[] $classes
     */
    public function __construct(array $classes = [])
    {
        $this->classes = $classes;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->classes);
    }

    public function count(): int
    {
        return \count($this->classes);
    }
}
