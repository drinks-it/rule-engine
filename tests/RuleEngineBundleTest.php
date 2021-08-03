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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RuleEngineBundleTest extends TestCase
{
    public function testBundle(): void
    {
        $ruleEngineBundle = new RuleEngineBundle();
        $this->assertEquals(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtensionClass());

        $this->assertInstanceOf(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtension());

        $container =  $this->createMock(ContainerBuilder::class);
        $container->expects($this->any())->method('hasDefinition')->with(
            $this->isType('string')
        )->willReturn(true);

        $container->expects($this->any())->method('addCompilerPass')->with(
            $this->isInstanceOf(CompilerPassInterface::class)
        )->willReturnCallback(function (CompilerPassInterface $service) use ($container) {
            $service->process($container);

            return $container;
        });

        $ruleEngineBundle->build(
            $container
        );
    }

    public function testNeverBundle(): void
    {
        $ruleEngineBundle = new RuleEngineBundle();
        $this->assertEquals(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtensionClass());

        $this->assertInstanceOf(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtension());

        $container =  $this->createMock(ContainerBuilder::class);
        $container->expects($this->any())->method('hasDefinition')->with(
            $this->isType('string')
        )->willReturn(false);

        $container->expects($this->any())->method('addCompilerPass')->with(
            $this->isInstanceOf(CompilerPassInterface::class)
        )->willReturnCallback(function (CompilerPassInterface $service) use ($container) {
            $service->process($container);

            return $container;
        });

        $ruleEngineBundle->build(
            $container
        );
    }
}
