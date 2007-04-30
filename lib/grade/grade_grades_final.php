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

class grade_grades_final extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_grades_final';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields');
    
    /**
     * The id of the grade_item this final grade belongs to.
     * @var int $itemid
     */
    var $itemid;
    
    /**
     * The id of the user this final grade belongs to.
     * @var int $userid
     */
    var $userid;
    
    /**
     * The value of the grade.
     * @var float $gradevalue
     */
    var $gradevalue;
    
    /**
     * The scale of this grade.
     * @var int $gradescale
     */
    var $gradescale;
    
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
     * 0 if not exported, > 1 is the last exported date.
     * @var int $exported
     */
    var $exported;

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
    function grade_grades_final($params=NULL, $fetch=true) {
        $this->grade_object($params, $fetch);
    }


    /**
     * Finds and returns a grade_grades_final object based on 1-3 field values.
     *
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
        if ($object = get_record('grade_grades_final', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (!isset($this)) {
                $object = new grade_grades_final($object);
                return $object;
            } else {
                foreach ($object as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            }
        } else {
            return false;
        }
    } 
}

?>
