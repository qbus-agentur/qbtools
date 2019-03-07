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

class RenderExternalViewHelperTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/qbtools',
        'typo3conf/ext/qbtools/Tests/Functional/Fixtures/Extensions/test_extension',
    ];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['fluid'];

    public function testRenderExternal()
    {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/renderexternal_viewhelper.html');

        $expected = 'bar:';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));
    }

    public function testRenderExternalWithSettings()
    {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/renderexternal_viewhelper.html');
        $view->assign('settings', ['foo' => 'baz']);

        $expected = 'bar: baz';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));
    }
}
