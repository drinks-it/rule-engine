<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Condition\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Condition\Attribute\BooleanConditionAttribute;
use DrinksIt\RuleEngineBundle\Rule\Condition\Exception\TypeValueNotSupportedForConditionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\BooleanAttributeConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class BooleanConditionAttributeTest extends TestCase
{
    /**
     * @dataProvider dataCasesBooleanEq
     * @param mixed $valueSet
     * @param mixed $valueMatch
     * @param mixed $isEq
     * @param null|mixed $exceptionClass
     * @param mixed $operator
     */
    public function testMatchBooleanCondition($operator, $valueSet, $valueMatch, $isEq = false, $exceptionClass = null): void
    {
        $message = '';

        if ($exceptionClass) {
            $this->expectException($exceptionClass);
        } else {
            $message = sprintf('Case %s as %s', $valueSet, $valueMatch);
        }

        $booleanCondition = new BooleanConditionAttribute(\stdClass::class, 'anyFieldName');
        $this->assertInstanceOf(BooleanAttributeConditionTypeInterface::class, $booleanCondition);
        $this->assertIsString($booleanCondition->getType());
        $this->assertIsArray($booleanCondition->getSupportOperators());

        $booleanCondition->setValue($valueSet)->setOperator(
            $operator
        );

        $this->assertIsArray($booleanCondition->toArray());
        $this->assertEquals($isEq, $booleanCondition->match($valueMatch), $message);
    }

    public function dataCasesBooleanEq(): array
    {
        return [
            'true | false | !=' => [
                BooleanAttributeConditionTypeInterface::OPERATOR_NEQ,
                true,
                false,
                true,
                null,
            ],
            'false | false | ==' => [
                BooleanAttributeConditionTypeInterface::OPERATOR_EQ,
                false,
                false,
                true,
                null,
            ],
            'false | true | != ' => [
                BooleanAttributeConditionTypeInterface::OPERATOR_NEQ,
                false,
                true,
                true,
                null,
            ],
            '0 | 1 | !=' => [
                BooleanAttributeConditionTypeInterface::OPERATOR_NEQ,
                0,
                1,
                true,
                null,
            ],
            'a | b | Error' => [
                BooleanAttributeConditionTypeInterface::OPERATOR_EQ,
                'a',
                'b',
                false,
                TypeValueNotSupportedForConditionException::class,
            ],
            '1 | 2 | ==' => [
                BooleanAttributeConditionTypeInterface::OPERATOR_EQ,
                '1',
                '2',
                true,
                null,
            ],
        ];
    }
}
