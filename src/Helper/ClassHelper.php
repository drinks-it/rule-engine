<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2022 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

namespace DrinksIt\RuleEngineBundle\Helper;

/**
 * Class ClassHelper
 * @package DrinksIt\RuleEngineBundle\Helper
 *
 * @internal
 */
final class ClassHelper
{
    public static function exist($class_or_object, bool $autoload = true): bool
    {
        return class_exists($class_or_object, $autoload);
    }
}
