<?php
/*
 * This file is part of Rule Engine Symfony Bundle.
 * © 2010-2021 DRINKS | Silverbogen AG
 */

declare(strict_types=1);

$year = date('Y');

$header = <<<HEADER
    This file is part of Rule Engine Symfony Bundle.
    © 2010-{$year} DRINKS | Silverbogen AG
    HEADER;

$finder = new PhpCsFixer\Finder();

$finder->name('*.php')
    ->notName('.php-cs-fixer.dist.php')
    ->in(__DIR__.\DIRECTORY_SEPARATOR.'src')
    ->in(__DIR__.\DIRECTORY_SEPARATOR.'tests')
    ->exclude([
        __DIR__.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR,
    ]);

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'.php_cs.cache')
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,

        // Comment
        'header_comment' => [
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'bottom',
        ],

        // Array Notation
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_before_comma_in_array' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,

        // Function Notation
        'native_function_invocation' => true,

        // Import
        'fully_qualified_strict_types' => true,

        // PHPDoc
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_types' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_var_without_name' => false,
        'phpdoc_var_annotation_correct_order' => true,

        // Whitespace
        'array_indentation' => true,
        'compact_nullable_typehint' => true,
        'method_chaining_indentation' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'return',
                'throw',
                'try',
                'exit',
                'if',
                'switch',
            ],
        ],
        'no_extra_blank_lines' => [
            'tokens' => [
                'break', 'case', 'continue', 'curly_brace_block',
                'default', 'extra', 'parenthesis_brace_block',
                'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait',
            ],
        ],
        'no_spaces_around_offset' => true,
        'no_spaces_inside_parenthesis' => true,

        // Strict
        'strict_comparison' => true,

        // String Notation
        'explicit_string_variable' => true,
        'semicolon_after_instruction' => true,

        '@PSR2' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
    ])
    ->setRiskyAllowed(true);
