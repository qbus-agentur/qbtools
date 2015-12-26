<?php
namespace Qbus\Qbtools\ViewHelpers;

class RenderContentViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /*
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;


    /**
     * @var Content Object
     */
    protected $cObj;

        /**
         * Parse a content element
         *
         * @param       int             UID of any content element
         * @param       int             PID of content elements to be rendered
         * @param       int             colpos of content elements
         * @return string Parsed Content Element
         */
        public function render($uid = 0, $pid = 0, $colpos = 0)
        {
            if ($uid > 0) {
                $conf = array(
                'tables' => 'tt_content',
                'source' => $uid,
                'dontCheckPid' => 1
            );

                return $this->cObj->RECORDS($conf);
            } elseif ($pid > 0) {
                $conf = array(
                                'table' => 'tt_content',
                                'select.' => array(
                                        'orderBy' => 'sorting',
                                        'pidInList' => (string) $pid,
                                        'where' => 'colPos=' . $colpos,
                                        'languageField' => 'sys_language_uid',
                                ),
                        );

                return $this->cObj->CONTENT($conf);
            }

            return '';
        }

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     *
     * @return void
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->cObj = $this->configurationManager->getContentObject();
    }
}
