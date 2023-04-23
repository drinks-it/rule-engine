<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Helper\DecodeRuleProperty;

abstract class AttributeCondition implements AttributeConditionTypeInterface
{
    protected string $classResource;

    protected string $fieldName;

    private array $supportsOperators;

    protected $value;

    protected string $operator;

    protected ?string $resourceShortName;

    public function __construct(string $classResource, string $fieldName, string $operator = null, $value = null, ?string $resourceShortName = null)
    {
        $this->classResource = $classResource;
        $this->fieldName = $fieldName;
        $this->supportsOperators = DecodeRuleProperty::getConstByKey('OPERATOR_', static::class);

        if ($operator) {
            $this->setOperator($operator);
        }

        $this->setValue($value);
        $this->setResourceShortName($resourceShortName);
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function setOperator(string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getClassResource(): string
    {
        return $this->classResource;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getSupportOperators(): array
    {
        return $this->supportsOperators;
    }

    /**
     * @return string|null
     */
    public function getResourceShortName(): ?string
    {
        return $this->resourceShortName;
    }

    /**
     * @param string|null $resourceShortName
     */
    public function setResourceShortName(?string $resourceShortName): self
    {
        $this->resourceShortName = $resourceShortName;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'class_resource' => $this->getClassResource(),
            'field_name'     => $this->getFieldName(),
            'operator'      => $this->getOperator(),
            'type'          => $this->getType(),
            'value'         => $this->getValue(),
            'resource_short_name' => $this->getResourceShortName(),
        ];
    }
}
