<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityExtractorInterface;
use DrinksIt\RuleEngineBundle\Metadata\Property\Factory\RuleEntityPropertyFactoryInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntity;

final class RuleEntityResourceFactory implements RuleEntityResourceFactoryInterface
{
    private RuleEntityExtractorInterface $annotationExtractor;

    private RuleEntityPropertyFactoryInterface $ruleEntityPropertyFactory;

    public function __construct(
        RuleEntityExtractorInterface $annotationExtractor,
        RuleEntityPropertyFactoryInterface $ruleEntityPropertyFactory
    ) {
        $this->annotationExtractor = $annotationExtractor;
        $this->ruleEntityPropertyFactory = $ruleEntityPropertyFactory;
    }

    public function create(string $entityClass): ?ResourceRuleEntity
    {
        $annotationResource = $this->annotationExtractor->getRuleEntityResourceAnnotation($entityClass);

        if (!$annotationResource) {
            return null;
        }

        return new ResourceRuleEntity(
            $entityClass,
            $this->ruleEntityPropertyFactory->create($entityClass),
            $annotationResource->events
        );
    }
}
