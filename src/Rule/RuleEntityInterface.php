<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule;

use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;

interface RuleEntityInterface
{
    public function getName(): ?string;

    public function setName(string $name): self;

    public function getDescription(): ?string;

    public function setDescription(?string $description): self;

    public function setActive(bool $active): self;

    public function getActive(): ?bool;

    public function getConditions(): ConditionsInterface;

    public function setConditions(ConditionsInterface $conditions): self;

    public function addCondition(Condition $condition): self;

    public function removedCondition(Condition $condition): self;

    public function getAction(): ActionColumn;

    public function setAction(ActionColumn $action): self;

    public function getTriggerEvent(): TriggerEventColumn;

    public function setTriggerEvent(TriggerEventColumn $triggerEvent): self;

    public function getStopRuleProcessing(): ?bool;

    public function setStopRuleProcessing(bool $stopRuleProcessing): self;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function getPriority(): ?int;

    public function setPriority(int $priority): self;
}
