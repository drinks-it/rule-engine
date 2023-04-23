<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2023 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Rule\Helper;

use DrinksIt\RuleEngineBundle\Helper\ClassHelper;

/**
 * Class DecodeRuleProperty
 * @package DrinksIt\RuleEngineBundle\Rule\Helper
 * @internal
 */
final class DecodeRuleProperty
{
    /**
     * DecodeRuleProperty constructor.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function getConstByKey(string $key, string $classParse): array
    {
        if (!ClassHelper::exist($classParse)) {
            return [];
        }

        $reflection = new \ReflectionClass($classParse);

        return array_filter(
            $reflection->getConstants(),
            fn ($constName) => str_contains($constName, $key),
            ARRAY_FILTER_USE_KEY
        );
    }
}
