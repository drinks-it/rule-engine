<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityNameCollection;

interface CollectionRuleEntityResourceNameFactoryInterface
{
    /**
     * @return ResourceRuleEntityNameCollection
     */
    public function create(): ResourceRuleEntityNameCollection;
}
