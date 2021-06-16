<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Rule\TriggerEventInterface;

final class TriggerEventType extends RuleEngineType
{
    public const TYPE = 'rule_engine_event';

    /**
     * @param TriggerEventInterface $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof TriggerEventInterface) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                TriggerEventInterface::class,
                [
                    TriggerEventInterface::class,
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
