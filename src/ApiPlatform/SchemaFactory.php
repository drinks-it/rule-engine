<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;

final class SchemaFactory implements SchemaFactoryInterface
{
    private SchemaFactoryInterface $decoder;

    public function __construct(SchemaFactoryInterface $schemaFactory)
    {
        $this->decoder = $schemaFactory;
    }

    /**
     * @inheritDoc
     */
    public function buildSchema(string $className, string $format = 'json', string $type = Schema::TYPE_OUTPUT, ?string $operationType = null, ?string $operationName = null, ?Schema $schema = null, ?array $serializerContext = null, bool $forceCollection = false): Schema
    {
        return $this->decoder->buildSchema($className, $format, $type, $operationType, $operationName, $schema, $serializerContext);
    }
}
