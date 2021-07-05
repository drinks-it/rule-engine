<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Serializer;

use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;

/**
 * Class DenormalizeAction
 * @package DrinksIt\RuleEngineBundle\Doctrine\Serializer
 * @internal
 */
final class DenormalizeAction
{
    private CollectionActionsInterface $collectionActions;

    public function __construct(CollectionActionsInterface $collectionActions)
    {
        $this->collectionActions = $collectionActions;
    }

    public function denormalizeCollection(): array
    {
        return [
            'class_collection_action' => \get_class($this->collectionActions),
            'elements' => $this->collectionActions->map(
                fn (ActionInterface $action) => $this->denormalizeAction($action)
            )->toArray(),
        ];
    }

    public function denormalizeAction(ActionInterface $action): array
    {
        return [
            'class_action' => \get_class($action),
            'properties' => [
                'type' => $action->getType(),
                'object_resource' => $action->getResourceClass(),
                'field' => $action->getFieldName(),
                'action' => $action->getAction(),
            ],
        ];
    }
}
