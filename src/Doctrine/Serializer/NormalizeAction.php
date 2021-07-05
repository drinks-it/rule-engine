<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Serializer;

use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassDoesNotImplementInterfaceException;
use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassNotExistRuleEngineDoctrineException;
use DrinksIt\RuleEngineBundle\Helper\ClassHelper;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;

final class NormalizeAction
{
    private array $actions;

    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function normalizeCollection(): CollectionActionsInterface
    {
        $classCollection = $this->actions['class_collection_action'];

        if (!ClassHelper::exist($classCollection)) {
            throw new ClassNotExistRuleEngineDoctrineException($classCollection);
        }

        if (!$this->actions['elements']) {
            return new $classCollection();
        }

        return new $classCollection(
            array_map(fn (array $action) => $this->normalizeAction($action), $this->actions['elements'])
        );
    }

    public function normalizeAction(array $action): ActionInterface
    {
        $classNameAction = $action['class_action'];
        $properties = $action['properties'];

        if (!ClassHelper::exist($classNameAction)) {
            throw new ClassNotExistRuleEngineDoctrineException($classNameAction);
        }
        /** @var ActionInterface $actionObject */
        $actionObject = new $classNameAction($properties['field'], $properties['object_resource']);

        if (!$actionObject instanceof ActionInterface) {
            throw new ClassDoesNotImplementInterfaceException($classNameAction, ActionInterface::class);
        }

        $actionObject->setAction($properties['action']);

        return $actionObject;
    }
}
