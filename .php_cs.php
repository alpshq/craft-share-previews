<?php

$finder = PhpCsFixer\Finder::create()
    ->notPath('vendor')
    ->notPath('storage')
    ->notPath('tests/_support/')
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config)
    ->setRules([
        '@Symfony' => true,
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['align_double_arrow' => false],
        'class_attributes_separation' => [
            'elements' => [
                'method',
                'property',
                'const',
            ],
        ],
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'self_static_accessor' => true,
        'fully_qualified_strict_types' => true,
        'single_line_comment_style' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'new_with_braces' => false,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'allow_unused_params' => false,
            'remove_inheritdoc' => true,
        ],
        'not_operator_with_successor_space' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => true,
        ],
        'phpdoc_line_span' => [
            'property' => 'single',
            'const' => 'single',
        ],
        'phpdoc_order' => true,
        'single_line_throw' => false,
        'yoda_style' => false,
    ])
    ->setFinder($finder);