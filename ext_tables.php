<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['qbtools'] = 'Qbus\\Qbtools\\Hooks\\Options\\BackendLayoutDataProvider';
