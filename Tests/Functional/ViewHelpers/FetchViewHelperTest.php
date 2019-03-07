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
        array $match,
        bool $ignoreEnableFields,
        string $expected,
        string $template
    ) {
        $view = new StandaloneView();
        $view->setTemplatePathAndFilename('typo3conf/ext/qbtools/Tests/Functional/ViewHelpers/Fixtures/' . $template . '.html');
        $view->assignMultiple([
            'from' => $from,
            'field' => $field,
            'sortby' => $sortby,
            'match' => $match,
            'ignoreEnableFields' => $ignoreEnableFields,
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
                'match' => [],
                'ignoreEnableFields' => false,
                'expected' => 'Main page, Sub page,',
                'template' => 'fetch_table_viewhelper',
            ],
            'fetch: model FrontendUser' => [
                'from' => \TYPO3\CMS\Extbase\Domain\Model\FrontendUser::class,
                'field' => 'username',
                'sortby' => 'uid',
                'match' => [],
                'ignoreEnableFields' => false,
                'expected' => 'testuser, testuser2,',
                'template' => 'fetch_model_viewhelper',
            ],
            'fetch: table pages in pid 1' => [
                'from' => 'pages',
                'field' => 'title',
                'sortby' => 'sorting',
                'match' => ['pid' => '1'],
                'ignoreEnableFields' => false,
                'expected' => 'Sub page,',
                'template' => 'fetch_table_viewhelper',
            ],
            'fetch: model FrontendUser with uid 2' => [
                'from' => \TYPO3\CMS\Extbase\Domain\Model\FrontendUser::class,
                'field' => 'username',
                'sortby' => 'uid',
                'match' => ['uid' => 2],
                'ignoreEnableFields' => false,
                'expected' => 'testuser2,',
                'template' => 'fetch_model_viewhelper',
            ],
            'fetch: table pages including hidden ones' => [
                'from' => 'pages',
                'field' => 'title',
                'sortby' => 'sorting',
                'match' => [],
                'ignoreEnableFields' => true,
                'expected' => 'Main page, Sub page,',
                'template' => 'fetch_table_viewhelper',
            ],
            'fetch: model FrontendUser including hidden ones' => [
                'from' => \TYPO3\CMS\Extbase\Domain\Model\FrontendUser::class,
                'field' => 'username',
                'sortby' => 'uid',
                'match' => [],
                'ignoreEnableFields' => true,
                'expected' => 'testuser, testuser2, testuser3,',
                'template' => 'fetch_model_viewhelper',
            ],
        ];
    }
}
