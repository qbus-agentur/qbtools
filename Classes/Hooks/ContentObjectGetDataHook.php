<?php
namespace Qbus\Qbtools\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectGetDataHookInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ContentObjectGetDataHook implements ContentObjectGetDataHookInterface
{
    /**
     * Extends the getData()-Method of \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer to process more/other commands
     *
     * @param  string                $getDataString Full content of getData-request e.g. "TSFE:id // field:title // field:uid
     * @param  array                 $fields        Current field-array
     * @param  string                $sectionValue  Currently examined section value of the getData request e.g. "field:title
     * @param  string                $returnValue   Current returnValue that was processed so far by getData
     * @param  ContentObjectRenderer $parentObject  Parent content object
     * @return string                Get data result
     */
    public function getDataExtension(
        $getDataString,
        array $fields,
        $sectionValue,
        $returnValue,
        ContentObjectRenderer &$parentObject
    ) {
        $parts = explode(':', $sectionValue, 2);
        $type = strtolower(trim($parts[0]));
        $key = trim($parts[1]);

        switch ($type) {
            case 'qbtools_flexform_field':
                $flexform_service = GeneralUtility::makeInstance(FlexFormService::class);
                $flexform_fields = $flexform_service->convertFlexFormContentToArray($fields['pi_flexform']);
                $returnValue = ArrayUtility::getValueByPath($flexform_fields, $key, '.');
                break;
            }

        return $returnValue;
    }
}
