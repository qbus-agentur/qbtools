<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'qbtools: viewhelpers and utilites',
    'description' => 'Basic tools for other extensions and fluid templates, mainly ViewHelpers and Utilities',
    'category' => 'misc',
    'author' => 'Axel Wüstemann',
    'author_email' => 'awu@qbus.de',
    'author_company' => 'Qbus Internetagentur GmbH',
    'state' => 'stable',
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'extbase' => '12.4.0-12.4.99',
            'fluid' => '12.4.0-12.4.99',
            'typo3' => '12.4.0-12.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => ['Qbus\\Qbtools\\' => 'Classes'],
    ],
];
