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
     * @var int $parent 
     */
    var $parent;
   
    /**
     * The number of parents this category has.
     * @var int $depth
     */
    var $depth = 0;

    /**
     * Shows the hierarchical path for this category as /1/2/3 (like course_categories), the last number being
     * this category's autoincrement ID number.
     * @var string $path
     */
    var $path;

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
     * Constructor. Extends the basic functionality defined in grade_object.
     * @param array $params Can also be a standard object.
     * @param boolean $fetch Wether or not to fetch the corresponding row from the DB.
     */
    function grade_category($params=NULL, $fetch=true) {
        $this->grade_object($params, $fetch);
    }

    
    /**
     * Builds this category's path string based on its parents (if any) and its own id number.
     * This is typically done just before inserting this object in the DB for the first time,
     * or when a new parent is added or changed. It is a recursive function: once the calling
     * object no longer has a parent, the path is complete.
     *
     * @static
     * @param object $grade_category
     * @return int The depth of this category (2 means there is one parent)
     */
    function build_path($grade_category) {
        if (empty($grade_category->parent)) {
            return "/$grade_category->id";
        } else {
            $parent = get_record('grade_categories', 'id', $grade_category->parent);
            return grade_category::build_path($parent) . "/$grade_category->id";
        }
    }


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

    /**
     * In addition to the normal insert() defined in grade_object, this method sets the depth
     * and path for this object, and update the record accordingly. The reason why this must
     * be done here instead of in the constructor, is that they both need to know the record's
     * id number, which only gets created at insertion time.
     */
    function insert() {
        $result = parent::insert();

        // Build path and depth variables
        if (!empty($this->parent)) {
            $this->path = grade_category::build_path($this);
            $this->depth = $this->get_depth_from_path();
        } else {
            $this->depth = 1;
            $this->path = "/$this->id";
        }
        
        $this->update();
        return $result;
    }
    
    /**
     * Looks at a path string (e.g. /2/45/56) and returns the depth level represented by this path (in this example, 3).
     * If no string is given, it looks at the obect's path and assigns the resulting depth to its $depth variable.
     * @param string $path
     * @return int Depth level
     */
    function get_depth_from_path($path=NULL) {
        if (empty($path)) {
            $path = $this->path;
        }
        preg_match_all('/\/([0-9]+)+?/', $path, $matches);
        $depth = count($matches[0]);

        return $depth;
    }
}

?>
