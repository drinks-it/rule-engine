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

    private string $classNameAttributeConditionType;

    public function __construct(string $name, string $classNameAttributeConditionType)
    {
        $this->name = $name;
        $this->classNameAttributeConditionType = $classNameAttributeConditionType;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getClassNameAttributeConditionType(): string
    {
        return $this->classNameAttributeConditionType;
    }
}
