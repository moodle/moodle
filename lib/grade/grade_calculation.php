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
 * A calculation string used to compute the value displayed by a grade_item.
 * There can be only one grade_calculation per grade_item (one-to-one).
 */
class grade_calculation extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_calculations';

    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields');
    
    /**
     * A reference to the grade_item this calculation belongs to.
     * @var int $itemid
     */
    var $itemid;

    /**
     * The string representation of the calculation.
     * @var string $calculation
     */
    var $calculation;

    /**
     * The userid of the person who last modified this calculation.
     * @var int $usermodified
     */
    var $usermodified;
    
    /**
     * A formula parser object.
     * @var object $parser
     * @TODO implement parsing of formula and calculation MDL-9643
     */
    var $parser;

    /**
     * Applies the formula represented by this object to the value given, and returns the result.
     * @param float $oldvalue
     * @param string $valuetype Either 'gradevalue' or 'gradescale'
     * @return float result
     */
    function compute($oldvalue, $valuetype = 'gradevalue') {
        return $oldvalue; // TODO implement computation using parser
    }

    /**
     * Finds and returns a grade_calculation object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_calculation object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($grade_calculation = get_record('grade_calculations', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_calculation') {
                print_object($this);
                foreach ($grade_calculation as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            } else {
                $grade_calculation = new grade_calculation($grade_calculation);
                return $grade_calculation;
            }
        } else {
            return false;
        }
    }
}
?>
