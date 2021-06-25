<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Rule\Condition\CollectionCondition;
use DrinksIt\RuleEngineBundle\Rule\ConditionsInterface;

final class ConditionsType extends RuleEngineType
{
    public const TYPE = 'rule_engine_conditions';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new CollectionCondition($this->decodeJson($value));
    }

    /**
     * @param ConditionsInterface $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof ConditionsInterface) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                ConditionsInterface::class,
                [
                    ConditionsInterface::class,
                ]
            );
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function getName()
    {
        return self::TYPE;
    }
}
