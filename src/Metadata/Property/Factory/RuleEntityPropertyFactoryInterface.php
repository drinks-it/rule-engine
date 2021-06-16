<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Property\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;

interface RuleEntityPropertyFactoryInterface
{
    /**
     * @param string $entityClass
     * @return PropertyRuleEntity[]
     */
    public function create(string $entityClass): array;
}
