<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RuleEngineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('rule_engine.collection_condition_class', $config['collection_condition_class']);
        $container->setParameter('rule_engine.mapping', $config['mapping'] ?? []);

        $this->addAnnotatedClassesToCompile($container->getParameter('rule_engine.mapping'));

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('maker.xsd');
        $loader->load('extractor.xsd');
        $loader->load('metadata.xsd');
    }
}
