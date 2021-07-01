<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule;

use Doctrine\Common\Collections\Collection;

interface CollectionActionsInterface extends Collection
{
    public function execute(iterable $data): void;
}
