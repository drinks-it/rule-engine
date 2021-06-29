<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\StringAttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class StringConditionAttributeTest extends TestCase
{
    /**
     * @dataProvider dataStringCases
     * @param mixed $valueSet
     * @param mixed $valueMatch
     * @param null|mixed $exception
     */
    public function testMatchStringCondition(string $operator, $valueSet, $valueMatch, bool $isEq = false, $exception = null): void
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $attributeCondition = new StringConditionAttribute(\stdClass::class, 'anyFieldName');
        $this->assertInstanceOf(StringAttributeConditionTypeInterface::class, $attributeCondition);

        $attributeCondition->setValue($valueSet)->setOperator($operator);
        $this->assertIsString($attributeCondition->getType());

        $this->assertEquals($isEq, $attributeCondition->match($valueMatch));
    }

    public function dataStringCases(): array
    {
        return [
            [
                StringAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                'Hello, Going to Test',
                'Test',
                true,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                'Hello, Going to Test',
                'Going to',
                true,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                "Hello, Going to Test\nTestCheck",
                "Test\nTestCheck",
                true,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                "Hello, Going to Test\nTestCheck",
                "Hello, Going to Test\nTestCheck",
                true,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                "Hello, Going to Test\nTestCheck",
                "Test\nTestCheck",
                false,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                'Hello, Going to Test',
                'Test',
                false,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                1,
                '1',
                true,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                1.1,
                '1.1',
                true,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                [1, 2, 3, 4],
                [1, 2, 3, 4],
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                new class() {},
                new class() {},
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                new class() {
                    public function __toString(): string
                    {
                        return 'CheckToString';
                    }
                },
                new class() {},
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                new class() {
                    public function __toString(): string
                    {
                        return 'CheckToString';
                    }
                },
                new class() {
                    public function __toString(): string
                    {
                        return 'CheckToString';
                    }
                },
                true,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                'CheckToString',
                new class() {
                    public function __toString(): string
                    {
                        return 'CheckToString';
                    }
                },
                true,
            ],
            [
                'RandomOperator',
                'CheckToString',
                'CheckToString',
                false,
            ],
            [
                StringAttributeConditionTypeInterface::OPERATOR_EQ,
                false,
                'CheckToString',
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
        ];
    }
}
