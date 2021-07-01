<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Property;

final class PropertyRuleEntity
{
    private string $name;

    private ?string $classNameAttributeConditionType;

    private ?string $classNameActionFieldType;

    public function __construct(string $name, string $classNameAttributeConditionType = null, string $classNameActionFieldType = null)
    {
        $this->name = $name;
        $this->classNameAttributeConditionType = $classNameAttributeConditionType;
        $this->classNameActionFieldType = $classNameActionFieldType;
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

    /**
     * @return string|null
     */
    public function getClassNameActionFieldType(): ?string
    {
        return $this->classNameActionFieldType;
    }
}
