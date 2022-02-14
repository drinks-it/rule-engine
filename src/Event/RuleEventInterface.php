<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Event;

interface RuleEventInterface
{
    public function onEvent(iterable $data);

    public function getName(): string;
}
