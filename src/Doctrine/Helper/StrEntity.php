<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Doctrine\Helper;

/**
 * Class StrEntity
 * @package DrinksIt\RuleEngineBundle\Doctrine\Helper
 *
 * @internal
 */
final class StrEntity
{
    /**
     * StrEntity constructor.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function getGetterNameMethod($fieldName): string
    {
        return 'get' . self::asCamelCase($fieldName);
    }

    public static function getSetterNameMethod($fieldName): string
    {
        return 'set' . self::asCamelCase($fieldName);
    }

    public static function getShortName(string $resourceClassName): string
    {
        return mb_substr($resourceClassName, mb_strrpos($resourceClassName, '\\') + 1);
    }

    /**
     * @param string $fieldName
     * @return string
     * @see \Symfony\Bundle\MakerBundle\Str
     */
    public static function asCamelCase(string $fieldName): string
    {
        return strtr(ucwords(strtr($fieldName, ['_' => ' ', '.' => ' ', '\\' => ' '])), [' ' => '']);
    }
}
