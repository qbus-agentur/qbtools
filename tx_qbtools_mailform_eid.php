<?php

use Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/* @var $hashService \TYPO3\CMS\Extbase\Security\Cryptography\HashService */
$hashService = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Security\Cryptography\HashService', $TYPO3_CONF_VARS);

if (isset($_POST['msg']['__tx_qbtools_mailform_data'])) {
    $config = unserialize(base64_decode($hashService->validateAndStripHmac($_POST['msg']['__tx_qbtools_mailform_data'])));
    if (!is_array($config)) {
        print json_encode(['status' => 'error']);
        exit;
    }

    /* @var $mailformController MailformController */
    $mailformController = GeneralUtility::makeInstance(MailformController::class);
    ObjectAccess::setProperty($mailformController, 'widgetConfiguration', $config, true);
    unset($_POST['msg']['__tx_qbtools_mailform_data']);
    print $mailformController->mailAction($_POST['msg']);
} else {
    print json_encode(['status' => 'error']);
}
