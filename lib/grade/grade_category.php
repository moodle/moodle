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
     * The DB table.
     * @var string $table
     */
    var $table = 'grade_categories';
    
    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields', 'children', 'all_children');
    
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
     * A hierarchical array of all children below this category. This is stored separately from 
     * $children because it is more memory-intensive and may not be used as often.
     * @var array $all_children
     */
    var $all_children;

    /**
     * An associated grade_item object, with itemtype=category, used to calculate and cache a set of grade values
     * for this category.
     * @var object $grade_item
     */
    var $grade_item;

    /**
     * Constructor. Extends the basic functionality defined in grade_object.
     * @param array $params Can also be a standard object.
     * @param boolean $fetch Whether or not to fetch the corresponding row from the DB.
     * @param object $grade_item The associated grade_item object can be passed during construction.
     */
    function grade_category($params=NULL, $fetch=true, $grade_item=NULL) {
        $this->grade_object($params, $fetch);
        if (!empty($grade_item) && $grade_item->itemtype == 'category') {
            $this->grade_item = $grade_item;
            if (empty($this->grade_item->iteminstance)) {
                $this->grade_item->iteminstance = $this->id;
                $this->grade_item->update();
            }
        }
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
    function fetch($field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields="*") { 
        if ($grade_category = get_record('grade_categories', $field1, $value1, $field2, $value2, $field3, $value3, $fields)) {
            if (isset($this) && get_class($this) == 'grade_category') {
                foreach ($grade_category as $param => $value) {
                    $this->$param = $value;
                }
                return $this;
            } else {
                $grade_category = new grade_category($grade_category);
                return $grade_category;
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
     * This method also creates an associated grade_item if this wasn't done during construction.
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
        
        if (empty($this->grade_item)) {
            $grade_item = new grade_item();
            $grade_item->iteminstance = $this->id;
            $grade_item->itemtype = 'category';
            $result = $result & $grade_item->insert();
            $this->grade_item = $grade_item;
        }

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

    /**
     * Fetches and returns all the children categories and/or grade_items belonging to this category. 
     * By default only returns the immediate children (depth=1), but deeper levels can be requested, 
     * as well as all levels (0).
     * @param int $depth 1 for immediate children, 0 for all children, and 2+ for specific levels deeper than 1.
     * @param string $arraytype Either 'nested' or 'flat'. A nested array represents the true hierarchy, but is more difficult to work with.
     * @return array Array of child objects (grade_category and grade_item).
     */
    function get_children($depth=1, $arraytype='nested') {
        $children_array = array();
        
        // Set up $depth for recursion
        $newdepth = $depth;
        if ($depth > 1) {
            $newdepth--;
        }
        
        $childrentype = $this->get_childrentype();
        
        if ($childrentype == 'grade_item') {
            $children = get_records('grade_items', 'categoryid', $this->id);
            // No need to proceed with recursion
            $children_array = $this->children_to_array($children, $arraytype, 'grade_item');
            $this->children = $this->children_to_array($children, 'flat', 'grade_item');
        } elseif ($childrentype == 'grade_category') {
            $children = get_records('grade_categories', 'parent', $this->id, 'id');
            
            if ($depth == 1) {
                $children_array = $this->children_to_array($children, $arraytype, 'grade_category');
                $this->children = $this->children_to_array($children, 'flat', 'grade_category');
            } else {
                foreach ($children as $id => $child) {
                    $cat = new grade_category($child, false);

                    if ($cat->has_children()) {
                        if ($arraytype == 'nested') {
                            $children_array[] = array('object' => $cat, 'children' => $cat->get_children($newdepth, $arraytype));
                        } else {
                            $children_array[] = $cat;
                            $cat_children = $cat->get_children($newdepth, $arraytype);
                            foreach ($cat_children as $id => $cat_child) {
                                $children_array[] = new grade_category($cat_child, false);
                            }
                        }
                    } else {
                        if ($arraytype == 'nested') {
                            $children_array[] = array('object' => $cat);
                        } else {
                            $children_array[] = $cat;
                        }
                    }
                }
            }
        } else {
            return null;
        }

        return $children_array;
    }
   
    /**
     * Given an array of stdClass children of a certain $object_type, returns a flat or nested
     * array of these children, ready for appending to a tree built by get_children.
     * @static
     * @param array $children
     * @param string $arraytype
     * @param string $object_type
     * @return array
     */
    function children_to_array($children, $arraytype='nested', $object_type='grade_item') {
        $children_array = array();

        foreach ($children as $id => $child) {
            if ($arraytype == 'nested') {
                $children_array[] = array('object' => new $object_type($child, false));
            } else {
                $children_array[] = new $object_type($child);
            }
        }        

        return $children_array;
    }

    /**
     * Returns true if this category has any child grade_category or grade_item.
     * @return int number of direct children, or false if none found.
     */
    function has_children() {
        return count_records('grade_categories', 'parent', $this->id) + count_records('grade_items', 'categoryid', $this->id);
    }

    /**
     * This method checks whether an existing child exists for this
     * category. If the new child is of a different type, the method will return false (not allowed).
     * Otherwise it will return true.
     * @param object $child This must be a complete object, not a stdClass
     * @return boolean Success or failure
     */
    function can_add_child($child) {
        if ($this->has_children()) {
            if (get_class($child) != $this->get_childrentype()) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Check the type of the first child of this category, to see whether it is a 
     * grade_category or a grade_item, and returns that type as a string (get_class).
     * @return string
     */
    function get_childrentype() {
        $children = $this->children;
        if (empty($this->children)) {
            $count_item_children = count_records('grade_items', 'categoryid', $this->id);
            $count_cat_children = count_records('grade_categories', 'parent', $this->id);
            
            if ($count_item_children > 0) {
                return 'grade_item';
            } elseif ($count_cat_children > 0) {
                return 'grade_category';
            } else {
                return null;
            }
        }
        return get_class($children[0]);
    }

    /**
     * Retrieves from DB, instantiates and saves the associated grade_item object.
     * @return object Grade_item
     */
    function load_grade_item() {
        $params = get_record('grade_items', 'categoryid', $this->id, 'itemtype', 'category');
        $this->grade_item = new grade_item($params);
        return $this->grade_item;
    }
}

?>
