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

use Psr\Log\NullLogger;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

//use TYPO3\CMS\Fluid\View\StandaloneView;
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

    public function testRenderExternalWithPluginName()
    {
        $this->initFrontendRendering(1);

        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/renderexternal_with_pluginname_viewhelper.html');
        $view->assign('partial', 'Foo/TestPartial');

        $expected = 'bar: foo';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));
    }

    public function testRenderExternalWithPluginNameAndToBeOverwrittenSettings()
    {
        $this->initFrontendRendering(1);

        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/renderexternal_with_pluginname_viewhelper.html');
        // These settings must not be passed to the external partial when pluginName is used
        $view->assign('settings', ['foo' => 'baz']);
        $view->assign('partial', 'Foo/TestPartial');

        $expected = 'bar: foo';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));
    }

    public function testRenderExternalWithPluginNameAndLinkRendering()
    {
        $this->initFrontendRendering(1);

        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/renderexternal_with_pluginname_viewhelper.html');
        // These settings must not be passed to the external partial when pluginName is used
        $view->assign('settings', ['foo' => 'baz']);
        $view->assign('partial', 'Foo/TestPartialWithLink');

        $expected = 'bar: index.php?id=1&amp;tx_testextension_pi1%5Baction%5D=action&amp;tx_testextension_pi1%5Bcontroller%5D=Dummy&amp;';
        $this->assertEquals($expected, trim(preg_replace('/cHash=.+/', '', preg_replace('/\s+/', ' ', $view->render()))));
    }

    private function initFrontendRendering(int $uid): TypoScriptFrontendController
    {
        $this->importDataSet('PACKAGE:typo3/testing-framework/Resources/Core/Functional/Fixtures/pages.xml');
        $this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/sys_template.xml');

        // Ensure that the Extbase UriBuilder generated links in frontend mode
        $environmentServiceProphecy = $this->prophesize(EnvironmentService::class);
        $environmentServiceProphecy->isEnvironmentInFrontendMode()->willReturn(true);
        $environmentServiceProphecy->isEnvironmentInBackendMode()->willReturn(false);
        $environmentServiceProphecy->isEnvironmentInCliMode()->willReturn(false);
        $environmentServiceProphecy->getServerRequestMethod()->willReturn('GET');
        GeneralUtility::setSingletonInstance(EnvironmentService::class, $environmentServiceProphecy->reveal());

        $typoScriptFrontendController = GeneralUtility::makeInstance(TypoScriptFrontendController::class, null, $uid, 0);
        $typoScriptFrontendController->cObj = new ContentObjectRenderer();
        // Remove condition once we drop support for TYPO3 v8, and always inject the logger
        if (method_exists($typoScriptFrontendController->cObj, 'setLogger')) {
            $typoScriptFrontendController->cObj->setLogger(new NullLogger());
        }
        $typoScriptFrontendController->sys_page = GeneralUtility::makeInstance(PageRepository::class);
        $typoScriptFrontendController->tmpl = GeneralUtility::makeInstance(TemplateService::class);
        // Remove guarded method call to init, once we drop support for TYPO3 v8
        if (method_exists($typoScriptFrontendController->tmpl, 'init')) {
            $typoScriptFrontendController->tmpl->init();
        }
        $typoScriptFrontendController->getPageAndRootlineWithDomain(1);
        $typoScriptFrontendController->getConfigArray();

        $GLOBALS['TSFE'] = $typoScriptFrontendController;
        return $typoScriptFrontendController;
    }
}
