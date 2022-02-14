<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule;

use Doctrine\Common\Collections\Collection;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;

/**
 * Interface CollectionActionsInterface
 * @package DrinksIt\RuleEngineBundle\Rule
 *
 * @method ActionInterface[] getIterator()
 */
interface CollectionActionsInterface extends Collection
{
    public function execute($objectEntity): void;
}
