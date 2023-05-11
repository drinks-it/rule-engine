<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ApiPlatformService implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (\class_exists('ApiPlatform\Symfony\Bundle\ApiPlatformBundle')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
            $loader->load('api_platform.xsd');
        }
    }
}
