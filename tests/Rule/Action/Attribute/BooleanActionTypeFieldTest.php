<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Action\Attribute;

use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\BooleanActionType;
use DrinksIt\RuleEngineBundle\Serializer\NormalizerPropertyInterface;
use PHPUnit\Framework\TestCase;

class BooleanActionTypeFieldTest extends TestCase
{
    /**
    * @dataProvider dataProviderTestSetValues
     *
     * @param mixed $inputToParse
     * @param mixed $objectEntity
     * @param mixed $resultExecute
     */
    public function testDecode($inputToParse, $objectEntity, $resultExecute): void
    {
        $actionBoolean = new BooleanActionType('field', \stdClass::class);

        $normalizer = $this->createMock(NormalizerPropertyInterface::class);
        $normalizer->method('normalize')->willReturnArgument(0);

        $actionBoolean->setNormalizer($normalizer);

        $decodedOperations = $actionBoolean->decodeAction($inputToParse);
        $decodedOperations->executeAction($objectEntity);
        $this->assertEquals($resultExecute, $objectEntity->getField());
    }

    private function makeEntityObject()
    {
        return new class () {
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

    public function dataProviderTestSetValues(): array
    {
        return [
            ['true', $this->makeEntityObject(), true],
            ['false', $this->makeEntityObject(), false],
            [0, $this->makeEntityObject(), false],
            [1, $this->makeEntityObject(), true],
            [[], $this->makeEntityObject(), false],
            [null, $this->makeEntityObject(), false],
            ['', $this->makeEntityObject(), false],
        ];
    }
}
