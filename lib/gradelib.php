<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Library of functions for gradebook
 *
 * @author Moodle HQ developers
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

define('GRADE_AGGREGATE_MEAN', 0);
define('GRADE_AGGREGATE_MEDIAN', 1);
define('GRADE_AGGREGATE_SUM', 2);
define('GRADE_AGGREGATE_MODE', 3);

require_once($CFG->libdir . '/grade/grade_category.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_calculation.php');

/**
* Extracts from the gradebook all the grade items attached to the calling object. 
* For example, an assignment may want to retrieve all the grade_items for itself, 
* and get three outcome scales in return. This will affect the grading interface.
*
* Note: Each parameter refines the search. So if you only give the courseid,
*       all the grade_items for this course will be returned. If you add the
*       itemtype 'mod', all grade_items for this courseif AND for the 'mod'
*       type will be returned, etc...
* 
* @param int $courseid The id of the course to which the grade items belong
* @param string $itemname The name of the grade item
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $itemnumber Can be used to distinguish multiple grades for an activity
* @param int $idnumber grade item Primary Key
* @return array An array of grade items
*/
function grade_get_items($courseid, $itemname=NULL, $itemtype=NULL, $itemmodule=NULL, $iteminstance=NULL, $itemnumber=NULL, $idnumber=NULL) {
    $grade_item = new grade_item(compact('courseid', 'itemname', 'itemtype', 'itemmodule', 'iteminstance', 'itemnumber', 'idnumber'), false);
    $grade_items = $grade_item->fetch_all_using_this();
    return $grade_items;
}


/**
* Creates a new grade_item in case it doesn't exist. This function would be called when a module
* is created or updates, for example, to ensure grade_item entries exist.
* It's not essential though--if grades are being added later and a matching grade_item doesn't
* yet exist, the gradebook will create them on the fly.
* 
* @param 
* @return mixed New grade_item id if successful
*/
function grade_create_item($params) {
    $grade_item = new grade_item($params);
    return $grade_item->insert();
}

/**
* For a given set of items, create a category to group them together (if one doesn't yet exist).
* Modules may want to do this when they are created. However, the ultimate control is in the gradebook interface itself.
*
* @param int $courseid
* @param string $fullname The name of the new category
* @param array $items An array of grade_items to group under the new category
* @param string $aggregation
* @return mixed New grade_category id if successful
*/
function grade_create_category($courseid, $fullname, $items, $aggregation=GRADE_AGGREGATE_MEAN) {
    $grade_category = new grade_category(compact('courseid', 'fullname', 'items', 'aggregation'));
    return $grade_category->insert();
}


/**
* Tells a module whether a grade (or grade_item if $userid is not given) is currently locked or not.
* This is a combination of the actual settings in the grade tables and a check on moodle/course:editgradeswhenlocked.
* If it's locked to the current use then the module can print a nice message or prevent editing in the module.
* If no $userid is given, the method will always return the grade_item's locked state.
* If a $userid is given, the method will first check the grade_item's locked state (the column). If it is locked,
* the method will return true no matter the locked state of the specific grade being checked. If unlocked, it will 
* return the locked state of the specific grade.
*
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $itemnumber Optional number of the item to check
* @param int $userid ID of the user who owns the grade
* @return boolean Whether the grade is locked or not
*/
function grade_is_locked($itemtype, $itemmodule, $iteminstance, $itemnumber=NULL, $userid=NULL) {
    $grade_item = new grade_item(compact('itemtype', 'itemmodule', 'iteminstance', 'itemnumber'));
    return $grade_item->is_locked($userid);
} 

?>
