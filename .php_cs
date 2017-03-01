<?php

/*
 * (c) 2016 by Cyberhouse GmbH
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the MIT License (MIT)
 *
 * For the full copyright and license information see
 * <https://opensource.org/licenses/MIT>
 */

use Cyberhouse\Phpstyle\Fixer\LowerHeaderCommentFixer;
use Cyberhouse\Phpstyle\Fixer\NamespaceFirstFixer;
use Cyberhouse\Phpstyle\Fixer\SingleEmptyLineFixer;
use PhpCsFixer\Config;
use Symfony\Component\Finder\Finder;

if (PHP_SAPI !== 'cli') {
    die('Nope');
}

$header = 'This file is (c) ' . date('Y') . ' by Cyberhouse GmbH

It is free software; you can redistribute it and/or
modify it under the terms of the GPLv3 license

For the full copyright and license information see
<https://www.gnu.org/licenses/gpl-3.0.html>';

LowerHeaderCommentFixer::setHeader($header);

$finder = Finder::create()
    ->name('/\.php$/')
    ->exclude('vendor')
    ->exclude('bin')
    ->in(__DIR__);

return Config::create()
    ->setUsingCache(true)
    ->registerCustomFixers([
        new LowerHeaderCommentFixer(),
        new NamespaceFirstFixer(),
        new SingleEmptyLineFixer(),
    ])
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'Cyberhouse/lower_header_comment' => true,
        'Cyberhouse/namespace_first' => true,
        'Cyberhouse/single_empty_line' => true,
        'encoding' => true,
        'cast_spaces' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_unsets' => true,
        'binary_operator_spaces' => [
            'align_double_arrow' => true,
            'align_equals' => false,
        ],
        'braces' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => true,
        'dir_constant' => true,
        'ereg_to_preg' => true,
        'hash_to_slash_comment' => true,
        'include' => true,
        'line_ending' => true,
        'lowercase_cast' => true,
        'modernize_types_casting' => true,
        'native_function_casing' => true,
        'new_with_braces' => true,
        'no_leading_import_slash' => true,
        'no_php4_constructor' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'psr0' => false,
        'short_scalar_cast' => true,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'strict_comparison' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_order' => true,
    ])
    ->setFinder($finder);
