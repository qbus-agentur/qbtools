<?php
namespace Qbus\Qbtools\ViewHelpers;

/**
 * CacheTagViewHelper
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CacheTagViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param string $tag
     */
    public function render($tag)
    {
        $GLOBALS['TSFE']->addCacheTags([$tag]);
        return '';
    }

}
