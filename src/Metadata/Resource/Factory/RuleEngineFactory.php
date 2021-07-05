<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityCollection;

final class RuleEngineFactory implements RuleEngineFactoryInterface
{
    private CollectionRuleEntityResourceNameFactoryInterface $collectionRuleEntityResourceNameFactory;

    private RuleEntityResourceFactoryInterface $resourceFactory;

    public function __construct(
        CollectionRuleEntityResourceNameFactoryInterface $collectionRuleEntityResourceNameFactory,
        RuleEntityResourceFactoryInterface $resourceFactory
    ) {
        $this->collectionRuleEntityResourceNameFactory = $collectionRuleEntityResourceNameFactory;
        $this->resourceFactory = $resourceFactory;
    }

    public function create(): ResourceRuleEntityCollection
    {
        $resourceCollection = new ResourceRuleEntityCollection();

        foreach ($this->collectionRuleEntityResourceNameFactory->create() as $className) {
            $resourceRuleEntity = $this->resourceFactory->create($className);

            if (!$resourceRuleEntity instanceof ResourceRuleEntity) {
                continue;
            }

            $resourceCollection->push($resourceRuleEntity);
        }

        return $resourceCollection;
    }
}
