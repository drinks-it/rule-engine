<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Action\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\StringActionType;
use PHPUnit\Framework\TestCase;

class StringActionTypeFieldTest extends TestCase
{
    /**
     * @param $inputToParse
     * @param $objectEntity
     * @param $resultExecute
     *
     * @dataProvider dataProviderTestSetValues
     */
    public function testDecode($inputToParse, $objectEntity, $resultExecute): void
    {
        $actionNumber = new StringActionType('field', \stdClass::class);

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

    public function dataProviderTestSetValues(): iterable
    {
        yield 'basic' => [
            'Pattern test',
            $this->makeEntityObject(),
            'Pattern test',
        ];

        $objEntityWithValues = $this->makeEntityObject();
        $objEntityWithValues->setField('Check Test');

        yield 'basic replace' => [
            'Pattern test',
            $objEntityWithValues,
            'Pattern test',
        ];

        $objEntityWithValues = $this->makeEntityObject();
        $objEntityWithValues->setField('Check Test');

        yield 'concat strings | End' => [
            'Pattern test %self%',
            $objEntityWithValues,
            'Pattern test Check Test',
        ];

        $objEntityWithValues = $this->makeEntityObject();
        $objEntityWithValues->setField('Check Test');

        yield 'concat strings | Begin' => [
            '%self% Pattern test',
            $objEntityWithValues,
            'Check Test Pattern test',
        ];

        $objEntityWithValues = $this->makeEntityObject();
        $objEntityWithValues->setField('Check Test');

        yield 'concat strings | Insert' => [
            'Pattern %self% test',
            $objEntityWithValues,
            'Pattern Check Test test',
        ];

        $relationCase = new class() {
            private $field = 'Olo';

            public function getField()
            {
                return $this->field;
            }

            public function setField($newValue)
            {
                $this->field = $newValue;

                return $this;
            }

            public function getRelationData()
            {
                return new class() {
                    public function getSupportData()
                    {
                        return 'Hello';
                    }
                };
            }
        };

        yield 'contact with relations' => [
            'Pattern %self% + %relationData.supportData%',
            $relationCase,
            'Pattern Olo + Hello',
        ];
    }
}
