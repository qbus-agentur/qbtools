<?php
namespace Qbus\Qbtools\Controller;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Benjamin Franzke <bfr@qbus.de, Qbus Werbeagentur GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Mailform Controller that wraps the Mailform View Helper.
 *
 * It is intended to be used via typoscript from a standalone fluid template (e.g. in DCE).
 * In a standalone fluid template you need to use instead of
 *   <qbtools:mailform recipient="..." sender="..." />
 * a typoscript caller, that itself calls this controller, which passes rendering on to the viewhelper:
 *   <f:cObject typoscriptObjectPath="tt_content.list.20.qbtools_mailformwrapper"
 *   		data="{recipientName: "Name", recipientEmail: "mail@domain.tld"}" />
 *
 * Note: The tt_content.list.20.qbtools_mailformwrapper is created through the
 *       call configurePlugin in this extensions ext_localconf.php.
 *
 * @todo Extend to be able to be used as a tt_content ctype element.
 *
 * @package qbtools
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class MailformController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	/* Initialize arguments from typoscript cObject context data.
	 * That is the data that is passes by the fluid f:cObject view helper. */
	public function initializeShowAction() {
		$cobj = $this->configurationManager->getContentObject();
		foreach ($this->arguments as $argument) {
			if (isset($cobj->data[$argument->getName()])) {
				$argument->setDefaultValue($cobj->data[$argument->getName()]);
				$argument->setRequired(false);
			}
		}
	}

	/**
	 * @param \string $recipientName
	 * @param \string $recipientEmail
	 * @param \string $senderName
	 * @param \string $senderEmail
	 */
	public function showAction($recipientName, $recipientEmail, $senderName = null, $senderEmail = null) {
		$this->view->assign("recipient", array("email" => $recipientEmail, "name" => $recipientName));
		if (strlen($senderName) > 0 && strlen($senderEmail) > 0) {
			$this->view->assign("sender", array("email" => $senderEmail, "name" => $senderName));
		} else {
			$this->view->assign("sender", NULL);
		}
	}
}
?>
