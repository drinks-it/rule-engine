<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\Event\Factory;

use DrinksIt\RuleEngineBundle\Event\Factory\RuleEventListenerFactory;
use DrinksIt\RuleEngineBundle\Event\RuleEventInterface;
use PHPUnit\Framework\TestCase;

class RuleEventListenerFactoryTest extends TestCase
{
    public function testCreateWithWrongInterfaces(): void
    {
        $handlerTagRuleEvents = function (): iterable {
            yield new class () {};
            yield new class () implements RuleEventInterface {
                public function onEvent(iterable $data): void
                {
                }

                public function getName(): string
                {
                    return 'CheckEventString';
                }
            };
        };

        $RuleEventFactory = new RuleEventListenerFactory($handlerTagRuleEvents());
        $resultFactory = $RuleEventFactory->create();
        $this->assertCount(1, $resultFactory);

        foreach ($resultFactory as $ruleEvent) {
            $this->assertArrayHasKey('event', $ruleEvent);
            $this->assertArrayHasKey('name', $ruleEvent);
            $this->assertInstanceOf(RuleEventInterface::class, $ruleEvent['event']);
            $this->assertArrayHasKey('resource', $ruleEvent);
            $this->assertIsString($ruleEvent['resource']);
        }
    }
}
