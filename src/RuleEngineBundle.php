<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle;

use DrinksIt\RuleEngineBundle\DependencyInjection\Compiler\ApiPlatformService;
use DrinksIt\RuleEngineBundle\DependencyInjection\RuleEngineExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RuleEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new ApiPlatformService());
    }

    public function getContainerExtensionClass(): string
    {
        return RuleEngineExtension::class;
    }
}
