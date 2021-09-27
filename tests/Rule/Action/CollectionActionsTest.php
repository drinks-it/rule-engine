<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Rule\Action;

use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions;
use DrinksIt\RuleEngineBundle\Rule\Exception\ClassDoesNotImplementInterfaceRuleException;
use DrinksIt\RuleEngineBundle\Rule\Exception\TypeArgumentRuleException;
use PHPUnit\Framework\TestCase;

class CollectionActionsTest extends TestCase
{
    public function testValidActions(): void
    {
        $action = $this->createMock(ActionInterface::class);
        $action->expects($this->once())->method('getResourceClass')->willReturn(\stdClass::class);
        $action->expects($this->once())->method('executeAction')->with($this->isType('object'));
        $collection = new CollectionActions([
            $action,
        ]);

        $collection->execute(new \stdClass());
    }

    public function testValidActionsExceptionNotObject(): void
    {
        $this->expectException(TypeArgumentRuleException::class);
        $action = $this->createMock(ActionInterface::class);
        $action->expects($this->never())->method('executeAction')->with($this->isType('object'));
        $collection = new CollectionActions([
            $action,
        ]);

        $collection->execute([]);
    }

    public function testValidActionsNotActionInterface(): void
    {
        $this->expectException(ClassDoesNotImplementInterfaceRuleException::class);
        $action = $this->createMock(\IteratorAggregate::class);

        $collection = new CollectionActions([
            $action,
        ]);

        $collection->execute(new \stdClass());
    }
}
