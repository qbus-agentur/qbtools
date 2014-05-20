<?php
namespace  Qbus\Qbtools\ViewHelpers\Widget\Controller;

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
class MailformController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController {

	/**
	 * @var \Qbus\Qbtools\Utility\StandaloneTemplateRenderer
	 * @inject
	 */
	protected $standaloneTemplateRenderer;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @return void
	 */
	public function indexAction() {
		//$GLOBALS['TSFE']->additionalHeaderData[md5('qbtools_jquery')]  = '<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>';
		$this->view->assign("required", $this->widgetConfiguration["required"]);

		//$this->view->assign("qbmailformid", "qbmailform-".$this->controllerContext->getRequest()->getWidgetContext()->getAjaxWidgetIdentifier());
		$id = 'qbmailform-'.md5(uniqid(mt_rand(), TRUE));
		$this->view->assign("qbmailformid", $id);

		$this->view->setTemplateRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName("EXT:qbtools/Resources/Private/Templates/"));
	}

	/**
	 * @param array $msg
	 * @return string
	 */
	public function mailAction(array $msg = array()) {
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
			$recipient = array($recipient['email'] => $recipient['name']);
		} else {
			$recipient = array($recipient['email']);
		}

		$sender = ($sender !== null) ? array($sender['email'] => $sender['name']) : \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();
		$params = array(
			'to' => $recipient,
			'from' => $sender,
			'msg' => $msg
		);

		$view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->widgetConfiguration["mailTemplate"]));
		$view->assignMultiple($params);
		$text = $view->render();

		list($subject, $body) = explode("\n", $text, 2);

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

?>
