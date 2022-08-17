<?php
$sourceDir = __DIR__;

$exclude = [
    ".git",
    ".idea",
    "vendor",
    "public",
    "storage",
    "resources",
    "node_modules",
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_unsets' => true,
        'heredoc_to_nowdoc' => true,
        'list_syntax' => ['syntax' => 'short'],
        'no_extra_blank_lines' => ['tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block']],
        'echo_tag_syntax' => ['format' => 'long'],
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => ['sort_algorithm' => 'length'],
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'no_unused_imports' => true,
        'semicolon_after_instruction' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'phpdoc_summary' => true,
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude($exclude)
            ->in($sourceDir)
    );
