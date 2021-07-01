<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Helper;

/**
 * Class DecodeRuleProperty
 * @package DrinksIt\RuleEngineBundle\Rule\Helper
 * @internal
 */
final class DecodeRuleProperty
{
    private function __construct()
    {
    }

    public static function getConstByKey(string $key, string $classParse): array
    {
        if (!class_exists($classParse)) {
            return [];
        }

        $reflection = new \ReflectionClass($classParse);

        return array_filter(
            $reflection->getConstants() ?: [],
            fn ($constName) => strpos($constName, $key) !== false,
            ARRAY_FILTER_USE_KEY
        );
    }
}
