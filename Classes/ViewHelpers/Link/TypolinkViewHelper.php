<?php
namespace Qbus\Qbtools\ViewHelpers\Link;

/**
 * TypolinkViewhelper
 *
 * Renders a link with the TypoLink function, to be used with the link wizard.
 *
 * All typolink arguments can be passed via the configuration array.
 * For a reference of available parameters see:
 * http://docs.typo3.org/typo3cms/TyposcriptReference/Functions/Typolink/Index.html
 *
 * Examples:
 * <qbtools:link.typolink configuration="{parameter: page.uid}" />
 * <qbtools:link.typolink configuration="{parameter: page.uid}" class="foo" />
 *
 * TODO: Add page cache tag for the rendered pages
 */
class TypolinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('configuration', 'array', 'The typoLink configuration', true);
        $this->registerArgument('class', 'string', 'A class attribute that\'s merged into the ATagParams key of the configuration', false);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $configuration = $this->arguments['configuration'];
        if (isset($this->arguments['class'])) {
            $class = 'class="' . htmlentities($this->arguments['class']) . '"';

            if (isset($configuration['ATagParams'])) {
                $configuration['ATagParams']  .= ' ' . $class;
            } else {
                $configuration['ATagParams']  =  $class;
            }

        }
        return $GLOBALS['TSFE']->cObj->typoLink($this->renderChildren(), $configuration);
    }
}
