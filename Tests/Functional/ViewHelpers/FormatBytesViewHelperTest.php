<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Fluid\Tests\Functional\ViewHelpers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class FormatBytesViewHelperTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/qbtools'];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['fluid'];

    public function testFormatBytes()
    {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/formatbytes_viewhelper.html');

        $expected = '10, 100, 1 k, 1 k, 1 M, 1 G, 1 T';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));
    }
}
