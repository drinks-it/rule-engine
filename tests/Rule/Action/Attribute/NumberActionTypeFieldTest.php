<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Action\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\NumberActionType;
use DrinksIt\RuleEngineBundle\Serializer\NormalizerPropertyInterface;
use PHPUnit\Framework\TestCase;

class NumberActionTypeFieldTest extends TestCase
{
    /**
     *
     * @dataProvider dataProviderDecodeDataBasicOperation
     * @dataProvider dataProviderDecodeDataBasicLevel1Operation
     * @dataProvider dataProviderDecodeDataBasicLevel2WithSpecialSymbolOperation
     * @dataProvider dataProviderDecodeDataCombineOperations
     * @dataProvider dataProviderWithMacrosObjects
     * @param mixed $objectEntity
     * @param mixed $resultExecute
     */
    public function testDecode(string $inputToParse, $objectEntity, $resultExecute): void
    {
        $actionNumber = new NumberActionType('field', \stdClass::class);

        $normalizer = $this->createMock(NormalizerPropertyInterface::class);
        $normalizer->method('normalize')->willReturnArgument(0);

        $actionNumber->setNormalizer($normalizer);

        $decodedOperations = $actionNumber->decodeAction($inputToParse);
        $decodedOperations->executeAction($objectEntity);
        $this->assertEquals($resultExecute, $objectEntity->getField());
    }

    private function makeEntityObject()
    {
        return new class() {
            private $field;

            public function getField()
            {
                return $this->field;
            }

            public function setField($newValue)
            {
                $this->field = $newValue;

                return $this;
            }
        };
    }

    public function dataProviderDecodeDataBasicOperation(): iterable
    {
        $enObject = $this->makeEntityObject();

        yield 'basic plus' => [
            '1 + 1',
            $enObject,
            2,
        ];

        yield 'basic minus' => [
            '1 - 1',
            $enObject,
            0,
        ];

        yield 'basic of v2' => [
            '1 * 1',
            $enObject,
            1,
        ];

        yield 'basic division' => [
            '1 / 1',
            $enObject,
            1,
        ];
    }

    public function dataProviderDecodeDataBasicLevel1Operation(): iterable
    {
        $enObject = $this->makeEntityObject();

        yield 'L1: basic plus' => [
            '1 + 1 + 2',
            $enObject,
            4,
        ];

        yield 'L1: basic minus' => [
            '12 - 1 - 1',
            $enObject,
            10,
        ];

        yield 'L1: basic of v2' => [
            '1 * 1 * 20',
            $enObject,
            20,
        ];

        yield 'L1: basic division' => [
            '100 / 10 / 2',
            $enObject,
            5,
        ];
    }

    public function dataProviderDecodeDataBasicLevel2WithSpecialSymbolOperation(): iterable
    {
        $enObject = $this->makeEntityObject();

        yield 'L2: basic plus' => [
            '1 + (1 + 2)',
            $enObject,
            4,
        ];

        yield 'L2: basic minus' => [
            '12 - (1 - 1)',
            $enObject,
            12,
        ];

        yield 'L2: basic of v2' => [
            '1 * (1 * 20)',
            $enObject,
            20,
        ];

        yield 'L2: basic division' => [
            '100 / (10 / 2)',
            $enObject,
            20,
        ];
    }

    public function dataProviderDecodeDataCombineOperations(): iterable
    {
        $enObject = $this->makeEntityObject();

        yield 'C: Minus | Plus ' => [
            '1 + 20 - 22',
            $enObject,
            -1,
        ];

        yield 'C: Minus | Plus. #2' => [
            '1 - (1.34 + 22)',
            $enObject,
            (1 - (1.34 + 22)),
        ];

        yield 'C: of | Plus' => [
            '1 - (1.34 * 22)',
            $enObject,
            (1 - (1.34 * 22)),
        ];

        yield 'C: of | Plus | Minus' => [
            '1 - (1.34 * 22) - 877',
            $enObject,
            (1 - (1.34 * 22) - 877),
        ];

        yield 'C: of | Plus | division | Minus' => [
            '1 - (1.34 * 22 / 34) - 877',
            $enObject,
            (1 - (1.34 * 22 / 34) - 877),
        ];

        yield 'Scanavi 1.605' => [
            '((2.75 / 1.1 + 3 * (1 / 3))/ ( 2.5 - (0.4 * 3 * (1 / 3)) ) / (5 / 7)) - ((2 * (1 / 6) + 4.5) + 0.375) / ( 2.75 - (1 * ( 1 / 2 )) )',
            $enObject,
            0.01851851851,
        ];
    }

    public function dataProviderWithMacrosObjects()
    {
        $enObject = function () {
            return new class() {
                private $field = 564;

                private $field_get = 3;

                public function getField()
                {
                    return $this->field;
                }

                public function setField($newValue)
                {
                    $this->field = $newValue;

                    return $this;
                }

                public function getFieldGet()
                {
                    return $this->field_get;
                }

                public function getRelationField()
                {
                    return new class() {
                        private $foo = 5;

                        public function getFoo()
                        {
                            return $this->foo;
                        }
                    };
                }
            };
        };

        yield 'Object macros. plus' => [
            '1 + %fieldGet% + %relationField.foo%',
            $enObject(),
            9,
        ];

        yield 'Object macros. self' => [
            '143 * %self% + %field%', // 143 + field + field
            $enObject(),
            143 * 564 + 564,
        ];
    }
}
