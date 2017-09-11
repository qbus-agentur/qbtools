<?php
namespace Qbus\Qbtools\ViewHelpers;

/**
 * WrapViewHelper
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class WrapViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @param string $class
     * @return string
     */
    public function render($class)
    {
        $content = $this->renderChildren();
        if (ctype_space($content) || $content === '') {
            return '';
        }

        return '<div class="' . htmlspecialchars($class) . '">' . $content . '</div>';
    }
}
