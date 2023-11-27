<?php

namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Benjamin Franzke <bfr@qbus.de>, Qbus Werbeagentur GmbH
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
class FalViewHelper extends AbstractViewHelper
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
        $this->registerArgument('object', 'mixed', '', true);
        $this->registerArgument('property', 'string', '', false, 'image');
        $this->registerArgument('table', 'string', 'ignored if $object is a DomainObject', false, 'tt_content');
        $this->registerArgument('as', 'string', '', false, 'files');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $object = $arguments['object'];
        $property = $arguments['property'];
        $table = $arguments['table'];
        $as = $arguments['as'];

        $uid = -1;
        $files = [];

        if (is_array($object)) {
            $uid = (int)$object['uid'];
            if ($object[$property] < 1) {
                $uid = -1;
            }
        } elseif (is_numeric($object)) {
            $uid = (int)$object;
        }

        if ($uid >= 0) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $files = $fileRepository->findByRelation($table, $property, $uid);
        }

        $renderingContext->getVariableProvider()->add($as, $files);
        $content = $renderChildrenClosure();
        $renderingContext->getVariableProvider()->remove($as);

        return $content;
    }
}
