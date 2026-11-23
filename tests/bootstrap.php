<?php

/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2026 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

if (!is_dir(\dirname(__DIR__).\DIRECTORY_SEPARATOR.'var')) {
    mkdir(\dirname(__DIR__).\DIRECTORY_SEPARATOR.'var');
}

$classLoader = require \dirname(__DIR__).'/vendor/autoload.php';
$classLoader->register(true);
