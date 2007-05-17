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
     * The grade_category object referenced by $this->parent (PK).
     * @var object $parent_category
     */
    var $parent_category;

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

        $this->path = grade_category::build_path($this);
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
     * In addition to update() as defined in grade_object, call flag_for_update of parent categories, if applicable.
     */
    function update() { 
        $qualifies = $this->qualifies_for_update();

        $result = parent::update();
       
        // Use $this->path to update all parent categories
        if ($result && $qualifies) {
            $this->flag_for_update();
        } 
        return $result;
    }
    
    /**
     * If parent::delete() is successful, send flag_for_update message to parent category.
     * @return boolean Success or failure.
     */
    function delete() {
        $result = parent::delete();
        
        if ($result) {
            $this->load_parent_category();
            if (!empty($this->parent_category)) {
                $result = $result && $this->parent_category->flag_for_update();
            }
        }

        return $result;
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
        
        $this->path = grade_category::build_path($this);

        // Build path and depth variables
        if (!empty($this->parent)) {
            $this->depth = $this->get_depth_from_path();
        } else {
            $this->depth = 1;
        }
        
        $this->update();
        
        if (empty($this->grade_item)) {
            $grade_item = new grade_item();
            $grade_item->iteminstance = $this->id;
            $grade_item->itemtype = 'category';
            
            if (!$grade_item->insert()) {
                return false;
            }
            
            $this->grade_item = $grade_item;
        }
        
        // Notify parent category of need to update.
        if ($result) {
            $this->load_parent_category();
            if (!empty($this->parent_category)) {
                if (!$this->parent_category->flag_for_update()) {
                    return false;
                }
            }
        } 
        return $result;
    }
    
    /**
     * Compares the values held by this object with those of the matching record in DB, and returns
     * whether or not these differences are sufficient to justify an update of all parent objects.
     * This assumes that this object has an id number and a matching record in DB. If not, it will return false.
     * @return boolean
     */
    function qualifies_for_update() {
        if (empty($this->id)) {
            return false;
        }

        $db_item = new grade_category(array('id' => $this->id));
        
        $aggregationdiff = $db_item->aggregation != $this->aggregation;
        $keephighdiff = $db_item->keephigh != $this->keephigh;
        $droplowdiff = $db_item->droplow != $this->droplow;

        if ($aggregationdiff || $keephighdiff || $droplowdiff) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets this category's and its parent's grade_item.needsupdate to true.
     * This is triggered whenever any change in any lower level may cause grade_finals
     * for this category to require an update. The flag needs to be propagated up all
     * levels until it reaches the top category. This is then used to determine whether or not
     * to regenerate the raw and final grades for each category grade_item. This is accomplished
     * thanks to the path variable, so we don't need to use recursion.
     * @return boolean Success or failure
     */
    function flag_for_update() {
        $result = true;
        
        $this->load_grade_item();

        if (empty($this->grade_item)) {
            die("Associated grade_item object does not exist for this grade_category!" . print_object($this)); 
            // TODO Send error message, this is a critical error: each category MUST have a matching grade_item object and load_grade_item() is supposed to create one!
        }

        $paths = explode('/', $this->path);
        
        // Remove the first index, which is always empty
        unset($paths[0]);
        
        if (!empty($paths)) {
            $wheresql = '';
            
            foreach ($paths as $categoryid) {
                $wheresql .= "iteminstance = $categoryid OR ";
            }
            $wheresql = substr($wheresql, 0, strrpos($wheresql, 'OR'));
            $grade_items = set_field_select('grade_items', 'needsupdate', '1', $wheresql);
            $this->grade_item->update_from_db();
        }
        return $result;
    }

    /**
     * Generates and saves raw_grades, based on this category's immediate children, then uses the 
     * associated grade_item to generate matching final grades. These immediate children must first have their own
     * raw and final grades, which means that ultimately we must get grade_items as children. The category's aggregation
     * method is used to generate these raw grades, which can then be used by the category's associated grade_item
     * to apply calculations to and generate final grades.
     * Steps to follow: 
     *  1. If the children are categories, AND their grade_item's needsupdate is true call generate_grades() on each of them (recursion)
     *  2. Get final grades from immediate children (if the children are categories, get the final grades from their grade_item)
     *  3. Aggregate these grades
     *  4. Save them under $this->grade_item->grade_grades_raw
     *  5. Use the grade_item's methods for generating the final grades.
     */
    function generate_grades() {
        // 1. Get immediate children
        $children = $this->get_children(1, 'flat');
        
        if (empty($children)) {
            return false;
        }

        // This assumes that all immediate children are of the same type (category OR item)
        $childrentype = get_class(current($children));
        
        $final_grades_for_aggregation = array();
        
        // 2. Get final grades from immediate children, after generating them if needed.
        // NOTE: Make sure that the arrays of final grades are indexed by userid. The resulting arrays are unlikely to match in sizes.
        if ($childrentype == 'grade_category') {
            foreach ($children as $id => $category) {
                $category->load_grade_item();
                
                if ($category->grade_item->needsupdate) {
                    $category->generate_grades();
                }
                
                $final_grades_for_aggregation[] = $category->grade_item->get_standardised_final();
            }
        } elseif ($childrentype == 'grade_item') {
            foreach ($children as $id => $item) {
                if ($item->needsupdate) {
                    $item->generate_final();
                }
                
                $final_grades_for_aggregation[] = $item->get_standardised_final();
            }
        }

        // 3. Aggregate the grades
        $aggregated_grades = $this->aggregate_grades($final_grades_for_aggregation);
        
        // 4. Save the resulting array of grades as raw grades
        $this->load_grade_item();
        $this->grade_item->save_raw($aggregated_grades);

        // 5. Use the grade_item's generate_final method
        $this->grade_item->generate_final();

        return true;
    }

    /**
     * Given an array of arrays of values, standardised from 0 to 1 and indexed by userid, 
     * uses this category's aggregation method to 
     * compute and return a single array of grade_raw objects with the aggregated gradevalue. 
     * @param array $raw_grade_sets
     * @return array Raw grade objects
     */
    function aggregate_grades($final_grade_sets) {
        if (empty($final_grade_sets)) {
            return null;
        }
        
        $aggregated_grades = array();
        $pooled_grades = array();

        foreach ($final_grade_sets as $setkey => $set) {
            foreach ($set as $userid => $final_grade) {
                $this->load_grade_item();
                $value = standardise_score((float) $final_grade, 0, 1, $this->grade_item->grademin, $this->grade_item->grademax);
                $pooled_grades[$userid][] = $value;
            }
        }

        foreach ($pooled_grades as $userid => $grades) {
            $aggregated_value = null;

            switch ($this->aggregation) {
                case GRADE_AGGREGATE_MEAN : // Arithmetic average
                    $num = count($grades);
                    $sum = array_sum($grades);
                    $aggregated_value = $sum / $num;
                    break;
                case GRADE_AGGREGATE_MEDIAN : // Middle point value in the set: ignores frequencies
                    sort($grades);
                    $num = count($grades);
                    $halfpoint = intval($num / 2);
                    
                    if($num % 2 == 0) { 
                        $aggregated_value = ($grades[ceil($halfpoint)] + $grades[floor($halfpoint)]) / 2; 
                    } else { 
                        $aggregated_value = $grades[$halfpoint]; 
                    }

                    break;
                case GRADE_AGGREGATE_MODE : // Value that occurs most frequently. Not always useful (all values are likely to be different)
                    // TODO implement or reject
                    break;
                case GRADE_AGGREGATE_SUM : // I don't see much point to this one either
                    $aggregated_value = array_sum($grades);
                    break;
                default:
                    $num = count($grades);
                    $sum = array_sum($grades);
                    $aggregated_value = $sum / $num; 
                    break;
            }
            
            // If the gradevalue is null, we have a problem
            if (empty($aggregated_value)) {
                return false;
            }            
            
            $grade_raw = new grade_grades_raw();
            
            $grade_raw->userid = $userid;
            $grade_raw->gradevalue = $aggregated_value;
            $grade_raw->grademin = $this->grade_item->grademin;
            $grade_raw->grademax = $this->grade_item->grademax;
            $grade_raw->itemid = $this->grade_item->id;
            $aggregated_grades[$userid] = $grade_raw;
        }
        
        return $aggregated_grades;
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
     * If no grade_item exists yet, create one.
     * @return object Grade_item
     */
    function load_grade_item() {
        $grade_items = get_records_select('grade_items', "iteminstance = $this->id AND itemtype = 'category'", null, '*', 0, 1);
        
        if ($grade_items){ 
            $params = current($grade_items);
            $this->grade_item = new grade_item($params);
        } else {
            $this->grade_item = new grade_item();
        }
        
        // If the associated grade_item isn't yet created, do it now. But first try loading it, in case it exists in DB.
        if (empty($this->grade_item->id)) {
            $this->grade_item->iteminstance = $this->id;
            $this->grade_item->itemtype = 'category';
            $this->grade_item->insert();
            $this->grade_item->update_from_db();
        }

        return $this->grade_item;
    }

    /**
     * Uses $this->parent to instantiate $this->parent_category based on the
     * referenced record in the DB.
     * @return object Parent_category
     */
    function load_parent_category() {
        if (empty($this->parent_category) && !empty($this->parent)) {
            $this->parent_category = new grade_category(array('id' => $this->parent));
        }
        return $this->parent_category;
    }


} 
?>
