<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(
        array(
            'no_blank_lines_after_class_opening',
            'encoding',
            'return',
            'whitespacy_line',
            'remove_leading_slash_use',
            'operators_spaces',
            'phpdoc_indent',
            'phpdoc_params',
            'phpdoc_trim',
            'self_accessor',
            'single_quote',
            'spaces_after_semicolon',
            'spaces_before_semicolon',
            'unused_use',
            'concat_with_spaces',
            'newline_after_open_tag',
        )
    )
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__)
    );
