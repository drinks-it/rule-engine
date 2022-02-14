<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule;

class TriggerEventColumn
{
    private ?string $entityClassName;

    public function __construct(string $entityClassName = null)
    {
        $this->entityClassName = $entityClassName;
    }

    public function getEntityClassName(): ?string
    {
        return $this->entityClassName;
    }

    public function __toString(): string
    {
        return $this->getEntityClassName() ?: '';
    }
}
