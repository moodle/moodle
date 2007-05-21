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
 * Class representing a grade outcome. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_outcome extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_outcomes';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'scale');
  
    /**
     * The course this outcome belongs to.
     * @var int $courseid
     */
    var $courseid;
    
    /**
     * The shortname of the outcome.
     * @var string $shortname
     */
    var $shortname;

    /**
     * The fullname of the outcome.
     * @var string $fullname
     */
    var $fullname;

    /**
     * A full grade_scale object referenced by $this->scaleid.
     * @var object $scale
     */
    var $scale;

    /**
     * The id of the scale referenced by this outcome.
     * @var int $scaleid
     */
    var $scaleid;
    
    /**
     * The userid of the person who last modified this outcome.
     * @var int $usermodified
     */
    var $usermodified;
    
    /**
     * Constructor. Extends the basic functionality defined in grade_object.
     * @param array $params Can also be a standard object.
     * @param boolean $fetch Wether or not to fetch the corresponding row from the DB.
     */
    function grade_outcome($params=NULL, $fetch=true) {
        $this->grade_object($params, $fetch);
        if (!empty($this->scaleid)) {
            $this->scale = new grade_scale(array('id' => $this->scaleid));
            $this->scale->load_items();
        }
    }
    
    /**
     * Finds and returns a grade_outcome object based on 1-3 field values.
     *
     * @param boolean $static Unless set to true, this method will also set $this object with the returned values.
     * @param string $field1
     * @param string $value1
     * @param string $field2
     * @param string $value2
     * @param string $field3
     * @param string $value3
     * @param string $fields
     * @return object grade_outcome object or false if none found.
     */
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($grade_outcome = get_record('grade_outcomes', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_outcome') {
                print_object($this);
                foreach ($grade_outcome as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            } else {
                $grade_outcome = new grade_outcome($grade_outcome);
                return $grade_outcome;
            }
        } else {
            return false;
        }
    }
}
?>
