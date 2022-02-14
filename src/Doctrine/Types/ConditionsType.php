<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\DenormalizeCondition;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\NormalizeCondition;
use DrinksIt\RuleEngineBundle\Rule\CollectionConditionInterface;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;

final class ConditionsType extends RuleEngineType
{
    public const TYPE = 'rule_engine_conditions';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $decodedConditions = $this->decodeJson($value);

        if (!$decodedConditions) {
            return new CollectionCondition();
        }

        $normalization = new NormalizeCondition($decodedConditions);

        return $normalization->normalizeCollection();
    }

    /**
     * @param CollectionConditionInterface $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof CollectionConditionInterface) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                CollectionConditionInterface::class,
                [
                    CollectionConditionInterface::class,
                ]
            );
        }
        $denormalize = new DenormalizeCondition($value);

        return parent::convertToDatabaseValue($denormalize->denormalizeCollection(), $platform);
    }

    public function getName()
    {
        return self::TYPE;
    }
}
