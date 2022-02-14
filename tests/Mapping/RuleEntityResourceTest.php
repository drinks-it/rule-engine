<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Mapping;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use DrinksIt\RuleEngineBundle\Helper\ClassHelper;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use PHPUnit\Framework\TestCase;

class RuleEntityResourceTest extends TestCase
{
    use AnnotationFixtures {
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
        $reader = new AnnotationReader();
        $ruleEntityResource = $reader->getClassAnnotation($reflection, RuleEntityResource::class);

        $this->assertInstanceOf(RuleEntityResource::class, $ruleEntityResource);
    }

    public function testRuleEntityResourceSupportOnlyClass(): void
    {
        $this->load(self::CLASS_NAME_ENTITY);

        $this->expectException(AnnotationException::class);
        $this->assertTrue(ClassHelper::exist(self::CLASS_NAME_ENTITY));

        $reflection = new \ReflectionClass(self::CLASS_NAME_ENTITY);
        $propertyReflection = $reflection->getProperty('test');
        $reader = new AnnotationReader();
        $ruleEntityResource = $reader->getPropertyAnnotation($propertyReflection, RuleEntityResource::class);

        $this->assertInstanceOf(RuleEntityResource::class, $ruleEntityResource);
    }
}
