<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle;

use DrinksIt\RuleEngineBundle\DependencyInjection\Compiler\ApiPlatformService;
use DrinksIt\RuleEngineBundle\DependencyInjection\RuleEngineExtension;
use DrinksIt\RuleEngineBundle\RuleEngineBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RuleEngineBundleTest extends TestCase
{
    public function testBundle(): void
    {
        $ruleEngineBundle = new RuleEngineBundle();
        $this->assertEquals(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtensionClass());

        $this->assertInstanceOf(RuleEngineExtension::class, $ruleEngineBundle->getContainerExtension());

        $container =  $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())->method('hasDefinition')->with(
            $this->equalTo('api_platform.data_persister')
        )->willReturn(true);

        $container->expects($this->once())->method('addCompilerPass')->with(
            $this->isInstanceOf(ApiPlatformService::class)
        )->willReturnCallback(function (ApiPlatformService $apiPlatformService) use ($container) {
            $apiPlatformService->process($container);

            return $container;
        });

        $ruleEngineBundle->build(
            $container
        );
    }
}
