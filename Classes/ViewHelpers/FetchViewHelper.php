<?php
namespace Qbus\Qbtools\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;


/* **************************************************************
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
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /*
     * Create a QueryInterface for a given $className
     *
     * @param string $className
     * @return QueryInterface
     */
    protected function createQuery($className, $ignoreEnableFields)
    {
        $query = $this->objectManager->get(QueryInterface::class, $className);
        $querySettings = $this->objectManager->get(QuerySettingsInterface::class);

        $querySettings->setRespectStoragePage(false);
        if ($ignoreEnableFields === true) {
            $querySettings->setIgnoreEnableFields(true);
        }
        /* FIXME: Add storagePid parameter? */
        /*
        $querySettings->setStoragePageIds(\TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $storagePid));
         */
        $query->setQuerySettings($querySettings);

        return $query;
    }

    /*
     * Retrieve an extbase domain model in a Repository alike fashion.
     * Can be filtered by key-value pairs from $match.
     *
     * @param string $model
     * @param array $match
     * @param string $sortby
     * @param string $sortdirection
     */
    protected function fetchModels($model, $match, $limit, $sortby, $sortdirection, $ignoreEnableFields)
    {
        $query = $this->createQuery($model, $ignoreEnableFields);
        if (count($match) > 0) {
            $constraints = array();
            foreach ($match as $property => $value) {
                $constraints[] = $query->equals($property, $value);
            }

            $query->matching($query->logicalAnd($constraints));
        }

        $query->setOrderings(array($sortby =>
            ($sortdirection == 'DESC' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING)));

        if (intval($limit) > 0) {
            $query->setLimit(intval($limit));
        }

        return $query->execute();
    }

    /*
     * Maps a lowerCamelCase $property to a column name
     *
     * @param string $property
     * @return string
     */
    protected function propertyToColumn($property)
    {
        return preg_replace_callback('/[A-Z]/', function ($matches) {
            return '_' . lcfirst($matches[0]);
        }, lcfirst($property));
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
    protected function fetchRows($table, $match, $limit, $sortby, $sortdirection, $ignoreEnableFields)
    {
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        $groupBy = '';
        $orderBy = sprintf('`%s`.`%s` %s', $table, $this->propertyToColumn($sortby), ($sortdirection == 'DESC' ? 'DESC' : 'ASC'));
        $limit   = '';
        $where   = '1 ';
        foreach ($match as $key => $value) {
            $value = $GLOBALS['TYPO3_DB']->fullQuoteStr($value, $table);

            $where .= sprintf('AND `%s`.`%s` = %s ', $table, /*$this->propertyToColumn($key)*/$key, $value);
        }
        if ($ignoreEnableFields === false) {
            $where .= $cObj->enableFields($table);
        }

        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = 1;
        $data = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $table, $where, $groupBy, $orderBy, $limit);

        $entities = array();
        foreach ($data as $record) {
            $entities[] = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table, $record, $GLOBALS['TSFE']->sys_language_uid);
        }

        return $entities;
    }

    /**
     * @param string $table         = "tt_content"
     * @param string $model
     * @param array  $match         = array()
     * @param string $sortby        = "sorting"
     * @param string $sortdirection = "ASC"
     * @param string $limit         = ''
     * @param bool   $hidden        = false
     * @param string $as            = "entities"
     *
     * @return string
     */
    public function render($table = 'tt_content', $model = null, $match = array(),
                   $sortby = 'sorting', $sortdirection = 'ASC', $limit = '',
                   $hidden = false, $as = 'entities')
    {
        $entities = null;

        if (strlen($model) > 0) {
            $entities = $this->fetchModels($model, $match, $limit, $sortby, $sortdirection, $hidden);
        } else {
            $entities = $this->fetchRows($table, $match, $limit, $sortby, $sortdirection, $hidden);
        }

        $this->templateVariableContainer->add($as, $entities);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($as);

        return $content;
    }
}
