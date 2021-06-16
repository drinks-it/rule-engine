<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\DependencyInjection;

use DrinksIt\RuleEngineBundle\Rule\Action\CollectionActions;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $configurationBuilder = new TreeBuilder('rule_engine');

        $configurationBuilder->getRootNode()
            ->children()
            ->scalarNode('collection_condition_class')->defaultValue(CollectionCondition::class)->end()
            ->scalarNode('collection_actions_class')->defaultValue(CollectionActions::class)->end()
            ->arrayNode('mapping')->scalarPrototype()->defaultValue(['%kernel.project_dir%/src/Entity'])->end()
            ->end();

        return $configurationBuilder;
    }
}
