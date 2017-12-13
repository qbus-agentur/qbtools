<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'qbtools: viewhelpers and utilites',
    'description' => 'Basic tools for other extensions and fluid templates, mainly ViewHelpers and Utilities',
    'category' => 'misc',
    'author' => 'Axel Wüstemann',
    'author_email' => 'awu@qbus.de',
    'author_company' => 'Qbus Werbeagentur GmbH',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '2.2.2',
    'constraints' => array(
        'depends' => array(
            'extbase' => '6.2',
            'fluid' => '6.2',
            'typo3' => '6.2',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
    'autoload' => array(
        'psr-4' => array('Qbus\\Qbtools\\' => 'Classes')
    ),
);
