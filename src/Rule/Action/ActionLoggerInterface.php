<?php

namespace DrinksIt\RuleEngineBundle\Rule\Action;

use Psr\Log\LoggerInterface;

interface ActionLoggerInterface
{
    public function setLogger(LoggerInterface $logger): void;
}
