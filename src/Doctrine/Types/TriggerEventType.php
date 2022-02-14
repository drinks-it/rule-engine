<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventColumn;

final class TriggerEventType extends RuleEngineType
{
    public const TYPE = 'rule_engine_event';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new TriggerEventColumn($value);
    }

    /**
     * @param TriggerEventColumn $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof TriggerEventColumn) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                TriggerEventColumn::class,
                [
                    TriggerEventColumn::class,
                ]
            );
        }

        return $value->getEntityClassName();
    }

    public function getName()
    {
        return self::TYPE;
    }
}
