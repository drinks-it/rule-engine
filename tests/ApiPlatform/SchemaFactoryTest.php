<?php

/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2026 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace Tests\DrinksIt\RuleEngineBundle\ApiPlatform;

use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Metadata\GetCollection;
use DrinksIt\RuleEngineBundle\ApiPlatform\Schema\ActionSchemaFactoryInterface;
use DrinksIt\RuleEngineBundle\ApiPlatform\Schema\ConditionSchemaFactoryInterface;
use DrinksIt\RuleEngineBundle\ApiPlatform\SchemaFactory;
use PHPUnit\Framework\TestCase;

class SchemaFactoryTest extends TestCase
{
    public function testForwardsCollectionAndSerializerContext(): void
    {
        $schema = new Schema();
        $operation = new GetCollection();
        $context = ['groups' => ['rule:read']];
        $inner = $this->createMock(SchemaFactoryInterface::class);
        $inner->expects($this->once())->method('buildSchema')
            ->with(\stdClass::class, 'json', Schema::TYPE_OUTPUT, $operation, $schema, $context, true)
            ->willReturn($schema);

        $factory = new SchemaFactory(
            $inner,
            $this->createMock(ConditionSchemaFactoryInterface::class),
            $this->createMock(ActionSchemaFactoryInterface::class)
        );
        $this->assertSame($schema, $factory->buildSchema(\stdClass::class, 'json', Schema::TYPE_OUTPUT, $operation, $schema, $context, true));
    }
}
