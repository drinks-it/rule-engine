<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Serializer\Exception\RuleEngineSerializerExceptionInterface;

interface SerializerConditionsFieldInterface
{
    /**
     * @throws RuleEngineSerializerExceptionInterface
     */
    public function decodeToConditionCollection(array $conditions, array $context = []): CollectionConditionInterface;

    public function encodeCollectionConditionToArray(iterable $collectionCondition, array $context = []): array;

    public function encodeItemConditionToArray(Condition $condition, array $context = []): array;
}
