<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Event;

use DrinksIt\RuleEngineBundle\Doctrine\RuleEntityFinderInterface;
use DrinksIt\RuleEngineBundle\Event\Exception\ItemObservedEntityNotObjectException;
use DrinksIt\RuleEngineBundle\Event\Factory\RuleEventListenerFactoryInterface;
use Psr\Log\LoggerInterface;

final class RuleEvent
{
    private RuleEventListenerFactoryInterface $eventListenerFactory;

    private RuleEntityFinderInterface $ruleEntityFinder;

    private LoggerInterface $logger;

    public const EVENT = 'rule_event';

    public function __construct(
        RuleEventListenerFactoryInterface $eventListenerFactory,
        RuleEntityFinderInterface $ruleEntityFinder,
        LoggerInterface $logger
    ) {
        $this->eventListenerFactory = $eventListenerFactory;
        $this->ruleEntityFinder = $ruleEntityFinder;
        $this->logger = $logger;
    }

    public function __invoke(ObserverEntityEventInterface $observerEntityEvent): void
    {
        $clientRuleEvent = null;
        foreach ($this->eventListenerFactory->create() as $ruleEvent) {
            if ($ruleEvent['resource'] === $observerEntityEvent->getClassNameRuleEventInterface()) {
                $this->logger->debug(sprintf('Find event `%s`', $ruleEvent['resource']));
                $clientRuleEvent = $ruleEvent['event'];

                break;
            }
        }

        if (!$clientRuleEvent instanceof RuleEventInterface) {
            return;
        }

        $ruleEntitiesMatched = $this->ruleEntityFinder->getRulesByEventName(
            $observerEntityEvent->getClassNameRuleEventInterface()
        );

        foreach ($observerEntityEvent->getObservedEntities() as $observedEntity) {
            if (!\is_object($observedEntity)) {
                throw new ItemObservedEntityNotObjectException();
            }

            foreach ($ruleEntitiesMatched as $ruleEntity) {
                if (!$ruleEntity->getConditions()->isMatched($observedEntity)) {
                    continue;
                }

                $ruleEntity->getAction()->execute($observedEntity);
                $this->logger->debug(sprintf('Complete Rule (%d, %s)', $ruleEntity->getId(), $ruleEntity->getName()));

                if ($ruleEntity->getStopRuleProcessing()) {
                    break;
                }
            }
        }

        $clientRuleEvent->onEvent($observerEntityEvent->getObservedEntities());
    }
}
