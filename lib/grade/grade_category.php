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

class grade_category extends grade_object
{
    /**
     * The table name
     * @var string $table
     */
    var $table = 'grade_categories';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'required_fields');

    /**
     * Array of required fields (keys) and their default values (values).
     * @var array $required_fields
     */
    var $required_fields = array('aggregation' => 0,
                                 'keephigh'    => 0,
                                 'fullname'    => null,
                                 'droplow'     => 0,
                                 'hidden'      => 0);
    
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
                if (in_object_vars($param, $this)) {
                    $this->$param = $value;
                }
            }

            $this->set_defaults();
        } 
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

?>
