<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action;

use Doctrine\Common\Collections\ArrayCollection;
use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\Exception\ClassDoesNotImplementInterfaceRuleException;
use DrinksIt\RuleEngineBundle\Rule\Exception\TypeArgumentRuleException;

class CollectionActions extends ArrayCollection implements CollectionActionsInterface
{
    public function execute($objectEntity): void
    {
        if (!\is_object($objectEntity)) {
            throw new TypeArgumentRuleException(\gettype($objectEntity), static::class, 'execute', 'object');
        }
        /** @var ActionInterface $action */
        foreach ($this as $action) {
            if (!$action instanceof ActionInterface) {
                throw new ClassDoesNotImplementInterfaceRuleException(\get_class($action), ActionInterface::class);
            }
            $resourceClass = $action->getResourceClass();

            if ($resourceClass === \get_class($objectEntity)) {
                $action->executeAction($objectEntity);

                continue;
            }

            $objectToExecute = $objectEntity;

            if ($objectToExecute instanceof ExecuteMethodAction) {
                $objectToExecute = $objectToExecute->getObjectToExecute();
            }

            $methodName = StrEntity::getGetterNameMethod(
                StrEntity::getShortName($resourceClass)
            );

            if (!method_exists($objectToExecute, $methodName)) {
                continue;
            }

            $action->executeAction($objectToExecute->{$methodName}());
        }
    }
}
