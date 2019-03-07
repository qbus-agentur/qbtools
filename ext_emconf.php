<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'qbtools: viewhelpers and utilites',
    'description' => 'Basic tools for other extensions and fluid templates, mainly ViewHelpers and Utilities',
    'category' => 'misc',
    'author' => 'Axel Wüstemann',
    'author_email' => 'awu@qbus.de',
    'author_company' => 'Qbus Internetagentur GmbH',
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
    'version' => '3.0.1',
    'constraints' => array(
        'depends' => array(
            'extbase' => '8.7.0-9.5.99',
            'fluid' => '8.7.0-9.5.99',
            'typo3' => '8.7.0-9.5.99',
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
