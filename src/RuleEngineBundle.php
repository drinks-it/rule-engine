<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle;

use DrinksIt\RuleEngineBundle\DependencyInjection\RuleEngineExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RuleEngineBundle extends Bundle
{
    public function getContainerExtensionClass()
    {
        return RuleEngineExtension::class;
    }
}
