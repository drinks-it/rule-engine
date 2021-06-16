<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Property;

use DrinksIt\RuleEngineBundle\Metadata\Property\PropertyRuleEntity;
use DrinksIt\RuleEngineBundle\Rule\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class PropertyRuleEntityTest extends TestCase
{
    public function testInit(): void
    {
        $propertyRuleEntity = new PropertyRuleEntity('Name', AttributeConditionTypeInterface::class);
        $this->assertEquals('Name', $propertyRuleEntity->getName());
        $this->assertEquals(AttributeConditionTypeInterface::class, $propertyRuleEntity->getClassNameAttributeConditionType());
    }
}
