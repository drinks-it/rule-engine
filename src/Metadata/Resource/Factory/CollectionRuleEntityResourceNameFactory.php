<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Resource\Factory;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityExtractorInterface;
use DrinksIt\RuleEngineBundle\Metadata\Resource\ResourceRuleEntityNameCollection;
use Symfony\Bridge\Doctrine\ManagerRegistry;

final class CollectionRuleEntityResourceNameFactory implements CollectionRuleEntityResourceNameFactoryInterface
{
    private ManagerRegistry $manageRegistry;

    private RuleEntityExtractorInterface $annotationExtractor;

    public function __construct(
        ManagerRegistry $manageRegistry,
        RuleEntityExtractorInterface $annotationExtractor
    ) {
        $this->manageRegistry = $manageRegistry;
        $this->annotationExtractor = $annotationExtractor;
    }

    public function create(): ResourceRuleEntityNameCollection
    {
        $resources = [];

        foreach ($this->manageRegistry->getManager()->getMetadataFactory()->getAllMetadata() as $classMetadata) {
            $ruleEntityResource = $this->annotationExtractor->getRuleEntityResourceAnnotation($classMetadata->getName());

            if (!$ruleEntityResource instanceof RuleEntityResource) {
                continue;
            }
            $resources[] = $classMetadata->getName();
        }

        return new ResourceRuleEntityNameCollection($resources);
    }
}
