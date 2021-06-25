<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Rule\ActionColumn;

final class ActionType extends RuleEngineType
{
    public const TYPE = 'rule_engine_action';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new ActionColumn($this->decodeJson($value));
    }

    /**
     * @param ActionColumn $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof ActionColumn) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                ActionColumn::class,
                [
                    ActionColumn::class,
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
