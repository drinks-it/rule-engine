<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\Schema;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;

final class ActionSchemaFactory implements ActionSchemaFactoryInterface
{
    public function buildSchemaForAction(SchemaFactoryInterface $schemaFactory, Schema $schema, array $schemaContext = []): Schema
    {
        $newDefinitions = $schema->getDefinitions();
        $newDefinitions->offsetSet(
            $schema->getRootDefinitionKey(),
            new \ArrayObject([
                'type'       => 'object',
                'required'   => [
                    'type',
                    'field',
                    'action',
                ],
                'properties' => [
                    'type' => [
                        'type' => 'string',
                    ],
                    'field' => [
                        'type'        => 'string',
                        'description' => 'Field to change value',
                    ],
                    'action' => [
                        'type'        => 'string',
                        'description' => 'Execute action',
                    ],
                ],
                'example' => [
                    'type'   => 'number',
                    'field'  => 'price',
                    'action' => '(%tax% + %productPrice.price%) * 0.45',
                ],
            ])
        );

        $schema->setDefinitions($newDefinitions);

        return $schema;
    }
}
