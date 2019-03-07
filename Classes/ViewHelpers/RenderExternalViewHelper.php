<?php
namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

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
class RenderExternalViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('extensionName', 'string', 'Render partial of this extension', true);
        $this->registerArgument('partial', 'string', 'The partial to render', false, null);
        $this->registerArgument('arguments', 'array', 'Arguments to pass to the partial', false, []);
        $this->registerArgument('pluginName', 'string', 'The pluginName of the plugin context to emulate', false, '');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $extensionName = $arguments['extensionName'];
        $partial = $arguments['partial'];
        $args = $arguments['arguments'];
        $pluginName = $arguments['pluginName'];

        // Overload arguments with own extension local settings (to pass own settings to external partial)
        $args = self::loadSettingsIntoArguments($args, $renderingContext);

        $ctxModified = false;
        $backupExtensionName = '';
        $backupPluginName = '';

        if ($pluginName) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
            $settings = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                str_replace('_', '', $extensionName),
                $pluginName
            );
            $args['settings'] = $settings;

            $req = $renderingContext->getControllerContext()->getRequest();

            $backupPluginName = $req->getPluginName();
            $backupExtensionName = $req->getControllerExtensionName();

            $req->setPluginName($pluginName);
            $req->setControllerExtensionName(str_replace('_', '', $extensionName));

            $ctxModified = true;
        }

        $view = $renderingContext->getViewHelperVariableContainer()->getView();

        $oldPartialRootPaths = $view->getPartialRootPaths();
        $newPartialRootPaths = array(
            ExtensionManagementUtility::extPath($extensionName) . 'Resources/Private/Partials'
        );
        $view->setPartialRootPaths($newPartialRootPaths);
        $content = $view->renderPartial($partial, null, $args);
        $view->setPartialRootPaths($oldPartialRootPaths);

        if ($ctxModified) {
            $req = $renderingContext->getControllerContext()->getRequest();
            $req->setPluginName($backupPluginName);
            $req->setControllerExtensionName($backupExtensionName);
        }

        return $content;
    }

    /**
     * If $arguments['settings'] is not set, it is loaded from the TemplateVariableContainer (if it is available there).
     *
     * @param  array $arguments
     * @param  RenderingContextInterface $renderingContext
     * @return array
     */
    protected static function loadSettingsIntoArguments($arguments, RenderingContextInterface $renderingContext)
    {
        if (!isset($arguments['settings']) && $renderingContext->getVariableProvider()->exists('settings')) {
            $arguments['settings'] = $renderingContext->getVariableProvider()->get('settings');
        }

        return $arguments;
    }
}
