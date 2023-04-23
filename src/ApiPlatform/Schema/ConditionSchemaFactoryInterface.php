<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\Schema;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;

interface ConditionSchemaFactoryInterface
{
    public function buildSchemaForCondition(SchemaFactoryInterface $schemaFactory, Schema $schema, array $schemaContext = []): Schema;
}
