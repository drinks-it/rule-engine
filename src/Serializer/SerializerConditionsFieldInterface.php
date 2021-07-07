<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;

interface SerializerConditionsFieldInterface
{
    public function decodeToConditionCollection(array $conditions): CollectionConditionInterface;

    public function encodeToConditionToArray(CollectionConditionInterface $collectionCondition): array;
}