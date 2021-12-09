<?php
namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;


/* **************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Benjamin Franzke <bfr@qbus.de>, Qbus Internetagentur GmbH
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
 * ************************************************************* */

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CalculateBoundsViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('images', 'array', '', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return array<string,int>
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): array
    {
        $images = $arguments['images'];

        $result = array('minWidth' => 0, 'minHeight' => 0,
                'maxWidth' => 0, 'maxHeight' => 0,
                'minRatio' => 0, 'maxRatio'  => 0,
                'minRatioWidth' => 0, 'minRatioHeight'  => 0,
                'maxRatioWidth' => 0, 'maxRatioHeight'  => 0);

        foreach ($images as $image) {
            foreach (array('height', 'width') as $type) {
                $val = self::getCroppedProperty($image, $type);
                if ($val === null) {
                    continue;
                }

                $min = 'min' . ucfirst($type);
                $max = 'max' . ucfirst($type);

                if ($result[$min] == 0 || $result[$min] > $val) {
                    $result[$min] = $val;
                }

                if ($result[$max] < $val) {
                    $result[$max] = $val;
                }
            }
            $width = self::getCroppedProperty($image, 'width');
            $height = self::getCroppedProperty($image, 'height');
            if ($width === null || $height === null || $width == 0) {
                continue;
            }

            $ratio = $height / $width * 100;
            if ($result['minRatio'] == 0 || $ratio < $result['minRatio']) {
                $result['minRatio'] = $ratio;
                $result['minRatioWidth'] = $width;
                $result['minRatioHeight'] = $height;
            }
            if ($ratio > $result['maxRatio']) {
                $result['maxRatio'] = $ratio;
                $result['maxRatioWidth'] = $width;
                $result['maxRatioHeight'] = $height;
            }
        }

        return $result;
    }

    /**
     * When retrieving the height or width for a media file
     * a possible cropping needs to be taken into account.
     *
     * @param  FileInterface|FileReference $fileObject
     * @param  string                      $dimensionalProperty 'width' or 'height'
     * @return int
     */
    protected static function getCroppedProperty($fileObject, $dimensionalProperty): int
    {
        if ($fileObject instanceof FileReference) {
            $fileObject = $fileObject->getOriginalResource();
        }
        if (!$fileObject->hasProperty('crop') || empty($fileObject->getProperty('crop'))) {
            return (int) $fileObject->getProperty($dimensionalProperty);
        }

        if (class_exists(Typo3Version:class) || version_compare(TYPO3_branch, '8', '>=')) {
            $croppingConfiguration = $fileObject->getProperty('crop');
            $cropVariantCollection = \TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection::create((string)$croppingConfiguration);

            return (int) $cropVariantCollection->getCropArea('default')->makeAbsoluteBasedOnFile($fileObject)->asArray()[$dimensionalProperty];
        } else {
            $croppingConfiguration = json_decode($fileObject->getProperty('crop'), true);

            return (int)$croppingConfiguration[$dimensionalProperty];
        }
    }
}
