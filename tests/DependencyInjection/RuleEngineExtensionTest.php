<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\DependencyInjection;

use DrinksIt\RuleEngineBundle\DependencyInjection\RuleEngineExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RuleEngineExtensionTest extends TestCase
{
    private $containerBuilder;

    private $extension;

    public function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);
        $this->extension = new RuleEngineExtension();
    }

    public function testLoad(): void
    {
        $configs = [];
        $this->containerBuilder->expects($this->any())->method('setParameter');
        $this->containerBuilder->expects($this->once())->method('getParameter')
            ->willReturnCallback(function ($name) {
                if ($name === 'rule_engine.mapping') {
                    return [];
                }

                return null;
            });

        $mockChildDefinition = $this->createMock(ChildDefinition::class);
        $this->containerBuilder->expects($this->any())->method('registerForAutoconfiguration')
            ->with($this->isType('string'))->willReturn($mockChildDefinition);

        $mockChildDefinition->expects($this->any())->method('addTag')->with(
            $this->stringStartsWith('rule_engine.'),
            $this->isType('array')
        );

        $this->extension->load($configs, $this->containerBuilder);
    }
}
