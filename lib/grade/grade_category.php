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

class grade_category extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_categories';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields');

    
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
     * Finds and returns a grade_category object based on 1-3 field values.
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
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*")
    { 
        if ($grade_category = get_record('grade_categories', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (!isset($this)) {
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
