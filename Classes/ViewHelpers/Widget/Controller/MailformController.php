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

	const MAIL_TEMPLATE = "ViewHelpers/Widget/Mailform/Mail.txt";
	const JS_TEMPLATE = "ViewHelpers/Widget/Mailform/JavaScript.js";

	/**
	 * @var \Qbus\Qbtools\Utility\StandaloneTemplateRenderer
	 * @inject
	 */
	protected $standaloneTemplateRenderer;

	/**
	 * @return void
	 */
	public function indexAction() {
		$GLOBALS['TSFE']->additionalHeaderData[md5('qbtools_jquery')]  = '<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>';
		$this->view->assign("required", $this->widgetConfiguration["required"]);
		$this->view->assign("qbmailformid", "qbmailform-".$this->controllerContext->getRequest()->getWidgetContext()->getAjaxWidgetIdentifier());
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

		if (!is_array($recipient) || !array_key_exists("email", $recipient) || !array_key_exists("name", $recipient)) {
			/* TODO: Throw exception instead. */
			return json_encode(array("status" => "internal-error",
						 "error" => "\$recipient is not valid"));
		}

		$recipient = array($recipient['email'] => $recipient['name']);
		$sender = ($sender !== null) ? array($sender['email'] => $sender['name']) : \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();
		$params = array(
			'to' => $recipient,
			'from' => $sender,
			'msg' => $msg
		);

		$text = $this->standaloneTemplateRenderer->renderTemplate(self::MAIL_TEMPLATE, $params, $this->getMailTemplateRootPath());
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

	protected function getMailtemplateRootPath() {
		$path = null;

		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$widgetViewHelperClassName = $this->request->getWidgetContext()->getWidgetViewHelperClassName();

		if (isset($extbaseFrameworkConfiguration['view']['widget'][$widgetViewHelperClassName]['templateRootPath']) &&
		    strlen($extbaseFrameworkConfiguration['view']['widget'][$widgetViewHelperClassName]['templateRootPath']) > 0) {
			/* We use dirname, since we do not want the trailing Templates/, as our standaloneTemplateRender appends that on its own.
			 * @todo: Fix the standaloneTemplateRenderer? */
			$path = dirname($extbaseFrameworkConfiguration['view']['widget'][$widgetViewHelperClassName]['templateRootPath']);
		}

		/* Fallback to default path */
		if ($path === null) {
			$path = "EXT:qbtools/Resources/Private";
		}

		return $path;
	}
}

?>
