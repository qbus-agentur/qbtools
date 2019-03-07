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

class FetchViewHelperTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/qbtools'];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['fluid'];

    protected function setUp()
    {
        parent::setUp();

        $this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/pages.xml');
        $this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/fe_groups.xml');
        $this->importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/fe_users.xml');
    }

    /**
     * @dataProvider renderFetchesDataProvider
     */
    public function testFetchesData(
        string $from,
        string $field,
        string $sortby,
        string $expected,
        string $template
    ) {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/' . $template . '.html');
        $view->assignMultiple([
            'from' => $from,
            'field' => $field,
            'sortby' => $sortby,
        ]);

        if (version_compare(TYPO3_branch, '9', '<')) {
            $typoScriptFrontendController = new \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController(null, $uid, 0);
            $typoScriptFrontendController->gr_list = '';
            $GLOBALS['TSFE'] = $typoScriptFrontendController;
        }

        $this->assertEquals($expected, trim(preg_replace('/\s+/', ' ', $view->render())));
    }

    /**
     * @return array
     */
    public function renderFetchesDataProvider(): array
    {
        return [
            'fetch: table pages' => [
                'from' => 'pages',
                'field' => 'title',
                'sortby' => 'sorting',
                'expected' => 'Main page, Sub page,',
                'template' => 'fetch_table_viewhelper',
            ],
            'fetch: model FrontendUser' => [
                'from' => \TYPO3\CMS\Extbase\Domain\Model\FrontendUser::class,
                'field' => 'username',
                'sortby' => 'uid',
                'expected' => 'testuser, testuser2,',
                'template' => 'fetch_model_viewhelper',
            ],
        ];
    }
}
