<?php
namespace Qbus\Qbtools\Xclass;

/* ***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Benjamin Franzke <bfr@qbus.de>, Qbus Werbeagentur GmbH
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
 *
 * @package qbTools
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

/* XClass that overwrites the DCE renderPreview hanlding.
 * We need this when using <type>inline</type> with tt_content FlexForm elements.
 *
 * DCE thinks all elements are of CType=dce_dceuid* and errors out with
 * 'No DCE found with uid "0" inline'.
 * By overwriting and handling those manually we are able to use inline tt_content elments.
 */

class DceController extends \Tx_Dce_Controller_DceController {

	public function renderPreviewAction() {
		$uid = intval($this->settings['dceUid']);
		if ($uid == 0) {
			$contentObject = $this->getContentObject($this->settings['contentElementUid']);
			if (substr($contentObject["CType"], 0, 10) !== "dce_dceuid") {
				if ($this->settings["previewType"] === 'header') {
					return $contentObject["header"];
				} else {
					return $contentObject["bodytext"];
				}
			}
		}

		return parent::renderPreviewAction();
	}
}
?>
