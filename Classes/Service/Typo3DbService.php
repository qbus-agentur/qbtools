<?php
namespace Qbus\Qbtools\Service;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Axel Wüstemann <awu@qbus.de>, Qbus Werbeagentur GmbH
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
 *
 * @package qbTools
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Typo3DbService
{

    /**
     * executes a raw sql select statement
     *
     * @param 	array	$sql parts of the select statement
     * @param	boolean	$toProperty whether the result array should have property names as key => default: TRUE
     * @return 	array	the queryResult
     */
    public static function executeSelect($sql, $toProperty = TRUE)
    {
        $queryResult = array();
        $res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($sql);
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
        {
            // transform row keys from column name to property name 
            $clonedRow = array();
            foreach ($row as $key => $column)
            {
                $key = ($toProperty) ? self::columnToProperty($key) : $key;
                $clonedRow[$key] = $column;
            }
            $queryResult[] = $clonedRow;
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        return $queryResult;
    }

    /**
     * executes a raw sql select statement
     *
     * @param 	string	$table table to be updated
     * @param 	string	$where where clause
     * @param 	string	$fields_values values
     * @return 	void
     */
    public static function executeUpdate($table, $where, $fields_values)
    {
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values);
    }

    /**
     * transforms a propertyName to a DB column name
     * 
     * @param string $propertyName
     * @return string
     */
    public static function propertyToColumn($propertyName)
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($propertyName);
    }

    /**
     * transforms a columnName to a property name
     * 
     * @param string $columnName
     * @return string
     */
    public static function columnToProperty($columnName)
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($columnName);
    }

}
?>