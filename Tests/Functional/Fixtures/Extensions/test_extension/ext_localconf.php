<?php
defined('TYPO3_MODE') or die();

// register extbase plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('Qbus.TestExtension', 'Pi1', ['Dummy' => 'action'], []);
