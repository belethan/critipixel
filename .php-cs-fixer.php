<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,

        // Structure générale
        'strict_param' => true,
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'single_import_per_statement' => true,

        // PHP 8+
        'normalize_index_brace' => true,
        'ternary_to_elvis_operator' => false,
        'trailing_comma_in_multiline' => true,

        // Style
        'linebreak_after_opening_tag' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_empty_phpdoc' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_align' => [
            'align' => 'left',
        ],

        // Classes
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],

        // Arrays & controle
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_trailing_whitespace' => true,
    ])
    ->setFinder($finder);
