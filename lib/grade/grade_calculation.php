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
class grade_calculation extends grade_object
{
    /**
     * The table name
     * @var string $table
     */
    var $table = 'grade_calculations';
    
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
     * Constructor.
     * @param object $params Object or array of variables=>values to assign to this object upon creation
     */
    function grade_calculation($params = null)
    {

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
    function get_record($static=false, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*")
    { 
        // In Moodle 2.0 (PHP5) we can replace table names with the static class var grade_calculation::$table
        if ($grade_calculation = get_record('grade_calculations', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if ($static) {
                $grade_calculation = new grade_calculation($grade_calculation);
                return $grade_calculation;
            } else {
                foreach ($grade_calculation as $param => $value) {
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
