<?php
namespace Qbus\Qbtools\UserFunc;

/* **************************************************************
 *  Copyright notice
 *
 *  (c) 2014  Benjamin Franzke <bfr@qbus.de>, Qbus Werbeagentur GmbH
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

/* FluidTemplate UserFunc
 *
 * This class can be used to execute stdWrap with a fluid template.
 * All the usual variables can be used as in the FLUIDTEMPLATE content object.
 * Note: The variable content is reserved!
 *
 * Example:
 *	page.10.value = foo
 * 	# foo is wrapped by the fluid template fileadmin/test.html:
 * 	page.10.stdWrap.postUserFunc = Qbus\Qbtools\UserFunc\FluidTemplate->wrap
 *	page.10.stdWrap.postUserFunc {
 *	file = fileadmin/test.html
 *		variables {
 * 			foo = TEXT
 * 			foo.value = bar
 *	 	}
 * 	}
 */

class FluidTemplate extends \TYPO3\CMS\Frontend\ContentObject\FluidTemplateContentObject {
	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	public $cObj;

	public function __construct() {}

	public function wrap($content, $conf) {
		if ($content == NULL) {
			/* Fore some reason we are called with $content == NULL,
			 * before we get called with the real $content.
			 * Lets ignore this until we figure out, what that means. */
			return;
		}

		parent::__construct($this->cObj);

		if (!isset($conf["variables."]))
			$conf["variables."] = array();

		if (isset($conf["variables."]["content"]) || isset($conf["variables."]["content."])) {
			throw new \InvalidArgumentException(
				'Cannot use reserved name content as variable name in FluidTemplate UserFunc.',
				1288095720
			);
		}

		$conf["variables."]["content"] = "TEXT";
		$conf["variables."]["content."] = array();
		$conf["variables."]["content."]["value"] = $content;
		return $this->render($conf);
	}
}
