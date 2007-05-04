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
 * Class representing a grade history. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_history extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_history';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields');
  
    /**
     * The grade_item whose raw grade is being changed.
     * @var int $itemid
     */
    var $itemid;
    
    /**
     * The user whose raw grade is being changed.
     * @var int $userid
     */
    var $userid;
    
    /**
     * The value of the grade before the change.
     * @var float $oldgrade
     */
    var $oldgrade;

    /**
     * The value of the grade after the change.
     * @var float $newgrade
     */
    var $newgrade;

    /**
     * An optional annotation to explain the change.
     * @var string $note
     */
    var $note;
    
    /**
     * How the grade was modified ('manual', 'module', 'import' etc...).
     * @var string $howmodified
     */
    var $howmodified;
    
    /**
     * Finds and returns a grade_history object based on 1-3 field values.
     *
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_history object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($grade_history = get_record('grade_history', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_history') {
                foreach ($grade_history as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            } else {
                $grade_history = new grade_history($grade_history);
                return $grade_history;
            }
        } else {
            return false;
        }
    } 

    /**
     * Given a grade_grades_raw object and some other parameters, records the 
     * change of grade value for this object, and associated data.
     * @static
     * @param object $grade_raw
     * @param float $oldgrade
     * @param string $note
     * @param string $howmodified
     * @return boolean Success or Failure
     */
    function insert_change($grade_raw, $oldgrade, $howmodified='manual', $note=NULL) {
        global $USER;
        $history = new grade_history();
        $history->itemid = $grade_raw->itemid;
        $history->userid = $grade_raw->userid;
        $history->oldgrade = $oldgrade;
        $history->newgrade = $grade_raw->gradevalue;
        $history->note = $note;
        $history->howmodified = $howmodified;
        $history->timemodified = mktime();
        $history->usermodified = $USER->id;

        return $history->insert();
    } 
}
?>
