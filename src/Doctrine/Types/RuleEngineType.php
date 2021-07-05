<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

abstract class RuleEngineType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (new JsonEncode())->encode($value, JsonEncoder::FORMAT);
    }
    protected function decodeJson($value)
    {
        return (new JsonDecode())->decode($value, JsonEncoder::FORMAT, [
            'json_decode_associative' => true,
        ]);
    }
}
