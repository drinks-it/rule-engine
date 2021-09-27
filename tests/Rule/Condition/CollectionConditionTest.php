<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition;

use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\NumberConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\StringConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\KeyNotFoundInArrayConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\MethodDoesNotExistException;
use PHPUnit\Framework\TestCase;

class CollectionConditionTest extends TestCase
{
    /**
     * @param CollectionCondition $collectionCondition
     * @param $exceptedResult
     * @param mixed $mockEntity
     *
     * @param string|null $exceptionClass
     * @dataProvider dataScriptsConditions
     * @dataProvider dataExceptionConditions
     */
    public function testIsMatched(CollectionCondition $collectionCondition, $exceptedResult, $mockEntity, string $exceptionClass = null): void
    {
        if ($exceptionClass) {
            $this->expectException($exceptionClass);
        }
        $this->assertEquals($exceptedResult, $collectionCondition->isMatched($mockEntity));
    }

    public function dataScriptsConditions(): iterable
    {
        $closerClass = new class() {
            public function getField1(): string
            {
                return 'Eko Hi';
            }

            public function getField2(): float
            {
                return 3;
            }
        };
        yield 'ANY (Ture) | Ex: true' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ANY,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closerClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\get_class($closerClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                        ),
                    ]),
                    true,
                ),
            ]),
            true,
            $closerClass,
            null,
        ];

        $closerClass = new class() {
            public function getField1(): string
            {
                return 'Eko Hi';
            }

            public function getField2(): float
            {
                return 5;
            }
        };
        yield 'ANY (Ture) | Ex: false' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ANY,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closerClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\get_class($closerClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                        ),
                    ]),
                    true,
                ),
            ]),
            false,
            $closerClass,
            null,
        ];

        $closerClass = new class() {
            public function getField1(): string
            {
                return 'Eko';
            }

            public function getField2(): float
            {
                return 3;
            }
        };
        yield 'ALL(TRUE) { [ATTRIBUTES](2) } ==> true | Ex: true' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closerClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\get_class($closerClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                        ),
                    ]),
                    Condition::IS_TRUE
                ),
            ]),
            true,
            $closerClass,
        ];

        $closerClass = new class() {
            public function getField1(): string
            {
                return 'Eko';
            }

            public function getField2(): float
            {
                return 3;
            }
        };
        yield 'Double ALL(TRUE) { [ATTRIBUTES](2) } ==> true | Ex: true' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closerClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\get_class($closerClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                        ),
                    ]),
                    Condition::IS_TRUE
                ),
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closerClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\get_class($closerClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                        ),
                    ]),
                    Condition::IS_TRUE
                ),
            ]),
            true,
            $closerClass,
        ];

        $closeClass = new class() {
            public function getField1(): string
            {
                return 'EkoOlo';
            }

            public function getField2(): float
            {
                return 55;
            }
        };
        yield 'ALL(FALSE) { [ATTRIBUTES](2) } ==> true | Ex: false' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closeClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new NumberConditionAttribute(\get_class($closeClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                        ),
                    ]),
                    Condition::IS_TRUE
                ),
            ]),
            false,
            $closeClass,
        ];

        $closeClass = new class() {
            public function getField1(): string
            {
                return 'Eko';
            }

            public function getField2(): float
            {
                return 3;
            }
        };
        yield 'ALL(TRUE) { [ATTRIBUTES](1), [ANY](FALSE) { [ATTRIBUTES](2) } } ==> true | Ex: true' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closeClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ANY,
                            2,
                            null,
                            new CollectionCondition([
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new StringConditionAttribute(\get_class($closeClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'EkoHolo')
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\get_class($closeClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                                ),
                            ]),
                            Condition::IS_FALSE
                        ),
                    ]),
                    Condition::IS_TRUE
                ),
            ]),
            true,
            $closeClass,
        ];

        $closeClass =  new class() {
            public function getField1(): string
            {
                return 'Eko';
            }

            public function getField2(): float
            {
                return 3;
            }
        };

        yield 'ALL(TRUE) { [ATTRIBUTES](1), [ANY](FALSE) { [ATTRIBUTES](2), ALL(TRUE) - Repeat } } ==> true | Ex: true' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ALL,
                    1,
                    null,
                    new CollectionCondition([
                        new Condition(
                            Condition::TYPE_ATTRIBUTE,
                            1,
                            new StringConditionAttribute(\get_class($closeClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                        ),
                        new Condition(
                            Condition::TYPE_ANY,
                            2,
                            null,
                            new CollectionCondition([
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new StringConditionAttribute(\get_class($closeClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'EkoHolo')
                                ),
                                new Condition(
                                    Condition::TYPE_ATTRIBUTE,
                                    1,
                                    new NumberConditionAttribute(\get_class($closeClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                                ),
                                new Condition(
                                    Condition::TYPE_ALL,
                                    1,
                                    null,
                                    new CollectionCondition([
                                        new Condition(
                                            Condition::TYPE_ATTRIBUTE,
                                            1,
                                            new StringConditionAttribute(\get_class($closeClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'Eko')
                                        ),
                                        new Condition(
                                            Condition::TYPE_ANY,
                                            2,
                                            null,
                                            new CollectionCondition([
                                                new Condition(
                                                    Condition::TYPE_ATTRIBUTE,
                                                    1,
                                                    new StringConditionAttribute(\get_class($closeClass), 'field1', StringConditionAttribute::OPERATOR_EQ, 'EkoHolo')
                                                ),
                                                new Condition(
                                                    Condition::TYPE_ATTRIBUTE,
                                                    1,
                                                    new NumberConditionAttribute(\get_class($closeClass), 'field2', NumberConditionAttribute::OPERATOR_EQ, 3)
                                                ),
                                            ]),
                                            Condition::IS_FALSE
                                        ),
                                    ]),
                                    Condition::IS_TRUE
                                ),
                            ]),
                            Condition::IS_FALSE
                        ),
                    ]),
                    Condition::IS_TRUE
                ),
            ]),
            true,
            $closeClass,
        ];
    }

    public function dataExceptionConditions(): iterable
    {
        $classCloser = new class() {};
        yield 'Exception: MethodDoesNotExistException' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ATTRIBUTE,
                    1,
                    new StringConditionAttribute(\get_class($classCloser), 'fieldOlo', StringConditionAttribute::OPERATOR_CONTAINS, 'hi')
                ),
            ]),
            false,
            $classCloser,
            MethodDoesNotExistException::class,
        ];

        $classCloser = new class() {
            public function getFieldOlo(): array
            {
                return [];
            }
        };
        yield 'Exception: KeyNotFoundInArrayConditionException' => [
            new CollectionCondition([
                new Condition(
                    Condition::TYPE_ATTRIBUTE,
                    1,
                    new StringConditionAttribute(\get_class($classCloser), 'fieldOlo', StringConditionAttribute::OPERATOR_CONTAINS, 'hi')
                ),
            ]),
            false,
            [],
            KeyNotFoundInArrayConditionException::class,
        ];
    }
}
