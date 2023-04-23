<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Doctrine\Serializer;

use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassDoesNotImplementInterfaceException;
use DrinksIt\RuleEngineBundle\Doctrine\Exception\ClassNotExistRuleEngineDoctrineException;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\DenormalizeAction;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\NormalizeAction;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\NumberActionType;
use DrinksIt\RuleEngineBundle\Rule\Action\Attribute\StringActionType;
use DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;
use PHPUnit\Framework\TestCase;

class SerializerActionsTest extends TestCase
{
    /**
     * @dataProvider dataProviderActionsObjects
     * @param CollectionActionsInterface $actions
     * @param string|null $exceptExceptionClass
     * @param callable|null $modifyArrayDecoded
     */
    public function testSerialize(
        CollectionActionsInterface $actions,
        string $exceptExceptionClass = null,
        callable $modifyArrayDecoded = null
    ): void {
        if ($exceptExceptionClass) {
            $this->expectException($exceptExceptionClass);
        }
        $denormalize = new DenormalizeAction($actions);
        $arrayDecoded = $denormalize->denormalizeCollection();

        $this->assertIsArray($arrayDecoded);
        $this->assertNotEmpty($arrayDecoded);

        if ($modifyArrayDecoded) {
            $arrayDecoded =  $modifyArrayDecoded($arrayDecoded);
        }

        $normalize = new NormalizeAction($arrayDecoded);
        $newCollectionConditions = $normalize->normalizeCollection();

        $this->assertEquals($actions, $newCollectionConditions);
    }

    public function dataProviderActionsObjects(): iterable
    {
        yield 'Collection Empty' => [
            new CollectionActions([
            ]),
        ];

        yield 'Collection Class not exist' => [
            new CollectionActions([
            ]),
            ClassNotExistRuleEngineDoctrineException::class,
            function (array $arraySerialize): array {
                $arraySerialize['class_collection_action'] = 'Nope';

                return $arraySerialize;
            },
        ];

        yield 'Collection Fill' => [
            new CollectionActions([
                new NumberActionType('field', 'App\Entity\Model', '1 + 1'),
                new StringActionType('fidlString', 'App\Entity\Model', 'Replace text'),
            ]),
        ];

        yield 'Exception Normalize class not found' => [
            new CollectionActions([
                new NumberActionType('field', 'App\Entity\Model', '1 + 1'),
                new StringActionType('fidlString', 'App\Entity\Model', 'Replace text'),
            ]),
            ClassNotExistRuleEngineDoctrineException::class,
            function (array $arraySerialize): array {
                $arraySerialize['elements'][0]['class_action'] = 'FakeClass';

                return $arraySerialize;
            },
        ];

        yield 'Exception Normalize class not implement interface' => [
            new CollectionActions([
                new NumberActionType('field', 'App\Entity\Model', '1 + 1'),
                new StringActionType('fidlString', 'App\Entity\Model', 'Replace text'),
            ]),
            ClassDoesNotImplementInterfaceException::class,
            function (array $arraySerialize): array {
                $arraySerialize['elements'][0]['class_action'] = \stdClass::class;

                return $arraySerialize;
            },
        ];
    }
}
