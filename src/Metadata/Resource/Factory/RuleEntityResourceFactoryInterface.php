<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;

interface RuleEntityResourceFactoryInterface
{
    public function create(string $entityClass): ?ResourceRuleEntity;
}
