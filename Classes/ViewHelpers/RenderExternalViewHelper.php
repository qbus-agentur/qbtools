<?php
namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;


/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Christian Kuhn <lolli@schwarzbu.ch>
 *      2013 Axel Wüstemann <awu@qbus.de>, Qbus Werbeagentur GmbH
 *      2015 Benjamin Franzke <bfr@qbus.de>, Qbus Werbeagentur GmbH
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
 ***************************************************************/

/**
 * Renders a partial from another extension in own namespace.
 * based on http://pastebin.com/5NEAmqQs (c) Christian Kuhn <lolli@schwarzbu.ch> 2011
 *
 * <q:renderExternal
 *         partial="Device/ProductImage"
 *         extensionName="EnetOtherExtension"
 *         arguments="{
 *             product: entry.device.product,
 *             clearing: entry.device.clearing,
 *             maxWidth: 30,
 *             maxHeight: 30
 *         }"
 *     />
 *
 * @author Christian Kuhn <lolli@schwarzbu.ch>
 * @TODO: Implement sections
 */
class RenderExternalViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * Renders the content.
     *
     * @param  string $extensionName Render partial of this extension
     * @param  string $partial       The partial to render
     * @param  array  $arguments     Arguments to pass to the partial
     * @return string
     */
    public function render($extensionName, $partial = null, array $arguments = array())
    {
        // Overload arguments with own extension local settings (to pass own settings to external partial)
        $arguments = $this->loadSettingsIntoArgumentsNonStatic($arguments);

        $oldPartialRootPaths = ObjectAccess::getProperty($this->viewHelperVariableContainer->getView(), 'partialRootPaths', true);
        $newPartialRootPaths = array(
            ExtensionManagementUtility::extPath($extensionName) . 'Resources/Private/Partials'
        );
        $this->viewHelperVariableContainer->getView()->setPartialRootPaths($newPartialRootPaths);
        $content = $this->viewHelperVariableContainer->getView()->renderPartial($partial, null, $arguments);
        $this->viewHelperVariableContainer->getView()->setPartialRootPaths($oldPartialRootPaths);

        return $content;
    }

    /**
     * If $arguments['settings'] is not set, it is loaded from the TemplateVariableContainer (if it is available there).
     *
     * @param array $arguments
     * @return array
     */
    protected function loadSettingsIntoArgumentsNonStatic($arguments) {
        if (!isset($arguments['settings']) && $this->templateVariableContainer->exists('settings')) {
            $arguments['settings'] = $this->templateVariableContainer->get('settings');
        }
        return $arguments;
    }

}
