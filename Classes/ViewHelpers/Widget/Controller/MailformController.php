<?php
namespace  Qbus\Qbtools\ViewHelpers\Widget\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
class MailformController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController
{
    /**
     * @var \TYPO3\CMS\Extbase\Security\Cryptography\HashService
     * @inject
     */
    protected $hashService;

    /**
     * @return void
     */
    public function indexAction()
    {
        //$GLOBALS['TSFE']->additionalHeaderData[md5('qbtools_jquery')]  = '<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>';
        $this->view->assign('required', $this->widgetConfiguration['required']);

        //$this->view->assign("qbmailformid", "qbmailform-".$this->controllerContext->getRequest()->getWidgetContext()->getAjaxWidgetIdentifier());
        $id = 'qbmailform-' . md5(uniqid(mt_rand(), true));
        $this->view->assign('qbmailformid', $id);

        $this->widgetConfiguration['receiver_overwrite_email'] = $this->getReceiverOverwriteEmail();

        $this->view->assign('absRefPrefix', $GLOBALS['TSFE']->absRefPrefix);
        $this->view->assign('qbmailformConfig', $this->hashService->appendHmac(base64_encode(serialize($this->widgetConfiguration))));
        $this->view->setTemplateRootPaths([GeneralUtility::getFileAbsFileName('EXT:qbtools/Resources/Private/Templates/')]);
    }

    /**
     * @param  array  $msg
     * @return string
     */
    public function mailAction(array $msg = array())
    {
        $recipient = $this->widgetConfiguration['recipient'];
        $sender = $this->widgetConfiguration['sender'];
        $required = $this->widgetConfiguration['required'];
        $cc = $this->widgetConfiguration['cc'];
        $bcc = $this->widgetConfiguration['bcc'];

        $missing = array();
        /* example: $required = [ 'name', 'email,phone' ] => name and (phone or email) are required.*/
        foreach ($required as $orFieldList) {
            $orFields = explode(',', $orFieldList);
            $found = false;
            foreach ($orFields as $field) {
                if (array_key_exists($field, $msg) && strlen($msg[$field]) != 0) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                foreach ($orFields as $field) {
                    $missing[] = $field;
                }
            }
        }

        if (count($missing)) {
            return json_encode(array(
                'status' => 'fields-missing',
                'missing' => $missing
            ));
        }

        if (!is_array($recipient) || !array_key_exists('email', $recipient)) {
            /* TODO: Throw exception instead. */
            return json_encode(array(
                'status' => 'internal-error',
                'error' => '$recipient is not valid'
            ));
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

        $sender = ($sender !== null) ? array($sender['email'] => $sender['name']) : MailUtility::getSystemFrom();

        $recipientSave = $recipient;
        $recipientOverwrite = $this->widgetConfiguration['receiver_overwrite_email'];
        if ($recipientOverwrite !== null) {
            $recipient = $recipientOverwrite;
            $cc = null;
            $bcc = null;
        }

        $params = array(
            'to' => $recipient,
            'from' => $sender,
            'msg' => $msg
        );

        $view = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->widgetConfiguration['mailTemplate']));
        $view->assignMultiple($params);
        $text = $view->render();

        list($subject, $body) = explode("\n", $text, 2);

        if ($recipientOverwrite !== null) {
            $subject .= ' – Recipient: ' . implode(',', $recipientSave);
        }

        $mail = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $mail->setFrom($sender);
        $mail->setTo($recipient);
        if ($cc) {
            $mail->setCc(array($cc['email'] => $cc['name']));
        }
        if ($bcc) {
            $mail->setBcc(array($bcc['email'] => $bcc['name']));
        }
        $mail->setSubject($subject);
        $mail->setBody(trim($body));
        if (isset($msg['email']) && strlen($msg['email']) > 0) {
            $mail->setReplyTo($msg['email']);
        }
        $mail->send();

        return json_encode(array('status' => 'ok'));
    }

    /**
     * @return string
     */
    protected function getReceiverOverwriteEmail()
    {
        if (!isset($GLOBALS['TSFE']->config['config']['tx_qbtools.']['mailform.']['receiver.']['overwrite.']['email'])) {
            return null;
        }

        $overwrite = trim($GLOBALS['TSFE']->config['config']['tx_qbtools.']['mailform.']['receiver.']['overwrite.']['email']);
        if ($overwrite == '') {
            return null;
        }

        return $overwrite;
    }
}
