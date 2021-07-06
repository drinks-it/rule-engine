<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEntityResourceFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Serializer\Exception\NotExistPropertyInterfaceException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\PropertyNotFoundException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceIsNotIdentifyException;
use DrinksIt\RuleEngineBundle\Serializer\Exception\ResourceNotFoundException;

final class SerializerActionsField implements SerializerActionsFieldInterface
{
    private string $collectionActionClassName;

    private RuleEntityResourceFactoryInterface $ruleEngineFactory;

    public function __construct(
        string $collectionActionClassName,
        RuleEntityResourceFactoryInterface $ruleEngineFactory
    ) {
        $this->collectionActionClassName = $collectionActionClassName;
        $this->ruleEngineFactory = $ruleEngineFactory;
    }

    public function decodeToActionCollection(array $actions, array $context = []): CollectionActionsInterface
    {
        $collection = $this->makeNewCollection();
        foreach ($actions as $action) {
            $resourceClassName  = $action['resource'] ?? null;

            if (!$resourceClassName && isset($context['rule_default_resource'])) {
                $resourceClassName = $context['rule_default_resource'];
            }

            if (!$resourceClassName) {
                throw new ResourceIsNotIdentifyException();
            }

            $ruleEngineResourceMetaData = $this->ruleEngineFactory->create($resourceClassName);

            if (!$ruleEngineResourceMetaData) {
                throw new ResourceNotFoundException($resourceClassName);
            }
            $properties = $ruleEngineResourceMetaData->getProperties();

            if (!\array_key_exists($action['field'], $properties)) {
                throw new PropertyNotFoundException($resourceClassName, $action['field']);
            }

            $propertyMetadata = $properties[$action['field']];
            $classNameAction  = $propertyMetadata->getClassNameActionFieldType();

            if (!$classNameAction) {
                throw new NotExistPropertyInterfaceException($resourceClassName, $action['field'], ActionInterface::class, true);
            }
            $actionObject     = new $classNameAction($action['field'], $resourceClassName, $action['action']);

            if (!$actionObject instanceof ActionInterface) {
                throw new NotExistPropertyInterfaceException($resourceClassName, $action['field'], ActionInterface::class);
            }
            $collection->add($actionObject);
        }

        return $collection;
    }

    public function encodeActionCollectionToArray(iterable $collectionActions, array $context = []): array
    {
        if (!$collectionActions) {
            return [];
        }
        $returnValues = [];
        foreach ($collectionActions as $action) {
            $returnValues[] = $this->encodeActionToArray($action);
        }

        return $returnValues;
    }

    public function encodeActionToArray(ActionInterface $action, array $context = []): array
    {
        return [
            'resource' => $action->getResourceClass(),
            'type' => $action->getType(),
            'field' => $action->getFieldName(),
            'action' => $action->getPatternExecute(),
        ];
    }

    private function makeNewCollection(): CollectionActionsInterface
    {
        $collectionName = $this->collectionActionClassName;

        return new $collectionName();
    }
}
