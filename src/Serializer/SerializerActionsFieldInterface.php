<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Serializer\Exception\RuleEngineSerializerExceptionInterface;

interface SerializerActionsFieldInterface
{
    /**
     * @throws RuleEngineSerializerExceptionInterface
     */
    public function decodeToActionCollection(array $actions, array $context = []): CollectionActionsInterface;

    public function encodeActionCollectionToArray(iterable $collectionActions, array $context = []): array;

    public function encodeActionToArray(ActionInterface $action, array $context = []): array;
}
