<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Metadata\Extractor;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;

interface RuleEntityExtractorInterface
{
    public const RELATION_CLASS_NAMES = [
        'Doctrine\ORM\Mapping\ManyToOne',
        'Doctrine\ORM\Mapping\OneToOne',
        'Doctrine\ORM\Mapping\OneToMany',
        'Doctrine\ORM\Mapping\ManyToMany',
    ];

    public function getRuleEntityResourceAnnotation(string $class): ?RuleEntityResource;

    public function getRuleEntityPropertiesNames(string $classResource): array;

    public function getRuleEntityPropertyAnnotation(string $classResource, string $propertyName): ?RuleEntityProperty;

    public function getRuleEntityClassNameFromRelationField(string $classResource, string $propertyRelationName): ?string;
}
