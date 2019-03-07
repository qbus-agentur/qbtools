<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Fluid\Tests\Functional\Hooks;

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

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ContentObjectGetDataHookTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/qbtools'];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['fluid'];

    public function testHook()
    {
        $flexform = <<<EOT
<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="settings.test">
                    <value index="vDEF">foobar</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>
EOT;

        $record = [
            'pi_flexform' => $flexform,
        ];
        $conf = [
            'data' => 'qbtools_flexform_field : settings.test',
        ];

        $typoScriptFrontendController = new TypoScriptFrontendController(null, 1, 0);
        $cObj = new ContentObjectRenderer($typoScriptFrontendController);
        $cObj->start($record, 'tt_content');
        $res = $cObj->cObjGetSingle('TEXT', $conf);

        $this->assertEquals('foobar', $res);
    }
}
