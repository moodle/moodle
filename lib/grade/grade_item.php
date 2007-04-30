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
class grade_item extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_items';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'calculation', 'grade_grades_raw');
  
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
    var $locked = false;
    
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
     * Array of grade_grades_raw objects linked to this grade_item. They are indexed by userid.
     * @var array $grade_grades_raw
     */
    var $grade_grades_raw = array();

    /**
     * Finds and returns a grade_item object based on 1-3 field values.
     *
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_item object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($grade_item = get_record('grade_items', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (!isset($this)) {
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
     * Returns the raw values for this grade item (as imported by module or other source).
     * @param int $userid Optional: to retrieve a single raw grade
     * @return mixed An array of all raw_grades (stdClass objects) for this grade_item, or a single raw_grade.
     */
    function get_raw($userid=NULL) {
        if (empty($this->grade_grades_raw)) {
            $this->load_raw();
        }

        $grade_raw_array = null;
        if (!empty($userid)) {
            $r = get_record('grade_grades_raw', 'itemid', $this->id, 'userid', $userid);
            $grade_raw_array[$r->userid] = new grade_grades_raw($r);
        } else {
            $grade_raw_array = $this->grade_grades_raw;
        }
        return $grade_raw_array;
    }

    /**
     * Loads all the grade_grades_raw objects for this grade_item from the DB into grade_item::$grade_grades_raw array.
     * @return array grade_grades_raw objects
     */      
    function load_raw() {
        $grade_raw_array = get_records('grade_grades_raw', 'itemid', $this->id);
        foreach ($grade_raw_array as $r) {
            $this->grade_grades_raw[$r->userid] = new grade_grades_raw($r);
        }
        return $this->grade_grades_raw;
    }

    /**
     * Returns the final values for this grade item (as imported by module or other source).
     * @param int $userid Optional: to retrieve a single final grade
     * @return mixed An array of all final_grades (stdClass objects) for this grade_item, or a single final_grade.
     */
    function get_final($userid=NULL) {
        if (empty($this->grade_grades_final)) {
            $this->load_final();
        }

        $grade_final_array = null;
        if (!empty($userid)) {
            $f = get_record('grade_grades_final', 'itemid', $this->id, 'userid', $userid);
            $grade_final_array[$f->userid] = new grade_grades_final($f);
        } else {
            $grade_final_array = $this->grade_grades_final;
        }
        return $grade_final_array;
    }

    /**
     * Loads all the grade_grades_final objects for this grade_item from the DB into grade_item::$grade_grades_final array.
     * @return array grade_grades_final objects
     */      
    function load_final() {
        $grade_final_array = get_records('grade_grades_final', 'itemid', $this->id);
        foreach ($grade_final_array as $f) {
            $this->grade_grades_final[$f->userid] = new grade_grades_final($f);
        }
        return $this->grade_grades_final;
    }

    /**
     * Returns this object's calculation.
     * @param boolean $fetch Whether to fetch the value from the DB or not (false == just use the object's value)
     * @return mixed $calculation A string if found, false otherwise.
     */
    function get_calculation($fetch = false) {
        if (!$fetch) {
            return $this->calculation;
        } 
        
        $grade_calculation = grade_calculation::fetch(true, 'itemid', $this->id);
        
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
    function set_calculation($calculation = null) {
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
                $grade_calculation = new grade_calculation($grade_calculation);
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
    function get_category() {
        if (!empty($this->categoryid)) {
            return grade_category::fetch('id', $this->categoryid);
        } else {
            return null;
        }
    }

    /**
     * Returns the locked state of this grade_item (if the grade_item is locked OR no specific
     * $userid is given) or the locked state of a specific grade within this item if a specific
     * $userid is given and the grade_item is unlocked.
     *
     * @param int $userid
     * @return boolean Locked state
     */
    function is_locked($userid=NULL) {
        if ($this->locked || empty($userid)) {
            return $this->locked; // This could be true or false (false only if no $userid given)
        } else {
            $final = $this->get_final($userid);
            return $final->locked;
        }
    }

    /**
     * Performs the necessary calculations on the grades_final referenced by this grade_item,
     * and stores the results in grade_grades_final. Performs this for only one userid if 
     * requested. Also resets the needs_update flag once successfully performed.
     *
     * @param int $userid
     * @param string $howmodified What caused the modification? manual/module/import/cron...
     * @param string $note A note attached to this modification.
     * @return boolean Success or failure
     */
    function update_final_grade($userid=NULL, $howmodified='manual', $note=NULL) {
        if (empty($this->grade_grades_final)) {
            $this->load_final();
        }
        
        // TODO implement parsing of formula and calculation MDL-9643
        foreach ($this->grade_grades_final as $f) {
            $newgradevalue = 0; // TODO replace '0' with calculated value
            $f->update($newgradevalue, $howmodified, $note);
        }

        return true;
    }
}
?>
