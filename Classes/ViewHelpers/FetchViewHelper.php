<?php
namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;


/* **************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Benjamin Franzke <bfr@qbus.de>, Qbus Internetagentur GmbH
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
 *
 * Examples:
 *
 * Fetches all QbContacts
 * <qbtools:fetch model="Qbus\\Qbcontact\\Domain\\Model\\Contact" as="contacts">
 * 	<f:for each="{contacts}" as="contact">
 * 		<f:debug>{contact}</f:debug>
 * 	</f:for>
 * </qbtools:fetch>
 *
 * Fetches the record uid from table tt_content
 * <qbtools:fetch match="{uid: 5}">
 * 	...
 * </qbtools>
 *
 * <qbtools:fetch table="sys_category">
 * 	<f:for each="{entities}" as="category">
 * 		<f:debug>{category}</f:debug>
 * 	</f:for>
 * </qbtools>
 */
class FetchViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /*
     * Create a QueryInterface for a given $className
     *
     * @param string $className
     * @param bool $ignoreEnableFields
     * @return QueryInterface
     */
    protected static function createQuery(string $className, bool $ignoreEnableFields): QueryInterface
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $query = $objectManager->get(QueryInterface::class, $className);
        $querySettings = $objectManager->get(QuerySettingsInterface::class);

        $querySettings->setRespectStoragePage(false);
        if ($ignoreEnableFields === true) {
            $querySettings->setIgnoreEnableFields(true);
        }
        $query->setQuerySettings($querySettings);

        return $query;
    }

    /*
     * Retrieve an extbase domain model in a Repository alike fashion.
     * Can be filtered by key-value pairs from $match.
     *
     * @param string $model
     * @param array $match
     * @param string $limit
     * @param string $sortby
     * @param string $sortdirection
     * @param bool $ignoreEnableFields
     */
    protected static function fetchModels(string $model, array $match, string $limit, string $sortby, string $sortdirection, bool $ignoreEnableFields)
    {
        $query = self::createQuery($model, $ignoreEnableFields);
        if (count($match) > 0) {
            $constraints = array();
            foreach ($match as $property => $value) {
                $constraints[] = $query->equals($property, $value);
            }

            $query->matching($query->logicalAnd($constraints));
        }

        $query->setOrderings([
            $sortby => ($sortdirection === 'DESC' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING)
        ]);

        if (intval($limit) > 0) {
            $query->setLimit(intval($limit));
        }

        return $query->execute();
    }

    /*
     * Retrieve an extbase domain model in a Repository alike fashion.
     * Can be filtered by key-value pairs from $match.
     *
     * @param string $model
     * @param array  $match
     * @param string $sortby
     * @param string $sortdirection
     */
    protected static function fetchRows($table, $match, $limit, $sortby, $sortdirection, $ignoreEnableFields)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        if ($ignoreEnableFields === false) {
            $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
        }

        $whereConditions = [];

        $languageField = $GLOBALS['TCA'][$table]['ctrl']['languageField'] ?? null;
        if ($languageField !== null) {
            $whereConditions[] = $queryBuilder->expr()->in('sys_language_uid', $queryBuilder->createNamedParameter([0, -1], Connection::PARAM_INT_ARRAY));
        }

        foreach ($match as $key => $value) {
            $whereConditions[] = $queryBuilder->expr()->eq($key, $queryBuilder->createNamedParameter($value, \PDO::PARAM_STR));
        }

        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->andX(...$whereConditions)
            );

        if (intval($limit) > 0) {
            $queryBuilder->setMaxresults(intval($limit));
        }

        $queryBuilder->addOrderBy($sortby, $sortdirection === 'DESC' ? 'DESC' : 'ASC');

        $result = $queryBuilder->execute();

        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $entities = [];
        while ($row = $result->fetch()) {
            if ($table === 'pages') {
                $row = $pageRepository->getPageOverlay($row);
            } else {
                $row = $pageRepository->getRecordOverlay($table, $row, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
            }

            $entities[] = $row;
        }

        return $entities;
    }

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('table', 'string', 'e.g. "tt_content"', false, 'tt_content');
        $this->registerArgument('model', 'string', 'Vendor\Ext\Domain\Model\Foo', false, null);
        $this->registerArgument('match', 'array', '', false, []);
        $this->registerArgument('sortby', 'string', 'column/property to sort by', false, 'sorting');
        $this->registerArgument('sortdirection', 'string', 'ASC or DESC', false, 'ASC');
        $this->registerArgument('limit', 'string', '', false, '');
        $this->registerArgument('hidden', 'bool', '', false, false);
        $this->registerArgument('as', 'string', 'variable to render the result into', false, 'entities');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $entities = null;

        $table = (string)$arguments['table'];
        $model = $arguments['model'] ?? '';
        $match = $arguments['match'];
        $sortby = $arguments['sortby'];
        $sortdirection = $arguments['sortdirection'];
        $limit = $arguments['limit'];
        $hidden = $arguments['hidden'];
        $as = $arguments['as'];

        if (strlen($model) > 0) {
            $entities = self::fetchModels($model, $match, $limit, $sortby, $sortdirection, $hidden);
        } else {
            $entities = self::fetchRows($table, $match, $limit, $sortby, $sortdirection, $hidden);
        }

        $renderingContext->getVariableProvider()->add($as, $entities);
        $content = $renderChildrenClosure();
        $renderingContext->getVariableProvider()->remove($as);

        return $content;
    }
}
