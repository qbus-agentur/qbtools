<?php
namespace Qbus\Qbtools\ViewHelpers;

class FalViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @param mixed $object
     * @param string $property
     * @param string $table (ignored if $object is a DomainObject)
     * @param string $as
     *
     * @return string
     */
    public function render($object, $property = "image", $table = "tt_content", $as = "files")
    {
        $uid = -1;
        $files = array();

        if (is_array($object)) {
            $uid = (int) $object['uid'];
            if ($object[$property] < 1) {
                $uid = -1;
            }
        } elseif (is_numeric($object)) {
            $uid = (int) $object;
        }

        if ($uid >= 0) {
            $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
            $files = $fileRepository->findByRelation($table, $property, $uid);
        }

        $this->templateVariableContainer->add($as, $files);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($as);

        return $content;
    }
}
