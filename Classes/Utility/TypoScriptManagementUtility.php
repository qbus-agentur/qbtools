<?php
namespace Qbus\Qbtools\Utility;

use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TypoScriptManagementUtility
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TypoScriptManagementUtility
{
    /**
     * @var array
     */
    private static $staticTypoScript = array();

    /*
     * @var bool
     */
    private static $includeStaticTemplateFilesBeforeAllStaticTemplates = false;


    /**
     * @param  string $path
     * @return void
     */
    public static function addStaticTypoScript($path)
    {
        if (!in_array($path, self::$staticTypoScript)) {
            array_push(self::$staticTypoScript, $path);
        }
    }

    /**
     * @param  array $paths
     * @return void
     */
    public static function addStaticTypoScripts($paths)
    {
        foreach ($paths as $path) {
            self::addStaticTypoScript($path);
        }
    }

    /**
     * @param bool $enable
     */
    public static function forceStaticTSFilesBeforeAllTSTemplates($enable = true)
    {
        self::$includeStaticTemplateFilesBeforeAllStaticTemplates = $enable;
    }

    /**
     * Includes static template from extensions
     *
     * @param array           $params
     * @param TemplateService $pObj
     * @return void
     */
    public function preprocessIncludeStaticTypoScriptSources(array &$params, TemplateService $pObj)
    {
        if (isset($params['row']['root']) && $params['row']['root']) {
            $existing = GeneralUtility::trimExplode(',', $params['row']['include_static_file']);
            $staticTemplates = array_merge($existing, self::$staticTypoScript);
            $params['row']['include_static_file'] = implode(',', array_unique($staticTemplates));
        }

        if (self::$includeStaticTemplateFilesBeforeAllStaticTemplates) {
            /* Enfore "Static Template Files from TYPO3 Extensions:" to be
             * "Include before all static templates if root flag is set".
             * So that our typoscript can override typoscript from other extensions "ext_typoscript_setup.txt" */
            $params['row']['static_file_mode'] = 3;
        }
    }
}
