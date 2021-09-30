<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action;

use Doctrine\Common\Collections\ArrayCollection;
use DrinksIt\RuleEngineBundle\Doctrine\Helper\StrEntity;
use DrinksIt\RuleEngineBundle\Rule\ActionPropertyNormalizerInterface;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use DrinksIt\RuleEngineBundle\Rule\Exception\ClassDoesNotImplementInterfaceRuleException;
use DrinksIt\RuleEngineBundle\Rule\Exception\TypeArgumentRuleException;
use DrinksIt\RuleEngineBundle\Serializer\NormalizerPropertyInterface;

class CollectionActions extends ArrayCollection implements CollectionActionsInterface, ActionPropertyNormalizerInterface
{
    private ?NormalizerPropertyInterface $normalizerProperty = null;

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

            if ($action instanceof ActionPropertyNormalizerInterface && $this->normalizerProperty) {
                $action->setNormalizer($this->normalizerProperty);
            }

            $resourceClass = $action->getResourceClass();

            if ($objectEntity instanceof ExecuteMethodAction || $resourceClass === \get_class($objectEntity)) {
                $action->executeAction($objectEntity);

                continue;
            }

            $methodName = StrEntity::getGetterNameMethod(
                StrEntity::getShortName($resourceClass)
            );

            if (!method_exists($objectEntity, $methodName)) {
                continue;
            }

            $action->executeAction($objectEntity->{$methodName}());
        }
    }

    public function setNormalizer(NormalizerPropertyInterface $normalizerProperty): self
    {
        $this->normalizerProperty = $normalizerProperty;

        return $this;
    }
}
