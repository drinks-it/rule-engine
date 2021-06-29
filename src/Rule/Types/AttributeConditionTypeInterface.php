<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Types;

interface AttributeConditionTypeInterface
{
    public function __construct(string $classResource, string $fieldName);

    public function getClassResource(): string;

    public function getFieldName(): string;

    public function getOperator(): string;

    public function setOperator(string $operator): self;

    public function setValue($value): self;

    public function getValue();

    public function toArray(): array;

    /**
     * can be is
     * 'array'
     * 'boolean' ,'bool,'double',
     * 'float','integer','int, 'numeric',
     * 'string'
     * or full class name.
     *  <br>
     * If type is class. Need Implement Interface ConditionValueInterface
     * @see ConditionValueInterface
     */
    public function getType(): string;

    /**
     * @return array<string>
     */
    public function getSupportOperators(): array;

    public function match($value): bool;
}
