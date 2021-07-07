<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\DependencyInjection\Compiler;

use DrinksIt\RuleEngineBundle\DependencyInjection\ApiPlatformServiceExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiPlatformService implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('api_platform.data_persister')) {
            $container->loadFromExtension(ApiPlatformServiceExtension::class);
        }
    }
}
