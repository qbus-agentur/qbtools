<?php
namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

class CalculateBoundsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param array $images
     *
     * @return array
     */
    public function render($images)
    {
        $result = array('minWidth' => 0, 'minHeight' => 0,
                'maxWidth' => 0, 'maxHeight' => 0,
                'minRatio' => 0, 'maxRatio'  => 0,
                'minRatioWidth' => 0, 'minRatioHeight'  => 0,
                'maxRatioWidth' => 0, 'maxRatioHeight'  => 0);

        foreach ($images as $image) {
            foreach (array('height', 'width') as $type) {
                $val = $this->getCroppedProperty($image, $type);
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
            $width = $this->getCroppedProperty($image, 'width');
            $height = $this->getCroppedProperty($image, 'height');
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
    protected function getCroppedProperty($fileObject, $dimensionalProperty)
    {
        if ($fileObject instanceof FileReference) {
            $fileObject = $fileObject->getOriginalResource();
        }
        if (!$fileObject->hasProperty('crop') || empty($fileObject->getProperty('crop'))) {
            return $fileObject->getProperty($dimensionalProperty);
        }
        $croppingConfiguration = json_decode($fileObject->getProperty('crop'), true);

        return (int)$croppingConfiguration[$dimensionalProperty];
    }
}
