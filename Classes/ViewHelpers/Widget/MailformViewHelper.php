<?php
namespace Qbus\Qbtools\ViewHelpers\Widget;

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
	public function render($recipient, $sender = null, $required = array("firstname", "lastname", "email"),
		$mailTemplate = "EXT:qbtools/Resources/Private/Templates/Mailform/Mail.txt") {
		return $this->initiateSubRequest();
	}
}

?>
