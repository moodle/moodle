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

class grade_grades_raw extends grade_object {
    
    /**
     * The DB table.
     * @var string $table
     */
    var $table = 'grade_grades_raw';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'scale');
    
    /**
     * The id of the grade_item this raw grade belongs to.
     * @var int $itemid
     */
    var $itemid;
    
    /**
     * The id of the user this raw grade belongs to.
     * @var int $userid
     */
    var $userid;
    
    /**
     * The grade value of this raw grade, if such was provided by the module.
     * @var float $gradevalue
     */
    var $gradevalue;
   
    /**
     * The scale value of this raw grade, if such was provided by the module.
     * @var int $scalevalue
     */
    var $scalevalue;

    /**
     * The maximum allowable grade when this grade was created.
     * @var float $grademax
     */
    var $grademax;

    /**
     * The minimum allowable grade when this grade was created.
     * @var float $grademin
     */
    var $grademin;

    /**
     * id of the scale, if this grade is based on a scale.
     * @var int $scaleid
     */
    var $scaleid;
   
    /**
     * A grade_scale object (referenced by $this->scaleid).
     * @var object $scale
     */
    var $scale;

    /**
     * The userid of the person who last modified this grade.
     * @var int $usermodified
     */
    var $usermodified;

    /**
     * Constructor. Extends the basic functionality defined in grade_object.
     * @param array $params Can also be a standard object.
     * @param boolean $fetch Wether or not to fetch the corresponding row from the DB.
     */
    function grade_grades_raw($params=NULL, $fetch=true) {
        $this->grade_object($params, $fetch);
        if (!empty($this->scaleid)) {
            $this->scale = new grade_scale(array('id' => $this->scaleid));
            $this->scale->load_items();
        }
    }


    /**
     * Finds and returns a grade_grades_raw object based on 1-3 field values.
     * @static
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_category object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($object = get_record('grade_grades_raw', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_grades_raw') {
                foreach ($object as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            } else {
                $object = new grade_grades_raw($object);
                return $object;
            }
        } else {
            return false;
        }
    } 

    /**
     * In addition to the normal updating set up in grade_object, this object also records
     * its pre-update value and its new value in the grade_history table.
     *
     * @param float $newgrade The new gradevalue of this object
     * @param string $howmodified What caused the modification? manual/module/import/cron...
     * @param string $note A note attached to this modification.
     * @return boolean Success or Failure.
     */
    function update($newgrade, $howmodified='manual', $note=NULL) {
        global $USER;
        $oldgrade = $this->gradevalue;
        $this->gradevalue = $newgrade;
        
        // Update this scaleid if it has changed (use the largest integer (most recent))
        if ($this->scale->id != $this->scaleid) {
            $this->scaleid = max($this->scale->id, $this->scaleid);
        }

        $result = parent::update();
        
        if ($result) {
            $logentry = new stdClass();
            $logentry->itemid = $this->itemid;
            $logentry->userid = $this->userid;
            $logentry->oldgrade = $oldgrade;
            $logentry->newgrade = $this->gradevalue;
            $logentry->note = $note;
            $logentry->howmodified = $howmodified;
            $logentry->timemodified = mktime();
            $logentry->usermodified = $USER->id;

            insert_record('grade_history', $logentry);
            return true;
        } else {
            return false;
        } 
    }
}

?>
