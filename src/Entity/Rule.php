<?php

namespace DrinksIt\RuleEngineBundle\Entity;

class Rule
{
    private int $id;
    private string $name;
    private string $description;
    private bool $active;
    private array $scope;
    private int $priority;
    private $conditions;
    private $action;
    private $triggerEvent;
    private bool $stopRuleProcessing;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
}
