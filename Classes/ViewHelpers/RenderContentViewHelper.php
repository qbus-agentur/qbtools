<?php

namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RenderContentViewHelper extends AbstractViewHelper
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
        $this->registerArgument('uid', 'int', '', false, 0);
        $this->registerArgument('pid', 'int', '', false, 0);
        $this->registerArgument('colpos', 'int', '', false, 0);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $uid = $arguments['uid'];
        $pid = $arguments['pid'];
        $colpos = $arguments['colpos'];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $cObj = $configurationManager->getContentObject();

        $content = '';

        if ($uid > 0) {
            $conf = [
                'tables' => 'tt_content',
                'source' => $uid,
                'dontCheckPid' => 1,
            ];

            $GLOBALS['TSFE']->addCacheTags(['tt_content_' . $uid]);
            $content = $cObj->cObjGetSingle('RECORDS', $conf);
        } elseif ($pid > 0) {
            $conf = [
                'table' => 'tt_content',
                'select.' => [
                    'orderBy' => 'sorting',
                    'pidInList' => (string)$pid,
                    'where' => 'colPos=' . (int)$colpos,
                    'languageField' => 'sys_language_uid',
                ],
            ];

            // This requires EXT:autoflush to work
            $tags = [];
            foreach (explode(',', $pid) as $p) {
                $tags[] = 'tt_content_pid_' . $p;
                $tags[] = 'pages_' . $p;
            }
            $GLOBALS['TSFE']->addCacheTags($tags);
            $content = $cObj->cObjGetSingle('CONTENT', $conf);
        }

        return $content;
    }
}
