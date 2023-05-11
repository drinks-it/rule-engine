<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform;

use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Metadata\Operation;
use DrinksIt\RuleEngineBundle\ApiPlatform\Schema\ActionSchemaFactoryInterface;
use DrinksIt\RuleEngineBundle\ApiPlatform\Schema\ConditionSchemaFactoryInterface;
use DrinksIt\RuleEngineBundle\Rule\Action\ActionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\Condition;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;

final class SchemaFactory implements SchemaFactoryInterface
{
    private SchemaFactoryInterface $decoder;

    private ConditionSchemaFactoryInterface $conditionSchemaFactory;

    private ActionSchemaFactoryInterface $actionSchemaFactory;

    private const SCHEMAS_BUILDS = [
        Condition::class,
        ActionInterface::class,
        TriggerEventColumn::class,
    ];

    public function __construct(
        SchemaFactoryInterface $schemaFactory,
        ConditionSchemaFactoryInterface $conditionSchemaFactory,
        ActionSchemaFactoryInterface $actionSchemaFactory
    ) {
        $this->decoder                = $schemaFactory;
        $this->conditionSchemaFactory = $conditionSchemaFactory;
        $this->actionSchemaFactory    = $actionSchemaFactory;
    }

    public function buildSchema(string $className, string $format = 'json', string $type = Schema::TYPE_OUTPUT, ?Operation $operation = null, ?Schema $schema = null, ?array $serializerContext = null, bool $forceCollection = false): Schema
    {
        $schemaBuild = $this->decoder->buildSchema($className, $format, $type, $operation, $schema, $serializerContext);

        if (!\in_array($className, self::SCHEMAS_BUILDS)) {
            return $schemaBuild;
        }

        $schemaContext = [
            'className'         => $className,
            'format'            => $format,
            'type'              => $type,
            'operation'         => $operation,
            'serializerContext' => $serializerContext,
            'forceCollection'   => $forceCollection,
        ];

        switch ($className) {
            case Condition::class:
                return $this->conditionSchemaFactory->buildSchemaForCondition(
                    $this->decoder,
                    $schemaBuild,
                    $schemaContext
                );
            case ActionInterface::class:
                return $this->actionSchemaFactory->buildSchemaForAction(
                    $this->decoder,
                    $schemaBuild,
                    $schemaContext
                );
            case TriggerEventColumn::class:
                $definitions = $schemaBuild->getDefinitions();
                $definitions->offsetSet($schemaBuild->getRootDefinitionKey(), new \ArrayObject([
                    'type'    => 'string',
                    'example' => 'TriggerClassName',
                ]));

                $schemaBuild->setDefinitions($definitions);

                return $schemaBuild;
        }

        return $schemaBuild;
    }
}
