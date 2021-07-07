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

    public function decodeToActionCollection(array $actions): CollectionActionsInterface
    {
        $collection = $this->makeNewCollection();
        foreach ($actions as $action) {
            $resourceClassName = $action['resource'];
            $ruleEngineResourceMetaData = $this->ruleEngineFactory->create($resourceClassName);

            if (!$ruleEngineResourceMetaData) {
                throw new \RuntimeException('Can not decode condition. Not found metadata rule engine for resource `'.$resourceClassName.'`');
            }
            $properties = $ruleEngineResourceMetaData->getProperties();

            if (!\array_key_exists($action['field'], $properties)) {
                throw new \RuntimeException('Property `'.$action['field'].'` not found in rule engine metadata in class `'.$resourceClassName.'`');
            }

            $propertyMetadata = $properties[$action['field']];
            $classNameAction  = $propertyMetadata->getClassNameActionFieldType();
            $actionObject     = new $classNameAction($action['field'], $resourceClassName, $action['action']);

            if (!$actionObject instanceof ActionInterface) {
                throw new \RuntimeException('Class `'.$classNameAction.'` must be implement `'.ActionInterface::class.'`');
            }
            $collection->add($actionObject);
        }

        return $collection;
    }

    public function encodeActionCollectionToArray(CollectionActionsInterface $collectionActions): array
    {
        if ($collectionActions->isEmpty()) {
            return [];
        }
        $returnValues = [];
        foreach ($collectionActions as $action) {
            $returnValues[] = [
                'resource' => $action->getResourceClass(),
                'type' => $action::getType(),
                'field' => $action->getFieldName(),
                'action' => $action->getAction(),
            ];
        }

        return $returnValues;
    }

    private function makeNewCollection(): CollectionActionsInterface
    {
        $collectionName = $this->collectionActionClassName;

        return new $collectionName();
    }
}
