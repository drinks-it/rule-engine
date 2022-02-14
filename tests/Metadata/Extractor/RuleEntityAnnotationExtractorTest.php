<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Metadata\Extractor;

use Doctrine\Common\Annotations\Reader;
use DrinksIt\RuleEngineBundle\Helper\ClassHelper;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityProperty;
use DrinksIt\RuleEngineBundle\Mapping\RuleEntityResource;
use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityAnnotationExtractor;
use DrinksIt\RuleEngineBundle\Metadata\Extractor\RuleEntityExtractorInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\AttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;
use Tests\DrinksIt\RuleEngineBundle\Mapping\AnnotationFixtures;

class RuleEntityAnnotationExtractorTest extends TestCase
{
    use AnnotationFixtures;

    private $test_self_property_not_use;

    public function testConstructEmptyReader(): void
    {
        $rule = new RuleEntityAnnotationExtractor();

        $this->assertInstanceOf(RuleEntityExtractorInterface::class, $rule);

        $this->assertNull($rule->getRuleEntityResourceAnnotation('AnyClassName'));
        $this->assertIsArray($rule->getRuleEntityPropertiesNames('AnyClassName'));
        $this->assertNull($rule->getRuleEntityPropertyAnnotation('AnyClassName', 'fieldName'));

        // class not exists
        $rule = new RuleEntityAnnotationExtractor($this->createMock(Reader::class));

        $this->assertInstanceOf(RuleEntityExtractorInterface::class, $rule);

        $this->assertNull($rule->getRuleEntityResourceAnnotation('AnyClassName'));
        $this->assertIsArray($rule->getRuleEntityPropertiesNames('AnyClassName'));
        $this->assertNull($rule->getRuleEntityPropertyAnnotation('AnyClassName', 'fieldName'));
    }

    public function testGetRuleEntityResourceAnnotation(): void
    {
        $className = 'EntityClassNameRuleEntityAnnotationExtractor';

        $mockReader = $this->createMock(Reader::class);
        $mockReader->expects($this->exactly(2))->method('getClassAnnotation')
            ->with($this->isInstanceOf(\ReflectionClass::class))
            ->willReturnCallback(function (\ReflectionClass $classNameArgument) use ($className) {
                if ($className === $classNameArgument->getName()) {
                    return new RuleEntityResource();
                }

                return null;
            });

        $rule = new RuleEntityAnnotationExtractor($mockReader);

        // null cases
        $this->assertNull($rule->getRuleEntityResourceAnnotation(self::class));

        $this->loadResourceWithValidProperty($className);

        // check with annotation
        $this->assertTrue(ClassHelper::exist($className));
        $this->assertInstanceOf(RuleEntityResource::class, $rule->getRuleEntityResourceAnnotation($className));
    }

    public function testGetRuleEntityPropertyAnnotation(): void
    {
        $className = 'EntityClassNameRuleEntityAnnotationExtractorProperty';

        $mockReader = $this->createMock(Reader::class);
        $mockReader->expects($this->exactly(2))->method('getPropertyAnnotation')->with(
            $this->isInstanceOf(\ReflectionProperty::class)
        )->willReturnCallback(function (\ReflectionProperty $property) {
            if ($property->getName() === 'test_self_property_not_use') {
                return null;
            }

            $rule = new RuleEntityProperty();
            $rule->condition = AttributeConditionTypeInterface::class;

            return $rule;
        });

        $rule = new RuleEntityAnnotationExtractor($mockReader);
        $this->assertNull($rule->getRuleEntityPropertyAnnotation(self::class, 'test_self_property_not_use'));

        $this->loadResourceWithValidProperty($className, 'check_valid_property');
        $this->assertTrue(ClassHelper::exist($className));

        $this->assertInstanceOf(
            RuleEntityProperty::class,
            $rule->getRuleEntityPropertyAnnotation($className, 'check_valid_property')
        );
    }

    public function testGetRuleEntityPropertiesNames(): void
    {
        $rule = new RuleEntityAnnotationExtractor();

        $this->assertGreaterThanOrEqual(1, \count($rule->getRuleEntityPropertiesNames(self::class)));

        // check empty properties
        $this->assertCount(0, $rule->getRuleEntityPropertiesNames(\stdClass::class));
    }
}
