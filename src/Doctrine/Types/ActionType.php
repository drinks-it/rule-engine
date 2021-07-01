<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\DenormalizeAction;
use DrinksIt\RuleEngineBundle\Doctrine\Serializer\NormalizeAction;
use DrinksIt\RuleEngineBundle\Rule\CollectionActionsInterface;

final class ActionType extends RuleEngineType
{
    public const TYPE = 'rule_engine_action';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $actionCollectionProperties = $this->decodeJson($value);

        $normalize = new NormalizeAction($actionCollectionProperties);

        return $normalize->normalizeCollection();
    }

    /**
     * @param CollectionActionsInterface $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof CollectionActionsInterface) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                CollectionActionsInterface::class,
                [
                    CollectionActionsInterface::class,
                ]
            );
        }

        $denormalize = new DenormalizeAction($value);

        return parent::convertToDatabaseValue($denormalize->denormalizeCollection(), $platform);
    }

    public function getName()
    {
        return self::TYPE;
    }
}
