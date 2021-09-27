<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Property;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;

final class PropertyRuleEntity
{
    private string $name;

    private ?string $classNameAttributeConditionType;

    private ?string $classNameActionFieldType;

    private array $onlyEvents = [];

    private ?RuleEntityResource $ruleEntityResource;

    public function __construct(
        string $name,
        string $classNameAttributeConditionType = null,
        string $classNameActionFieldType = null,
        array  $onlyEvents = [],
        RuleEntityResource $entityResource = null
    ) {
        $this->name = $name;
        $this->classNameAttributeConditionType = $classNameAttributeConditionType;
        $this->classNameActionFieldType = $classNameActionFieldType;
        $this->ruleEntityResource = $entityResource;
        $this->onlyEvents = $onlyEvents;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getClassNameAttributeConditionType(): ?string
    {
        return $this->classNameAttributeConditionType;
    }

    public function getConditionType(): ?string
    {
        if (!$this->classNameAttributeConditionType) {
            return null;
        }

        if (!method_exists($this->classNameAttributeConditionType, 'getType')) {
            return null;
        }

        return $this->classNameAttributeConditionType::getType();
    }

    /**
     * @return string|null
     */
    public function getClassNameActionFieldType(): ?string
    {
        return $this->classNameActionFieldType;
    }

    public function getActionType(): ?string
    {
        if (!$this->classNameActionFieldType) {
            return null;
        }

        if (!method_exists($this->classNameActionFieldType, 'getType')) {
            return null;
        }

        return $this->classNameActionFieldType::getType();
    }

    public function getEvents(): array
    {
        if (!$this->onlyEvents) {
            return [];
        }

        return $this->onlyEvents;
    }

    /**
     * @return RuleEntityResource|null
     */
    public function getRuleEntityResource(): ?RuleEntityResource
    {
        return $this->ruleEntityResource;
    }
}
