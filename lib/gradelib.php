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
function grade_get_items($courseid, $itemname=NULL, $itemtype=NULL, $itemmodule=NULL, $iteminstance=NULL, $itemnumber=NULL, $idnumber=NULL)
{
    $grade_item = new grade_item();
    $grade_item->courseid = $courseid;
    $grade_item->itemname = $itemname;
    $grade_item->itemtype = $itemtype;
    $grade_item->itemmodule = $itemmodule;
    $grade_item->iteminstance = $iteminstance;
    $grade_item->itemnumber = $itemnumber;
    $grade_item->id = $idnumber;

    $grade_items = $grade_item->get_records_select();
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
function grade_create_item($params)
{
    $grade_item = new grade_item($params);
    return $grade_item->insert();
}

/**
* For a given set of items, create a category to group them together (if one doesn't yet exist).
* Modules may want to do this when they are created. However, the ultimate control is in the gradebook interface itself.
* 
* @param string $fullname The name of the new category
* @param array $items An array of grade_items to group under the new category
* @param string $aggregation
* @return mixed New grade_category id if successful
*/
function grade_create_category($fullname, $items, $aggregation=GRADE_AGGREGATE_MEAN)
{
    $params = new stdClass();
    $params->fullname = $fullname;
    $params->items = $items;
    $params->aggregation = $aggregation;

    $grade_category = new grade_category($params);
    return $grade_category->insert();
}


/**
* Tells a module whether a grade (or grade_item if $userid is not given) is currently locked or not.
* This is a combination of the actual settings in the grade tables and a check on moodle/course:editgradeswhenlocked.
* If it's locked to the current use then the module can print a nice message or prevent editing in the module.
* 
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $userid ID of the user who owns the grade
* @return boolean Whether the grade is locked or not
*/
function grade_is_locked($itemtype, $itemmodule, $iteminstance, $userid=NULL)
{
    $grade_item = grade_item::get_record(true, 'itemtype', $itemtype, 'itemmodule', $itemmodule, 'iteminstance', $iteminstance);
    if ($grade_item) {
        return $grade_item->locked;
    } else {
        return null;
     }
} 

/**
 * Class representing a grade item. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_item
{
    /**
     * The table name
     * @var string $tablename
     */
    var $tablename = 'grade_items';

    /**
     * The grade_item PK.
     * @var int $id The grade_item PK
     */
    var $id;
    
    /**
     * The course this grade_item belongs to.
     * @var int $courseid
     */
    var $courseid;
    
    /**
     * The category this grade_item belongs to (optional).
     * @var int $categoryid 
     */
    var $categoryid;
    
    /**
     * The name of this grade_item (pushed by the module).
     * @var string $itemname
     */
    var $itemname;
    
    /**
     * e.g. 'mod', 'blocks', 'import', 'calculate' etc...
     * @var string $itemtype 
     */
    var $itemtype;
    
    /**
     * The module pushing this grade (e.g. 'forum', 'quiz', 'assignment' etc).
     * @var string $itemmodule
     */
    var $itemmodule;
    
    /**
     * ID of the item module
     * @var int $iteminstance
     */
    var $iteminstance;
    
    /**
     * Number of the item in a series of multiple grades pushed by an activity.
     * @var int $itemnumber
     */
    var $itemnumber;
    
    /**
     * The type of grade (0 = value, 1 = scale, 2 = text)
     * @var int $gradetype
     */
    var $gradetype;
    
    /**
     * Maximum allowable grade.
     * @var float $grademax
     */
    var $grademax;
    
    /**
     * Minimum allowable grade.
     * @var float $grademin
     */
    var $grademin;
    
    /**
     * The scale this grade is based on, if applicable.
     * @var object $scale
     */
    var $scale;
    
    /**
     * The Outcome this grade is associated with, if applicable.
     * @var object $outcome
     */
    var $outcome;
    
    /**
     * grade required to pass. (grademin < gradepass <= grademax)
     * @var float $gradepass
     */
    var $gradepass;
    
    /**
     * Multiply all grades by this number.
     * @var float $multfactor
     */
    var $multfactor;
    
    /**
     * Add this to all grades.
     * @var float $plusfactor
     */
    var $plusfactor;
    
    /**
     * Sorting order of the columns.
     * @var int $sortorder
     */
    var $sortorder;
    
    /**
     * Date until which to hide this grade_item. If null, 0 or false, grade_item is not hidden. Hiding prevents viewing.
     * @var int $hidden
     */
    var $hidden;
    
    /**
     * Date until which to lock this grade_item. If null, 0 or false, grade_item is not locked. Locking prevents updating.
     * @var int $locked
     */
    var $locked;
    
    /**
     * If set, the whole column will be recalculated, then this flag will be switched off.
     * @var boolean $needsupdate
     */
    var $needsupdate;
    
    /**
     * The first time this grade_item was created
     * @var int $timecreated
     */
    var $timecreated;
    
    /**
     * The last time this grade_item was modified
     * @var int $timemodified
     */
    var $timemodified;
    
    /**
     * Constructor
     * @param object $params an object with named parameters for this grade item.
     */     
    function grade_item($params=NULL) 
    {
        if (!empty($params) && (is_array($params) || is_object($params))) {
            foreach ($params as $param => $value) {
                if (method_exists($this, $param)) {
                    $this->$param = $value;
                }
            }
        } 
    }

    /**
     * Records this object in the Database.
     * @return int PK ID if successful, false otherwise
     */
    function insert()
    {
        return insert_record($this->table, $this, true);
    }
   

    /**
     * Deletes this object from the database.
     */
    function delete()
    {
        return delete_records($this->table, 'id', $this->id);
    }


    /**
     * Finds and returns a grade_item object based on its ID number.
     * 
     * @param int $id
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @return object grade_item object or false if none found.
     */
    function get_by_id($id, $static=false)
    {
        if ($static) {
            return grade_item::get_record(true, 'id', $id);
        } else {
            return $this->get_record(false, 'id', $id);
        }
    }
    

    /**
     * Finds and returns a grade_item object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_item object or false if none found.
     */
    function get_record($static=false, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*")
    { 
        // In Moodle 2.0 (PHP5) we can replace table names with the static class var grade_item::$table
        if ($grade_item = get_record('grade_items', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if ($static) {
                $grade_item = new grade_item($grade_item);
                return $grade_item;
            } else {
                foreach ($grade_item as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Uses the variables of this object to retrieve all matching objects from the DB.
     * @return array $objects
     */
    function get_records_select()
    {
        $variables = get_object_vars($this);
        $wheresql = '';
        
        foreach ($variables as $var => $value) {
            if (!empty($value)) {
                $wheresql .= " $var = '$value' AND ";
            }
        }
        
        // Trim trailing AND
        $wheresql = substr($wheresql, 0, strrpos($wheresql, 'AND'));

        return get_records_select($this->table, $wheresql);
    }
    
    /**
     * Returns the raw value for this grade item (as imported by module or other source).
     * 
     * @return mixed grades_Raw object if found, or false.
     */
    function get_raw()
    {
        $grade_raw = get_record('grade_grades_raw', 'itemid', $this->id);
        return $grade_raw; 
    }

    /**
     * Returns the final value for this grade item. 
     * 
     * @return mixed grades_Final object if found, or false.
     */
    function get_final()
    {
        $grade_final = get_record('grade_grades_final', 'itemid', $this->id);
        return $grade_final; 
    }

    /**
     * Returns this object's calculation.
     * @return mixed $calculation A string if found, false otherwise.
     */
    function get_calculation()
    {
        $grade_calculation = get_record('grade_calculations', 'itemid', $this->id);
        if ($grade_calculation) {
            return $grade_calculation->calculation;
        } else {
            return $grade_calculation;
        }
    }
    
    /**
    * Returns the grade_category object this grade_item belongs to (if any).
    * 
    * @return mixed grade_category object if applicable, NULL otherwise
    */
    function get_category()
    {
        if (!empty($this->categoryid)) {
            $grade_category = new grade_category($this->category_id);
            return $grade_category;
        } else {
            return null;
        }
    }
}

class grade_category
{
    /**
     * The table name
     * @var string $tablename
     */
    var $tablename = 'grade_categories';
    
    /**
     * The grade_category PK.
     * @var int $id The grade_category PK
     */
    var $id;
    
    /**
     * The course this category belongs to.
     * @var int $courseid
     */
    var $courseid;
    
    /**
     * The category this category belongs to (optional).
     * @var int $categoryid 
     */
    var $categoryid;
    
    /**
     * The name of this category.
     * @var string $fullname
     */
    var $fullname;
    
    /**
     * A constant pointing to one of the predefined aggregation strategies (none, mean, median, sum etc) .
     * @var int $aggregation 
     */
    var $aggregation;
    
    /**
     * Keep only the X highest items.
     * @var int $keephigh
     */
    var $keephigh;
    
    /**
     * Drop the X lowest items.
     * @var int $droplow
     */
    var $droplow;
    
    /**
     * Date until which to hide this category. If null, 0 or false, category is not hidden.
     * @var int $hidden
     */
    var $hidden;
    
    /**
     * Array of grade_items or grade_categories nested exactly 1 level below this category
     * @var array $children
     */
    var $children;
    
    /**
     * Constructor
     * @param object $params an object with named parameters for this category.
     */     
    function grade_category($params=NULL) 
    {
        if (!empty($params) && (is_array($params) || is_object($params))) {
            foreach ($params as $param => $value) {
                if (method_exists($this, $param)) {
                    $this->$param = $value;
                }
            }
        } 
    }

    /**
     * Records this object in the Database.
     * @return int PK ID if successful, false otherwise
     */
    function insert()
    {
        return insert_record($this->table, $this, true);
    }
   

    /**
     * Deletes this object from the database.
     */
    function delete()
    {
        return delete_records($this->table, 'id', $this->id);
    }

    /**
     * Finds and returns a grade_category object based on its ID number.
     * 
     * @param int $id
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @return object grade_category object or false if none found.
     */
    function get_by_id($id, $static=false)
    {
        if ($static) {
            return grade_category::get_record(true, 'id', $id);
        } else {
            return $this->get_record(false, 'id', $id);
        }
    }
    

    /**
     * Finds and returns a grade_category object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_category object or false if none found.
     */
    function get_record($static=false, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*")
    { 
        // In Moodle 2.0 (PHP5) we can replace table names with the static class var grade_category::$table
        if ($grade_category = get_record('grade_categories', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if ($static) {
                $grade_category = new grade_category($grade_category);
                return $grade_category;
            } else {
                foreach ($grade_category as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            }
        } else {
            return false;
        }
    }

} 
