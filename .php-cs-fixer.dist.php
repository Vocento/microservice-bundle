<?php

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setRules([
        '@PER' => true,
        '@PER:risky' => true,
        '@PHP54Migration' => true,
        '@PHP56Migration:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'strict_comparison' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'native_function_invocation' => [
            'exclude' => [],
            'include' => ['@all'],
            'scope' => 'all',
            'strict' => true,
        ],
        'native_constant_invocation' => [
            'exclude' => [
                'null',
                'false',
                'true',
            ],
            'fix_built_in' => true,
            'include' => [],
            'scope' => 'all',
            'strict' => false,
        ],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_unreachable_default_argument_value' => true,
        'comment_to_phpdoc' => true,
        'phpdoc_to_comment' => ['ignored_tags' => ['todo', 'var']],
        'header_comment' => [
            'header' => <<<HEADER
                This file is part of the Vocento Software.

                (c) Vocento S.A., <desarrollo.dts@vocento.com>

                For the full copyright and license information, please view the LICENSE
                file that was distributed with this source code.

                HEADER,
            'location' => 'after_open',
            'separate' => 'bottom',
        ],
        'single_line_empty_body' => false,
        'phpdoc_line_span' => ['const' => 'single', 'property' => 'single', 'method' => 'multi'],
        'self_static_accessor' => true,
        'simplified_if_return' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
    );
