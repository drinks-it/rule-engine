<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource;

use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;

/**
 * Class ResourceRuleEntity
 * @package DrinksIt\RuleEngineBundle\Metadata\Resource
 */
final class ResourceRuleEntity
{
    private string $className;

    /**
     * @var PropertyRuleEntity[]
     */
    private array $properties;

    public function __construct(string $className, array $properties = [])
    {
        $this->className = $className;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return PropertyRuleEntity[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}