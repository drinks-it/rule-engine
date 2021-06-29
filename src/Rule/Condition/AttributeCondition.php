<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;

abstract class AttributeCondition implements AttributeConditionTypeInterface
{
    protected string $classResource;

    protected string $fieldName;

    private array $supportsOperators;

    protected $value;

    protected string $operator;

    public function __construct(string $classResource, string $fieldName, string $operator = null, $value = null)
    {
        $this->classResource = $classResource;
        $this->fieldName = $fieldName;

        $reflection = new \ReflectionClass(static::class);
        $this->supportsOperators = array_filter(
            $reflection->getConstants() ?: [],
            fn ($constName) => strpos($constName, 'OPERATOR_') !== false,
            ARRAY_FILTER_USE_KEY
        );

        if ($operator) {
            $this->setOperator($operator);
        }

        if ($value) {
            $this->setValue($value);
        }
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

    public function toArray(): array
    {
        return [
            'class_resource' => $this->getClassResource(),
            'field_name'     => $this->getFieldName(),
            'operator'      => $this->getOperator(),
            'type'          => $this->getType(),
            'value'         => $this->getValue(),
        ];
    }
}
