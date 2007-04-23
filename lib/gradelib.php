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
 * Library of functions for Gradebook
 *
 * @author Moodle HQ developers
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/**
* Extracts from the Gradebook all the Grade Items attached to the calling object. 
* For example, an assignment may want to retrieve all the grade_items for itself, 
* and get three outcome scales in return. This will affect the grading interface.
*
* Note: Each parameter refines the search. So if you only give the courseid,
*       all the grade_items for this course will be returned. If you add the
*       itemtype 'mod', all grade_items for this courseif AND for the 'mod'
*       type will be returned, etc...
* 
* @param int $courseid The id of the course to which the Grade Items belong
* @param string $itemname The name of the grade item
* @param string $itemtype 'mod', 'blocks', 'import', 'calculated' etc
* @param string $itemmodule 'forum, 'quiz', 'csv' etc
* @param int $iteminstance id of the item module
* @param int $itemnumber Can be used to distinguish multiple grades for an activity
* @param int $idnumber Grade Item Primary Key
* @return array An array of Grade Items
*/
function grade_get_items($courseid, $itemname=NULL, $itemtype=NULL, $itemmodule=NULL, $iteminstance=NULL, $itemnumber=NULL, $idnumber=NULL)
{

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
    $grade_item = new Grade_Item($params);
    return $grade_item->record();
}

/**
* For a given set of items, create a category to group them together (if one doesn't yet exist).
* Modules may want to do this when they are created. However, the ultimate control is in the gradebook interface itself.
* 
* @param string $fullname The name of the new Category
* @param array $items An array of grade_items to group under the new Category
* @param string $aggregation
* @return mixed New grade_category id if successful
*/
function grade_create_category()
{

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

} 

/**
 * Class representing a Grade Item. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class Grade_Item
{
    /**
     * The table name
     * @var string $tablename
     */
    var $tablename = 'grade_items';

    /**
     * The Grade_Item PK.
     * @var int $id The Grade_Item PK
     */
    var $id;
    
    /**
     * The course this Grade_Item belongs to.
     * @var int $courseid
     */
    var $courseid;
    
    /**
     * The Category this Grade_Item belongs to (optional).
     * @var int $categoryid 
     */
    var $categoryid;
    
    /**
     * The name of this Grade_Item (pushed by the module).
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
     * Grade required to pass. (grademin < gradepass <= grademax)
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
     * Date until which to hide this Grade_Item. If null, 0 or false, Grade_Item is not hidden. Hiding prevents viewing.
     * @var int $hidden
     */
    var $hidden;
    
    /**
     * Date until which to lock this Grade_Item. If null, 0 or false, Grade_Item is not locked. Locking prevents updating.
     * @var int $locked
     */
    var $locked;
    
    /**
     * If set, the whole column will be recalculated, then this flag will be switched off.
     * @var boolean $needsupdate
     */
    var $needsupdate;
    
    /**
     * The first time this Grade_Item was created
     * @var int $timecreated
     */
    var $timecreated;
    
    /**
     * The last time this Grade_Item was modified
     * @var int $timemodified
     */
    var $timemodified;
    
    /**
     * Constructor
     */
    function Grade_Item()
    {
        global $CFG;
        $this->tablename = $CFG->prefix . $this->tablename;
    }

    /**
     * Inserts the Grade Item object as a new record in the grade_items table.
     */
    function record()
    {
        
    }

    function get_raw()
    {
        $grade_raw = get_record('grade_grades_raw', 'itemid', $this->id);
        return $grade_raw; 
    }

    /**
     * Returns the raw value for this grade item (as imported by module or other source).
     * 
     * @return mixed Grades_Raw object if found, or false.
     */
    function get_final()
    {

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
    * Returns the Grade_Category object this Grade_Item belongs to (if any).
    * 
    * @return mixed Grade_Category object if applicable, NULL otherwise
    */
    function get_category()
    {
        if (!empty($this->categoryid)) {
            $grade_category = new Grade_Category($this->category_id);
            return $grade_category;
        } else {
            return null;
        }
    }
}

class Grade_Category
{
    /**
     * The table name
     * @var string $tablename
     */
    var $tablename = 'grade_categories';
    /**
     * The Grade_Category PK.
     * @var int $id The Grade_Category PK
     */
    var $id;
    /**
     * The course this Category belongs to.
     * @var int $courseid
     */
    var $courseid;
    /**
     * The Category this Category belongs to (optional).
     * @var int $categoryid 
     */
    var $categoryid;
    /**
     * The name of this Category.
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
     * Multiply total grade by this number.
     * @var float $multfactor
     */
    var $multfactor;
    /**
     * Add this to total grade.
     * @var float $plusfactor
     */
    var $plusfactor;
    /**
     * What final grade needs to be achieved to pass this item?
     * @var float $gradepass
     */
    var $gradepass;
    /**
     * Date until which to hide this Category. If null, 0 or false, Category is not hidden.
     * @var int $hidden
     */
    var $hidden;
    
    
    /**
     * Constructor
     * @param object $params an object with named parameters for this category.
     */     
    function Grade_Category($params=NULL) 
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
     * Finds and returns a Grade_Category object based on its ID number.
     * 
     * @param int $id
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @return object Grade_Category object or false if none found.
     */
    function get_by_id($id, $static=false)
    {
        if ($static) {
            return Grade_Category::get_record(true, 'id', $id);
        } else {
            return $this->get_record(false, 'id', $id);
        }
    }
    

    /**
     * Finds and returns a Grade_Category object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object Grade_Category object or false if none found.
     */
    function get_record($static=false, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*")
    { 
        if ($grade_category = get_record('grade_categories', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if ($static) {
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
