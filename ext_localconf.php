<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Qbus.' . $_EXTKEY,
    'mailformwrapper',
    array('Mailform' => 'show')
);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['tx_qbtools_mailform'] = 'EXT:qbtools/tx_qbtools_mailform_eid.php';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['getData']['qbtools'] =
    'EXT:qbtools/Classes/Hooks/ContentObjectGetDataHook.php:Qbus\\Qbtools\\Hooks\\ContentObjectGetDataHook';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['includeStaticTypoScriptSources']['qbtools'] =
    'EXT:qbtools/Classes/Utility/TypoScriptManagementUtility.php:Qbus\\Qbtools\\Utility\\TypoScriptManagementUtility->preprocessIncludeStaticTypoScriptSources';
