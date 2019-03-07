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
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;


class RenderContentViewHelperTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/qbtools'];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['fluid', 'fluid_styled_content'];

    protected function setUp()
    {
        parent::setUp();

        $this->importDataSet('PACKAGE:typo3/testing-framework/Resources/Core/Functional/Fixtures/pages.xml');
        $this->importDataSet('PACKAGE:typo3/testing-framework/Resources/Core/Functional/Fixtures/tt_content.xml');
        $this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/sys_template.xml');

        $this->initTypoScriptFrontendController(1);
    }

    public function testRenderContentByUid()
    {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/rendercontent_uid_viewhelper.html');
        $view->assign('id', 1);

        $expected = 'Test content';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));

        $cacheTags = $GLOBALS['TSFE']->getPageCacheTags();
        $this->assertContains('tt_content_1', $cacheTags);
    }

    public function testRenderContentByPid()
    {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/rendercontent_pid_viewhelper.html');
        $view->assign('id', 1);

        $expected = 'Test content';
        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));

        $cacheTags = $GLOBALS['TSFE']->getPageCacheTags();

        $this->assertContains('pages_1', $cacheTags);
        $this->assertContains('tt_content_pid_1', $cacheTags);
    }

    private function initTypoScriptFrontendController(int $uid): TypoScriptFrontendController
    {
        $typoScriptFrontendController = GeneralUtility::makeInstance(TypoScriptFrontendController::class, null, $uid, 0);
        $typoScriptFrontendController->cObj = new ContentObjectRenderer();
        $typoScriptFrontendController->cObj->setLogger(new NullLogger());
        $typoScriptFrontendController->sys_page = GeneralUtility::makeInstance(PageRepository::class);
        $typoScriptFrontendController->tmpl = GeneralUtility::makeInstance(TemplateService::class);
        $typoScriptFrontendController->getPageAndRootlineWithDomain(1);
        $typoScriptFrontendController->getConfigArray();
        $GLOBALS['TSFE'] = $typoScriptFrontendController;
        return $typoScriptFrontendController;
    }
}
