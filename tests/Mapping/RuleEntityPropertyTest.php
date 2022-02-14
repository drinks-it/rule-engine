<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Mapping;

use Doctrine\Common\Annotations\AnnotationReader;
use DrinksIt\RuleEngineBundle\Helper\ClassHelper;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class RuleEntityPropertyTest extends TestCase
{
    use AnnotationFixtures;

    private const CLASS_NAME_ENTITY = 'EntityPropertyTypeResource';

    public function testParseAnnotation(): void
    {
        $this->loadResourceWithValidProperty(self::CLASS_NAME_ENTITY);
        $this->assertTrue(ClassHelper::exist(self::CLASS_NAME_ENTITY));

        $reflectionEntityClass = new \ReflectionClass(self::CLASS_NAME_ENTITY);
        $reader = new AnnotationReader();

        $isExistOnProperty = false;
        foreach ($reflectionEntityClass->getProperties() as $property) {
            /** @var RuleEntityProperty $ruleEntityProperty */
            $ruleEntityProperty = $reader->getPropertyAnnotation($property, RuleEntityProperty::class);

            if (\is_null($ruleEntityProperty)) {
                continue;
            }
            $this->assertInstanceOf(RuleEntityProperty::class, $ruleEntityProperty);
            $this->assertEquals(AttributeConditionTypeInterface::class, $ruleEntityProperty->condition);
            $isExistOnProperty = true;
        }

        $this->assertTrue($isExistOnProperty);
    }
}
