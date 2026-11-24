<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2026 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Extractor;

use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityAttributeExtractor;

class RuleEntityAttributeExtractorTest extends \PHPUnit\Framework\TestCase
{
    public function testAttributeMappings(): void
    {
        $e = new RuleEntityAttributeExtractor();
        $this->assertTrue($e->getRuleEntityResourceAnnotation(AttributeCheck::class)->events === ['event']);
        $this->assertTrue($e->getRuleEntityPropertyAnnotation(AttributeCheck::class, 'field')->condition === 'condition');
        $this->assertTrue($e->getRuleEntityPropertiesNames(AttributeCheck::class) === ['field']);
        $this->assertTrue($e->getRuleEntityClassNameFromRelationField(AttributeCheck::class, 'relation') === AttributeCheck::class);
        $this->assertTrue($e->getRuleEntityResourceAnnotation(\stdClass::class) === null);
        $this->assertTrue($e->getRuleEntityClassNameFromRelationField('MissingClass', 'field') === null);
    }
}

#[RuleEntityResource(events: ['event'])]
class AttributeCheck
{
    #[RuleEntityProperty(condition: 'condition')]
    public $field;
    #[\Doctrine\ORM\Mapping\ManyToOne(targetEntity: AttributeCheck::class)]
    public $relation;
}
