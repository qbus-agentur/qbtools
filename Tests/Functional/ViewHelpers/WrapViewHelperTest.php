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

class WrapViewHelperTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/qbtools'];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['fluid'];

    public function testWrap()
    {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/wrap_viewhelper.html');

        $expected = '<empty></empty> <div class=""> Hallo </div>';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));
    }
}
