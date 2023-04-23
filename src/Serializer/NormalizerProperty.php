<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Serializer;

use DrinksIt\RuleEngineBundle\Serializer\Property\NormalizeFieldPropertyInterface;

final class NormalizerProperty implements NormalizerPropertyInterface
{
    /**
     * @var iterable|array|NormalizeFieldPropertyInterface[]
     */
    private iterable $normalizerFieldProperties;

    public function __construct(iterable $normalizerFieldProperties = [])
    {
        $this->normalizerFieldProperties = $normalizerFieldProperties;
    }

    /**
     * @inheritDoc
     */
    public function normalize($value, string $resourceClass, string $propertyName, array $context = [])
    {
        if (!$this->normalizerFieldProperties) {
            return $value;
        }

        foreach ($this->normalizerFieldProperties as $propertyNormalizer) {
            if (!$propertyNormalizer instanceof NormalizeFieldPropertyInterface) {
                continue;
            }

            if (!$propertyNormalizer->support($resourceClass, $propertyName, $context)) {
                continue;
            }

            $value = $propertyNormalizer->normalize($value, $resourceClass, $propertyName, $context);
        }

        return $value;
    }
}
