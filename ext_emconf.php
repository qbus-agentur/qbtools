<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'qbtools: viewhelpers and utilites',
    'description' => 'Basic tools for other extensions and fluid templates, mainly ViewHelpers and Utilities',
    'category' => 'misc',
    'author' => 'Axel Wüstemann',
    'author_email' => 'awu@qbus.de',
    'author_company' => 'Qbus Internetagentur GmbH',
    'state' => 'stable',
    'version' => '3.0.10',
    'constraints' => [
        'depends' => [
            'extbase' => '8.7.0-11.5.99',
            'fluid' => '8.7.0-11.5.99',
            'typo3' => '8.7.0-11.5.99',
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
