<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle;

use DrinksIt\RuleEngineBundle\DependencyInjection\RuleEngineExtension;
use DrinksIt\RuleEngineBundle\RuleEngineBundle;
use PHPUnit\Framework\TestCase;

class RuleEngineBundleTest extends TestCase
{
    public function testBundle(): void
    {
        $ruleEngineBundle = new RuleEngineBundle();
        $this->assertEquals(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtensionClass());

        $this->assertInstanceOf(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtension());
    }
}
