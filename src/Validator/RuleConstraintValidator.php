<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Validator;

use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Metadata\Resource\Factory\RuleEngineFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;
use Symfony\Component\Validator\ConstraintValidator;

abstract class RuleConstraintValidator extends ConstraintValidator
{
    private RuleEngineFactoryInterface $ruleEngineFactory;

    public function __construct(
        RuleEngineFactoryInterface $ruleEngineFactory
    ) {
        $this->ruleEngineFactory = $ruleEngineFactory;
    }

    public function getResourceRuleEntity(string $resourceName): ?ResourceRuleEntity
    {
        foreach ($this->ruleEngineFactory->create() as $resourceRuleEntity) {
            if ($resourceRuleEntity->getClassName() !== $resourceName) {
                continue;
            }

            return $resourceRuleEntity;
        }

        return null;
    }

    public function getPropertyFromResource(ResourceRuleEntity $resourceRuleEntity, string $fieldName): ?PropertyRuleEntity
    {
        foreach ($resourceRuleEntity->getProperties() as $property) {
            if ($property->getName() !== $fieldName) {
                continue;
            }

            return $property;
        }

        return null;
    }
}
