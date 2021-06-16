<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Event;

use DrinksIt\RuleEngineBundle\Doctrine\RuleEntityFinderInterface;
use DrinksIt\RuleEngineBundle\Event\Exception\ItemObservedEntityNotObjectException;
use DrinksIt\RuleEngineBundle\Event\Factory\RuleEventListenerFactory;
use DrinksIt\RuleEngineBundle\Event\ObserverEntityEventInterface;
use DrinksIt\RuleEngineBundle\Event\RuleEvent;
use DrinksIt\RuleEngineBundle\Event\RuleEventInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\RuleEntityInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class RuleEventTest extends TestCase
{
    public function testEvent(): void
    {
        $callableCheckObjectOnEvent = fn ($object) => $this->assertEquals('yes', $object->assertCheckCall());

        $listerFactory = $this->createMock(RuleEventListenerFactory::class);
        $listerFactory->expects($this->exactly(2))->method('create')->willReturn([
            [
                'resource' => 'TestRuleEventNameObject',
                'name' => 'NameObject',
                'event' => new class($callableCheckObjectOnEvent) implements RuleEventInterface {
                    private $testCase;
                    public function __construct(callable $testCase)
                    {
                        $this->testCase = $testCase;
                    }

                    public function onEvent(iterable $data): void
                    {
                        foreach ($data as $object) {
                            \call_user_func($this->testCase, $object);
                        }
                    }

                    public function getName(): string
                    {
                        return 'NameObject';
                    }
                },
            ],
        ]);

        $conditions = $this->createMock(CollectionCondition::class);
        $conditions->expects($this->once())->method('isMatched')
            ->with($this->isType('object'))->willReturn(true);

        $actions = $this->createMock(CollectionActions::class);
        $actions->expects($this->once())->method('execute')->with($this->isType('object'));

        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
        $ruleEntityInterface->method('getConditions')->willReturn($conditions);

        $ruleEntityInterface->method('getAction')->willReturn($actions);
        $ruleEntityInterface->expects($this->once())->method('getStopRuleProcessing')
            ->willReturn(true);

        $ruleEntityInterface->method('getName')->willReturn('NameRule');

        $finderInterface = $this->createMock(RuleEntityFinderInterface::class);
        $finderInterface->expects($this->exactly(2))->method('getRulesByEventName')
            ->with($this->equalTo('TestRuleEventNameObject'))
            ->willReturn([
                $ruleEntityInterface,
            ]);

        $loggerInterface = $this->createMock(LoggerInterface::class);
        $loggerInterface->expects($this->any())
            ->method('debug')->with($this->isType('string'));

        $ruleEvent = new RuleEvent($listerFactory, $finderInterface, $loggerInterface);

        $observerEntity = new class() implements ObserverEntityEventInterface {
            public function getObservedEntities(): iterable
            {
                yield new class() {
                    public function assertCheckCall()
                    {
                        return 'yes';
                    }
                };
            }

            public function getClassNameRuleEventInterface(): string
            {
                return 'TestRuleEventNameObject';
            }
        };

        $ruleEvent($observerEntity);

        $this->expectException(ItemObservedEntityNotObjectException::class);
        $observerEntity = new class() implements ObserverEntityEventInterface {
            public function getObservedEntities(): iterable
            {
                yield [];
            }

            public function getClassNameRuleEventInterface(): string
            {
                return 'TestRuleEventNameObject';
            }
        };

        $ruleEvent($observerEntity);
    }

    public function testEventNotEventType(): void
    {
        $callableCheckObjectOnEvent = fn ($object) => $this->assertEquals('yes', $object->assertCheckCall());

        $listerFactory = $this->createMock(RuleEventListenerFactory::class);
        $listerFactory->expects($this->once())->method('create')->willReturn([
            [
                'resource' => 'TestRuleEventNameObject',
                'name' => 'NameObject',
                'event' => new class($callableCheckObjectOnEvent) {
                    private $testCase;
                    public function __construct(callable $testCase)
                    {
                        $this->testCase = $testCase;
                    }

                    public function onEvent(iterable $data): void
                    {
                        foreach ($data as $object) {
                            \call_user_func($this->testCase, $object);
                        }
                    }

                    public function getName(): string
                    {
                        return 'NameObject';
                    }
                },
            ],
        ]);

        $conditions = $this->createMock(CollectionCondition::class);
        $conditions->expects($this->never())->method('isMatched');

        $actions = $this->createMock(CollectionActions::class);
        $actions->expects($this->never())->method('execute');

        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
        $ruleEntityInterface->expects($this->never())->method('getConditions');

        $ruleEntityInterface->method('getAction')->willReturn($actions);
        $ruleEntityInterface->expects($this->never())->method('getStopRuleProcessing');

        $ruleEntityInterface->expects($this->never())->method('getName')->willReturn('NameRule');

        $finderInterface = $this->createMock(RuleEntityFinderInterface::class);
        $finderInterface->expects($this->never())->method('getRulesByEventName');

        $loggerInterface = $this->createMock(LoggerInterface::class);
        $loggerInterface->expects($this->any())
            ->method('debug')->with($this->isType('string'));

        $ruleEvent = new RuleEvent($listerFactory, $finderInterface, $loggerInterface);

        $observerEntity = new class() implements ObserverEntityEventInterface {
            public function getObservedEntities(): iterable
            {
                yield new class() {
                    public function assertCheckCall()
                    {
                        return 'yes';
                    }
                };
            }

            public function getClassNameRuleEventInterface(): string
            {
                return 'TestRuleEventNameObject';
            }
        };

        $ruleEvent($observerEntity);
    }

    public function testEventNotMatch(): void
    {
        $callableCheckObjectOnEvent = fn ($object) => $this->assertEquals('yes', $object->assertCheckCall());

        $listerFactory = $this->createMock(RuleEventListenerFactory::class);
        $listerFactory->expects($this->exactly(2))->method('create')->willReturn([
            [
                'resource' => 'TestRuleEventNameObject',
                'name' => 'NameObject',
                'event' => new class($callableCheckObjectOnEvent) implements RuleEventInterface {
                    private $testCase;
                    public function __construct(callable $testCase)
                    {
                        $this->testCase = $testCase;
                    }

                    public function onEvent(iterable $data): void
                    {
                        foreach ($data as $object) {
                            \call_user_func($this->testCase, $object);
                        }
                    }

                    public function getName(): string
                    {
                        return 'NameObject';
                    }
                },
            ],
        ]);

        $conditions = $this->createMock(CollectionCondition::class);
        $conditions->expects($this->once())->method('isMatched')
            ->with($this->isType('object'))->willReturn(false);

        $actions = $this->createMock(CollectionActions::class);
        $actions->expects($this->never())->method('execute');

        $ruleEntityInterface = $this->createMock(RuleEntityInterface::class);
        $ruleEntityInterface->expects($this->once())->method('getConditions')->willReturn($conditions);

        $ruleEntityInterface->method('getAction')->willReturn($actions);
        $ruleEntityInterface->expects($this->never())->method('getStopRuleProcessing');

        $ruleEntityInterface->expects($this->never())->method('getName')->willReturn('NameRule');

        $finderInterface = $this->createMock(RuleEntityFinderInterface::class);
        $finderInterface->expects($this->exactly(2))->method('getRulesByEventName')
            ->with($this->equalTo('TestRuleEventNameObject'))
            ->willReturn([
                $ruleEntityInterface,
            ]);

        $loggerInterface = $this->createMock(LoggerInterface::class);
        $loggerInterface->expects($this->any())
            ->method('debug')->with($this->isType('string'));

        $ruleEvent = new RuleEvent($listerFactory, $finderInterface, $loggerInterface);

        $observerEntity = new class() implements ObserverEntityEventInterface {
            public function getObservedEntities(): iterable
            {
                yield new class() {
                    public function assertCheckCall()
                    {
                        return 'yes';
                    }
                };
            }

            public function getClassNameRuleEventInterface(): string
            {
                return 'TestRuleEventNameObject';
            }
        };

        $ruleEvent($observerEntity);

        $this->expectException(ItemObservedEntityNotObjectException::class);
        $observerEntity = new class() implements ObserverEntityEventInterface {
            public function getObservedEntities(): iterable
            {
                yield [];
            }

            public function getClassNameRuleEventInterface(): string
            {
                return 'TestRuleEventNameObject';
            }
        };

        $ruleEvent($observerEntity);
    }
}
