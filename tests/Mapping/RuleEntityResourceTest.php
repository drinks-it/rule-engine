<?php

/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2026 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Mapping;

use DrinksIt\RuleEngineBundle\Helper\ClassHelper;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use PHPUnit\Framework\TestCase;

class RuleEntityResourceTest extends TestCase
{
    use AttributeFixtures {
        loadResourceWithBrokeProperty as load;
    }
    private const CLASS_NAME_ENTITY = 'EntityTypeResource';

    public function testInitResource(): void
    {
        $this->assertInstanceOf(RuleEntityResource::class, new RuleEntityResource());
    }

    public function testParseFromEntity(): void
    {
        $this->load(self::CLASS_NAME_ENTITY);
        $this->assertTrue(ClassHelper::exist(self::CLASS_NAME_ENTITY));

        $reflection = new \ReflectionClass(self::CLASS_NAME_ENTITY);
        $ruleEntityResource = $reflection->getAttributes(RuleEntityResource::class)[0]->newInstance();

        $this->assertInstanceOf(RuleEntityResource::class, $ruleEntityResource);
    }

    public function testRuleEntityResourceSupportOnlyClass(): void
    {
        $this->load(self::CLASS_NAME_ENTITY);

        $this->expectException(\Error::class);
        $this->assertTrue(ClassHelper::exist(self::CLASS_NAME_ENTITY));

        $reflection = new \ReflectionClass(self::CLASS_NAME_ENTITY);
        $propertyReflection = $reflection->getProperty('test');
        $ruleEntityResource = $propertyReflection->getAttributes(RuleEntityResource::class)[0]->newInstance();

        $this->assertInstanceOf(RuleEntityResource::class, $ruleEntityResource);
    }
}
