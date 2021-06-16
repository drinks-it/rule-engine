<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Rule\ActionInterface;

final class ActionType extends RuleEngineType
{
    public const TYPE = 'rule_engine_action';

    /**
     * @param ActionInterface $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof ActionInterface) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                ActionInterface::class,
                [
                    ActionInterface::class,
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
