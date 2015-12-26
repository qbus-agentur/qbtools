<?php
use \TYPO3\CMS\Core\Utility\GeneralUtility;

class DummyMailformController
{

    public $widgetConfiguration;

    /**
     * @param array $msg
     * @return string
     */
    public function mailAction(array $msg = array())
    {
        $recipient = $this->widgetConfiguration['recipient'];
        $sender = $this->widgetConfiguration['sender'];
        $required = $this->widgetConfiguration['required'];

        $missing = array();
        foreach ($required as $r) {
            if (!array_key_exists($r, $msg) || strlen($msg[$r]) == 0) {
                $missing[] = $r;
            }
        }
        if (count($missing)) {
            return json_encode(array("status" => "fields-missing",
                         "missing" => $missing));
        }

        if (!is_array($recipient) || !array_key_exists("email", $recipient)) {
            /* TODO: Throw exception instead. */
            return json_encode(array("status" => "internal-error",
                         "error" => "\$recipient is not valid"));
        }

        if (isset($recipient['name']) && strlen($recipient['name']) > 0) {
            $tmp = $recipient;
            $recipient = array();
            foreach (GeneralUtility::trimExplode(',', $tmp['email']) as $email) {
                $recipient[$email] = $tmp['name'];
            }
        } else {
            $recipient = GeneralUtility::trimExplode(',', $recipient['email']);
        }

        $sender = ($sender !== null) ? array($sender['email'] => $sender['name']) : \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();

        /* Temporary overwrite */
        $recipientSave = $recipient;
        $recipient = array("bfr@qbus.de" => "Benjamin Franzke");
        /* Temporary overwrite  END */

        $params = array(
            'to' => $recipient,
            'from' => $sender,
            'msg' => $msg
        );

        $view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->widgetConfiguration["mailTemplate"]));
        $view->assignMultiple($params);
        $text = $view->render();

        list($subject, $body) = explode("\n", $text, 2);

        /* Temporary overwrite */
        $subject .= ' - Adressat: ' . implode(',', $recipientSave);
        /* Temporary overwrite  END */

        $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $mail->setFrom($sender);
        $mail->setTo($recipient);
        $mail->setSubject($subject);
        $mail->setBody(trim($body));
        if (isset($msg["email"]) && strlen($msg["email"]) > 0) {
            $mail->setReplyTo($msg["email"]);
        }
        $mail->send();

        return json_encode(array("status" => "ok"));
    }
}

$hashService = GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Security\Cryptography\HashService', $TYPO3_CONF_VARS);

if (isset($_POST['msg']['__tx_qbtools_mailform_data'])) {
    $config = unserialize(base64_decode($hashService->validateAndStripHmac($_POST['msg']['__tx_qbtools_mailform_data'])));
    if (!is_array($config)) {
        print json_encode(array("status" => "error"));
        exit;
    }

    $controller = new DummyMailformController;
    $controller->widgetConfiguration = $config;
    unset($_POST['msg']['__tx_qbtools_mailform_data']);
    print $controller->mailAction($_POST['msg']);
} else {
    print json_encode(array("status" => "error"));
}
