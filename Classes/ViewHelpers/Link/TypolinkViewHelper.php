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
 *
 */
class TypolinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    public function initializeArguments()
    {
        $this->registerArgument('configuration', 'array', 'The typoLink configuration', true);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $GLOBALS['TSFE']->cObj->typoLink($this->renderChildren(), $this->arguments['configuration']);
    }
}
