<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\DependencyInjection;

use DrinksIt\RuleEngineBundle\Doctrine\RuleEntityFinderExtensionInterface;
use DrinksIt\RuleEngineBundle\Event\RuleEventInterface;
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
        $container->setParameter('rule_engine.collection_actions_class', $config['collection_actions_class']);
        $container->setParameter('rule_engine.mapping', $config['mapping'] ?? []);

        $this->addAnnotatedClassesToCompile($container->getParameter('rule_engine.mapping'));

        $container->registerForAutoconfiguration(RuleEventInterface::class)
            ->addTag('rule_engine.tag.event');

        $container->registerForAutoconfiguration(RuleEntityFinderExtensionInterface::class)
            ->addTag('rule_engine.tag.doctrine.extension_finder');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('maker.xsd');
        $loader->load('extractor.xsd');
        $loader->load('metadata.xsd');
        $loader->load('doctrine.xsd');
        $loader->load('events.xsd');
        $loader->load('serializer.xsd');
    }

    public function getAlias()
    {
        return 'rule_engine';
    }
}
