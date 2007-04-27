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

require_once('grade_object.php');

/**
 * Class representing a grade item. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_item extends grade_object
{
    /**
     * The table name
     * @var string $table
     */
    var $table = 'grade_items';

    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'required_fields', 'calculation');

    /**
     * Array of required fields (keys) and their default values (values).
     * @var array $required_fields
     */
    var $required_fields = array('gradetype'   => 0,
                                 'grademax'    => 100.00000,
                                 'grademin'    => 0.00000,
                                 'gradepass'   => 0.00000,
                                 'multfactor'  => 1.00000,
                                 'plusfactor'  => 0.00000,
                                 'sortorder'   => 0,
                                 'hidden'      => 0,
                                 'locked'      => 0,
                                 'needsupdate' => 0);
  
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
     * Info and notes about this item.
     * @var string $iteminfo
     */
    var $iteminfo;

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
     * Calculation string used for this item.
     * @var string $calculation
     */
    var $calculation;

    /**
     * Constructor
     * @param object $params an object with named parameters for this grade item.
     */       
    function grade_item($params=NULL) 
    {
        if (!empty($params) && (is_array($params) || is_object($params))) {
            foreach ($params as $param => $value) {
                if (in_object_vars($param, $this)) {
                    $this->$param = $value;
                }
            }
            $this->set_defaults();
        } 
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
     * @param boolean $fetch Whether to fetch the value from the DB or not (false == just use the object's value)
     * @return mixed $calculation A string if found, false otherwise.
     */
    function get_calculation($fetch = false)
    {
        if (!$fetch) {
            return $this->calculation;
        } 
        
        $grade_calculation = grade_calculation::get_record(true, 'itemid', $this->id);
        
        if (empty($grade_calculation)) { // There is no calculation in DB
            return false;
        } elseif ($grade_calculation->calculation != $this->calculation->calculation) { // The object's calculation is not in sync with the DB (new value??)
            $this->calculation = $grade_calculation;
            return $grade_calculation;
        } else { // The object's calculation is already in sync with the database
            return $this->calculation;
        }
    }

    /**
     * Sets this item's calculation (creates it) if not yet set, or
     * updates it if already set (in the DB). If no calculation is given,
     * the method will attempt to retrieve one from the Database, based on
     * the variables set in the current object.
     * @param string $calculation
     * @return boolean
     */
    function set_calculation($calculation = null)
    {
        if (empty($calculation)) { // We are setting this item object's calculation variable from the DB
            $grade_calculation = $this->get_calculation(true);
            if (empty($grade_calculation)) {
                return false;
            } else {
                $this->calculation = $grade_calculation;
            }
        } else { // We are updating or creating the calculation entry in the DB
            $grade_calculation = $this->get_calculation();
            
            if (empty($grade_calculation)) { // Creating
                $grade_calculation = new grade_calculation();
                $grade_calculation->calculation = $calculation;
                $grade_calculation->itemid = $this->id;

                if ($grade_calculation->insert()) {
                    $this->calculation = $grade_calculation;
                    return true;
                } else {
                    return false;
                }                
            } else { // Updating
                $grade_calculation->calculation = $calculation;
                $this->calculation = $grade_calculation;
                return $grade_calculation->update();
            }
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
?>
