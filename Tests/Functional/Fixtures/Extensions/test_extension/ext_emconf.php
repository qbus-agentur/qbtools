<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'A test extension',
    'description' => 'A test extension',
    'category' => 'example',
    'author' => 'Qbus',
    'author_company' => '',
    'author_email' => '',
    'state' => 'stable',
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
