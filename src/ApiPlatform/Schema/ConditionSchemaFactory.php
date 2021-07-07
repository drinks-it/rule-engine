<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform\Schema;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\BooleanAttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\NumberAttributeConditionTypeInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Types\StringAttributeConditionTypeInterface;

final class ConditionSchemaFactory implements ConditionSchemaFactoryInterface
{
    public function buildSchemaForCondition(SchemaFactoryInterface $schemaFactory, Schema $schema, array $schemaContext = []): Schema
    {
        if (isset($schemaContext['condition_attribute'])) {
            $definitionName = 'Attribute'.$schema->getRootDefinitionKey();
            $ref = Schema::VERSION_OPENAPI === $schema->getVersion() ? '#/components/schemas/'.$definitionName : '#/definitions/'.$definitionName;

            $newDefinitions = $schema->getDefinitions();
            unset($newDefinitions[$schema->getRootDefinitionKey()]);

            $schema['$ref'] = $ref;

            $newDefinitions->offsetSet(
                $definitionName,
                new \ArrayObject([
                    'type' => 'object',
                    'required' => [
                        'type',
                        'field',
                        'operator',
                        'value',
                    ],
                    'properties' => [
                        'type' => [
                            'type'    => 'string',
                            'example' => Condition::TYPE_ATTRIBUTE,
                            'enum'    => [
                                Condition::TYPE_ATTRIBUTE,
                            ],
                        ],
                        'field'     => ['type' => 'string'],
                        'operator'  => ['type' => 'string'],
                        'value'     => ['type' => 'string'],
                    ],
                ])
            );

            $schema->setDefinitions($newDefinitions);

            return $schema;
        }

        $subAttributeCondition = $this->buildSchemaForCondition(
            $schemaFactory,
            $schemaFactory->buildSchema(
                $schemaContext['classname'],
                $schemaContext['format'],
                $schemaContext['type'],
                $schema['operationType'],
                $schema['operationName'],
                new Schema($schema->getVersion()),
                $schema['serializerContext'],
                $schema['forceCollection']
            ),
            array_merge($schemaContext, ['condition_attribute' => true])
        );

        $newDefinitions = $schema->getDefinitions();
        $newDefinitions->offsetSet($subAttributeCondition->getRootDefinitionKey(), $subAttributeCondition->getDefinitions()[$subAttributeCondition->getRootDefinitionKey()]);
        $newDefinitions->offsetSet(
            $schema->getRootDefinitionKey(),
            $this->makeSchemaForCondition($schema['$ref'], $subAttributeCondition['$ref'])
        );

        $schema->setDefinitions($newDefinitions);

        return $schema;
    }

    private function makeSchemaForCondition($root, $subAttributes = null): \ArrayObject
    {
        return new \ArrayObject([
            'type' => 'object',
            'required' => [
                'type',
                'exceptedResult',
                'subConditions',
            ],
            'properties' => [
                'type' => [
                    'type' => 'string',
                    'example' => Condition::TYPE_ALL,
                    'enum' => [Condition::TYPE_ALL, Condition::TYPE_ANY],
                ],
                'exceptedResult' => ['type' => 'boolean', 'example' => true],
                'subConditions' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                '$ref' => $root,
                                'description' => '$ref -> ' . $root,
                                'example' => [
                                    'type' => Condition::TYPE_ANY,
                                    'exceptedResult' => false,
                                    'subConditions' => [
                                        [
                                            'type'      => Condition::TYPE_ATTRIBUTE,
                                            'field'     => 'tax',
                                            'operator'  => NumberAttributeConditionTypeInterface::OPERATOR_LTE,
                                            'value'     => 45,
                                        ],
                                        [
                                            'type'      => Condition::TYPE_ATTRIBUTE,
                                            'field'     => 'status',
                                            'operator'  => BooleanAttributeConditionTypeInterface::OPERATOR_EQ,
                                            'value'     => true,
                                        ],
                                        [
                                            'type'      => Condition::TYPE_ATTRIBUTE,
                                            'field'     => 'name',
                                            'operator'  => StringAttributeConditionTypeInterface::OPERATOR_CONTAINS,
                                            'value'     => true,
                                        ],
                                    ],
                                ],
                            ],
                            [
                                '$ref' => $subAttributes,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
