<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\IntegerConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\IntegerAttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\NumberAttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class IntegerConditionAttributeTest extends TestCase
{
    /**
     * @param $operator
     * @param $valueSet
     * @param $valueMatch
     * @param bool $isEq
     * @param null $exceptionClass
     *
     * @dataProvider dataCasesNumbersEq
     * @dataProvider dataCasesNumbersGt
     * @dataProvider dataCasesNumbersGte
     * @dataProvider dataCasesNumbersLt
     * @dataProvider dataCasesNumbersLte
     */
    public function testMatchNumberCondition($operator, $valueSet, $valueMatch, bool $isEq = false, $exceptionClass = null): void
    {
        $message = '';

        if ($exceptionClass) {
            $this->expectException($exceptionClass);
        } else {
            $message = sprintf('%s: Case %s as %s', $operator, $valueSet, $valueMatch);
        }
        $numberCondition = new IntegerConditionAttribute(\stdClass::class, 'anyFieldName');
        $this->assertInstanceOf(IntegerAttributeConditionTypeInterface::class, $numberCondition);
        $this->assertIsString($numberCondition->getType());
        $this->assertIsArray($numberCondition->getSupportOperators());

        $numberCondition->setValue($valueSet)->setOperator($operator);

        $this->assertIsArray($numberCondition->toArray());
        $this->assertEquals($isEq, $numberCondition->match($valueMatch), $message);
    }

    public function dataCasesNumbersEq(): array
    {
        return [
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                34,
                34,
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                '34',
                '34',
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                '34.0',
                '34',
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                '34',
                '34.0',
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                '34.1',
                '34.0',
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                '34.1',
                '34.01',
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                34.1,
                34.01,
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                34.01,
                34.01,
                true,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                '34.99',
                35,
                false,
                null,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                new class() {},
                35,
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                false,
                35,
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                [],
                35,
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                35,
                new class() {},
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                35,
                false,
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_EQ,
                35,
                [],
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
        ];
    }

    public function dataCasesNumbersGt(): array
    {
        return array_merge([
            [
                NumberAttributeConditionTypeInterface::OPERATOR_GT,
                '0.0000001',
                '0.00000001',
                false,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_GT,
                '0.00000001',
                '0.0000001',
                false,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_GT,
                '0.9999998',
                '0.99999998',
                false,
            ],
        ], array_map(function ($item): array {
            [$operator, $valueSet, $valueMatch, $isEq, $exceptionClass] = $item;

            if (!$exceptionClass) {
                $isEq = (int)$valueSet > (int)$valueMatch;
            }

            return [
                NumberAttributeConditionTypeInterface::OPERATOR_GT,
                $valueSet,
                $valueMatch,
                $isEq,
                $exceptionClass,
            ];
        }, $this->dataCasesNumbersEq()));
    }

    public function dataCasesNumbersLt(): array
    {
        return array_merge([
            [
                NumberAttributeConditionTypeInterface::OPERATOR_LT,
                '0.0000001',
                '0.00000001',
                false,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_LT,
                '0.00000001',
                '0.0000001',
                false,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_LT,
                '0.9999998',
                '0.99999998',
                false,
            ],
        ], array_map(function ($item): array {
            [$operator, $valueSet, $valueMatch, $isEq, $exceptionClass] = $item;

            if (!$exceptionClass) {
                $isEq = (int)$valueSet < (int)$valueMatch;
            }

            return [
                NumberAttributeConditionTypeInterface::OPERATOR_LT,
                $valueSet,
                $valueMatch,
                $isEq,
                $exceptionClass,
            ];
        }, $this->dataCasesNumbersEq()));
    }

    public function dataCasesNumbersGte(): array
    {
        return array_merge([
            [
                NumberAttributeConditionTypeInterface::OPERATOR_GTE,
                '0.0000001',
                '0.00000001',
                true,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_GTE,
                '0.00000001',
                '0.0000001',
                true,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_GTE,
                '0.9999998',
                '0.99999998',
                true,
            ],
        ], array_map(function ($item): array {
            [$operator, $valueSet, $valueMatch, $isEq, $exceptionClass] = $item;

            if (!$exceptionClass) {
                $isEq = (int)$valueSet >= (int)$valueMatch;
            }

            return [
                NumberAttributeConditionTypeInterface::OPERATOR_GTE,
                $valueSet,
                $valueMatch,
                $isEq,
                $exceptionClass,
            ];
        }, $this->dataCasesNumbersEq()));
    }

    public function dataCasesNumbersLte(): array
    {
        return array_merge([
            [
                NumberAttributeConditionTypeInterface::OPERATOR_LTE,
                '0.0000001',
                '0.00000001',
                true,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_LTE,
                '0.00000001',
                '0.0000001',
                true,
            ],
            [
                NumberAttributeConditionTypeInterface::OPERATOR_LTE,
                '0.9999998',
                '0.99999998',
                true,
            ],
        ], array_map(function ($item): array {
            [$operator, $valueSet, $valueMatch, $isEq, $exceptionClass] = $item;

            if (!$exceptionClass) {
                $isEq = (int)$valueSet <= (int)$valueMatch;
            }

            return [
                NumberAttributeConditionTypeInterface::OPERATOR_LTE,
                $valueSet,
                $valueMatch,
                $isEq,
                $exceptionClass,
            ];
        }, $this->dataCasesNumbersEq()));
    }
}
