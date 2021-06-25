<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule;

final class ActionColumn
{
    private array $actions;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function toArray(): array
    {
        return $this->actions;
    }

    public function run(iterable $data): void
    {
    }
}
