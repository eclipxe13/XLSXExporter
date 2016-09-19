<?php
return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        '-psr0', // do not enforce psr-0, it rewrites 'namespace Test\' to 'namespace test\'
        // symfony
        'array_element_white_space_after_comma',
        'duplicate_semicolon',
        'extra_empty_lines',
        'function_typehint_space',
        'join_function',
        'multiline_array_trailing_comma',
        'new_with_braces',
        'no_blank_lines_after_class_opening',
        'no_empty_lines_after_phpdocs',
        'object_operator',
        'operators_spaces',
        'phpdoc_scalar',
        'self_accessor',
        'single_array_no_trailing_comma',
        'single_quote',
        'spaces_before_semicolon',
        'unused_use',
        'whitespacy_lines',
        // contrib
        'concat_with_spaces',
        'logical_not_operators_with_successor_space',
        'no_blank_lines_before_namespace',
        'newline_after_open_tag',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()->in([__DIR__ . '/sources', __DIR__ . '/tests'])
    )
;
