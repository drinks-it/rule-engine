<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\ArrayConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\NotSupportedOperatorConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\ArrayAttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class ArrayConditionAttributeTest extends TestCase
{
    /**
     * @param $operator
     * @param $valueSet
     * @param $valueMatch
     * @param bool $isEq
     * @param string|null $exceptionClass
     *
     * @dataProvider dataCasesArrayCondition
     */
    public function testMatchArrayConditions($operator, $valueSet, $valueMatch, bool $isEq = false, string $exceptionClass = null): void
    {
        if ($exceptionClass) {
            $this->expectException($exceptionClass);
        }
        $conditionArray = new ArrayConditionAttribute(\stdClass::class, 'fieldArray');
        $this->assertInstanceOf(ArrayAttributeConditionTypeInterface::class, $conditionArray);

        $this->assertIsString($conditionArray->getType());
        $conditionArray->setValue($valueSet)->setOperator($operator);
        $this->assertEquals($isEq, $conditionArray->match($valueMatch));
    }

    public function dataCasesArrayCondition(): array
    {
        $std = new \stdClass();

        return [
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                [1, 2, 3, 4],
                [2, 3],
                true,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                [1, [], 2, 3],
                [[]],
                true,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                [1, 4, 2, 3],
                [[]],
                false,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                [1, 4, 2, 3],
                null,
                false,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                [1, 4, 2, 3],
                4,
                true,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                [1, 4, $std, 3],
                $std,
                true,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_EQ,
                [1, 2, 3, 4],
                [2, 3],
                false,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_EQ,
                [1, [], 2, 3],
                [[]],
                false,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_EQ,
                [1, 4, 2, 3],
                [[]],
                false,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_KEY_EXISTS,
                [1, 'test' => 4, 2, 3],
                'test',
                true,
            ],
            [
                ArrayAttributeConditionTypeInterface::OPERATOR_KEY_EXISTS,
                [1, 'test' => 4, 2, 3],
                [],
                true,
                NotSupportedOperatorConditionException::class,
            ],
        ];
    }
}
