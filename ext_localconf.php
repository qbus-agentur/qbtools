<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Qbtools',
    'mailformwrapper',
    [\Qbus\Qbtools\Controller\MailformController::class => 'show']
);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['tx_qbtools_mailform'] = 'EXT:qbtools/tx_qbtools_mailform_eid.php';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['getData']['qbtools'] =
    'Qbus\\Qbtools\\Hooks\\ContentObjectGetDataHook';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['includeStaticTypoScriptSources']['qbtools'] =
    'Qbus\\Qbtools\\Utility\\TypoScriptManagementUtility->preprocessIncludeStaticTypoScriptSources';
