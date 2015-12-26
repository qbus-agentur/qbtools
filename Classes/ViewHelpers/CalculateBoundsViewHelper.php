<?php
namespace Qbus\Qbtools\ViewHelpers;

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
                $val = $image->getProperty($type);
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
            $width = $image->getProperty('width');
            $height = $image->getProperty('height');
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
}
