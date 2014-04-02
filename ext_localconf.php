<?php 
if (!defined ('TYPO3_MODE')) die ('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Qbus.' . $_EXTKEY,
	'mailformwrapper',
	array("Mailform" => 'show')
);
