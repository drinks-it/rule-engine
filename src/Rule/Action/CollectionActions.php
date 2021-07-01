<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Action;

use Doctrine\Common\Collections\ArrayCollection;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;

class CollectionActions extends ArrayCollection implements CollectionActionsInterface
{
    public function execute(iterable $data): void
    {
        /** @var ActionInterface $action */
        foreach ($this->getIterator() as $action) {
            if (!$action instanceof ActionInterface) {
                // todo
                throw new \RuntimeException('Action must implement interface ' . ActionInterface::class);
            }

            foreach ($data as $objectEntity) {
                if (!\is_object($objectEntity)) {
                    // todo
                    throw new \RuntimeException('The $data must has objects collection ');
                }
                $action->executeAction($objectEntity);
            }
        }
    }
}
