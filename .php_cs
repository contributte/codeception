<?php

$finder = PhpCsFixer\Finder::create()
    ->name('.php_cs')
    ->ignoreDotFiles(false)
    ->exclude('_helpers')
    ->exclude('_temp')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'combine_consecutive_unsets' => true,
        'linebreak_after_opening_tag' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'short_array_syntax' => true,
    ])
    ->finder($finder);
