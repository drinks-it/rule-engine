<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\ApiPlatform;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\TypeFactoryInterface;
use Symfony\Component\PropertyInfo\Type;

final class SchemaTypeFactory implements TypeFactoryInterface
{
    private TypeFactoryInterface $decorated;

    public function __construct(TypeFactoryInterface $typeFactory)
    {
        $this->decorated = $typeFactory;
    }

    public function getType(Type $type, string $format = 'json', ?bool $readableLink = null, ?array $serializerContext = null, Schema $schema = null): array
    {
        return $this->decorated->getType($type, $format, $readableLink, $serializerContext, $schema);
    }
}
