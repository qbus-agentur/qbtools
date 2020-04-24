<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('qbtools', 'Configuration/TypoScript', 'Qbus Tools');

$TCA['tt_content']['columns']['tx_gridelements_children']['config']['appearance']['enabledControls']['new'] = true;
$TCA['tt_content']['columns']['tx_gridelements_children']['config']['appearance']['levelLinksPosition'] = 'top';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['qbtools'] = 'Qbus\\Qbtools\\Hooks\\Options\\BackendLayoutDataProvider';
