<?php
namespace Qbus\Qbtools\ViewHelpers\Widget;

class MailformViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper {

	/**
	 * @var bool
	 */
	protected $ajaxWidget = TRUE;

	/**
	 * @var \Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController
	 */
	protected $controller;

	/**
	 * @param \Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController $controller
	 * @return void
	 */
	public function injectController(\Qbus\Qbtools\ViewHelpers\Widget\Controller\MailformController $controller) {
		$this->controller = $controller;
	}

	/**
	 * @param array $recipient = array('mail': "mail@address", 'name' => "Full Name");
	 * @param array $sender = array('mail': "mail@address", 'name' => "Full Name");
	 * @param array $required = array("firstname", "lastname", "email");
	 * @param string $mailTemplate
	 * @return string
	 */
	public function render($recipient, $sender = null, $required = array("firstname", "lastname", "email", "message"),
			$mailTemplate = "EXT:qbtools/Resources/Private/Templates/Mailform/Mail.txt") {

		/* <f:renderChildren> does not include the variable context from the  subrequest-controller,
		 * therefore we set the desired variables here. */
		$this->viewHelperVariableContainer->add('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'fieldNamePrefix', "msg");
		$result = $this->initiateSubRequest();
		$this->viewHelperVariableContainer->remove('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'fieldNamePrefix');

		return $result;
	}
}

?>
