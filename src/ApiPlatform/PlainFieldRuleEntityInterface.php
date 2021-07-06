<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform;

use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;

interface PlainFieldRuleEntityInterface extends RuleEntityInterface
{
    public function getPlainConditions(): array;

    public function setPlainConditions(array $plainConditions): self;

    public function getPlainActions(): array;

    public function setPlainActions(array $plainActions): self;

    public function getPlainTriggerEvent(): ?string;

    public function setPlainTriggerEvent(string $triggerEvent): self;

    public function clearPlains(): void;
}
