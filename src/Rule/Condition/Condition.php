<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;

final class Condition
{
    /**
     * Order priority conditions.
     */
    private int $priority;

    // ANY|ALL of these conditions are IS_TRUE
    public const IS_TRUE = true;

    // ANY|ALL of these conditions are IS_FALSE
    public const IS_FALSE = false;

    /**
     * @see IS_TRUE
     * @see IS_FALSE
     */
    private bool $result = self::IS_TRUE;

    public const TYPE_ANY = 'ANY';

    public const TYPE_ALL = 'ALL';

    public const TYPE_ATTRIBUTE = 'ATTRIBUTE';

    /**
     * @see TYPE_ANY
     * @see TYPE_ALL
     * @see TYPE_ATTRIBUTE
     */
    private ?string $type = self::TYPE_ANY;

    /**
     * If ifType == IF_ATTRIBUTE.
     */
    private ?AttributeConditionTypeInterface $attributeCondition = null;

    private ?CollectionConditionInterface $subConditions = null;

    public function __construct(
        string $type = self::TYPE_ANY,
        int $priority = 1,
        AttributeConditionTypeInterface $attributeCondition = null,
        CollectionConditionInterface $subConditions = null,
        bool $result = null
    ) {
        $this->type     = $type;
        $this->priority = $priority;

        if ($attributeCondition instanceof AttributeConditionTypeInterface) {
            $this->attributeCondition = $attributeCondition;
        }

        if (null !== $subConditions) {
            $this->subConditions = $subConditions;
        }

        if (null !== $result) {
            $this->result = $result;
        }
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isNotDefaultResult(): bool
    {
        return self::TYPE_ATTRIBUTE === $this->getType();
    }

    public function getResultBlock(): bool
    {
        return $this->result;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAttributeCondition(): ?AttributeConditionTypeInterface
    {
        return $this->attributeCondition;
    }

    public function getSubConditions(): ?CollectionConditionInterface
    {
        return $this->subConditions;
    }

    public function setAttributeCondition(AttributeConditionTypeInterface $attributeCondition): self
    {
        $this->attributeCondition = $attributeCondition;

        return $this;
    }

    /**
     * @param CollectionConditionInterface|null $subConditions
     */
    public function setSubConditions(CollectionConditionInterface $subConditions): self
    {
        $this->subConditions = $subConditions;

        return $this;
    }

    public function setResult(bool $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * For serialization.
     */
    public function toArray(): array
    {
        $attributeCondition = $this->getAttributeCondition();

        if ($attributeCondition instanceof AttributeConditionTypeInterface) {
            $attributeCondition = $attributeCondition->toArray();
        }

        $subConditions = $this->getSubConditions();

        if ($subConditions instanceof CollectionConditionInterface) {
            $subConditions = $subConditions->toArray();
        }

        return [
            'priority'            => $this->getPriority(),
            'type'                => $this->getType(),
            'result'              => $this->isNotDefaultResult() ? null : $this->getResultBlock(),
            'attribute_condition' => $attributeCondition,
            'sub_conditions'      => $subConditions,
        ];
    }
}
