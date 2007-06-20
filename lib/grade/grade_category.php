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
    var $nonfields = array('table', 'nonfields', 'children', 'all_children', 'grade_item', 'parent_category');

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
     * A grade_category object this category used to belong to before getting updated. Will be deleted shortly.
     * @var object $old_parent
     */
    var $old_parent;

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
    function grade_category($params=NULL, $fetch=true) {
        $this->grade_object($params, $fetch);
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

        // Update the grade_item's sortorder if needed
        if (!empty($this->sortorder)) {
            $this->load_grade_item();
            if (!empty($this->grade_item)) {
                $this->grade_item->sortorder = $this->sortorder;
                $this->grade_item->update();
            }
            unset($this->sortorder);
        }

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

            // Update children's categoryid/parent field
            global $db;
            $set_field_result = set_field('grade_items', 'categoryid', null, 'categoryid', $this->id);
            $set_field_result = set_field('grade_categories', 'parent', null, 'parent', $this->id);
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
        if (!parent::insert()) {
            debugging("Could not insert this category: " . print_r($this, true));
            return false;
        }

        $this->path = grade_category::build_path($this);

        // Build path and depth variables
        if (!empty($this->parent)) {
            $this->depth = $this->get_depth_from_path();
        } else {
            $this->depth = 1;
        }

        $this->update();

        // initialize grade_item for this category
        $this->grade_item = $this->get_grade_item();

        // Notify parent category of need to update.
        $this->load_parent_category();
        if (!empty($this->parent_category)) {
            if (!$this->parent_category->flag_for_update()) {
                debugging("Could not notify parent category of the need to update its final grades.");
                return false;
            }
        }

        return true;
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
        if (empty($this->id)) {
            debugging("Needsupdate requested before insering grade category.");
            return true;
        }

        $result = true;

        $this->load_grade_item();

        $paths = explode('/', $this->path);

        // Remove the first index, which is always empty
        unset($paths[0]);

        if (!empty($paths)) {
            $wheresql = '';

            foreach ($paths as $categoryid) {
                $wheresql .= "iteminstance = $categoryid OR ";
            }
            $wheresql = substr($wheresql, 0, strrpos($wheresql, 'OR'));
            $grade_items = set_field_select('grade_items', 'needsupdate', '1', $wheresql . ' AND courseid = ' . $this->courseid);
            $this->grade_item->update_from_db();
        }
        return $result;
    }

    /**
     * Generates and saves raw_grades in associated category grade item.
     * These immediate children must alrady have their own final grades.
     * The category's aggregation method is used to generate raw grades.
     *
     * Please note that category grade is either calculated or aggregated - not both at the same time.
     *
     * This method must be used ONLY from grade_item::update_final_grades(),
     * because the calculation must be done in correct order!
     *
     * Steps to follow:
     *  1. Get final grades from immediate children
     *  3. Aggregate these grades
     *  4. Save them in raw grades of associated category grade item
     */
    function generate_grades() {
        global $CFG;

        $this->load_grade_item();
        $this->grade_item->load_scale();

        // find grde items of immediate children (category or grade items)
        $dependson = $this->grade_item->dependson();
        $items = array();

        foreach($dependson as $dep) {
            $items[$dep] = grade_item::fetch('id', $dep);
        }

        // where to look for final grades - include or grade item too
        $gis = implode(',', array_merge($dependson, array($this->grade_item->id)));

        $sql = "SELECT g.*
                  FROM {$CFG->prefix}grade_grades g, {$CFG->prefix}grade_items gi
                 WHERE gi.id = g.itemid AND gi.courseid={$this->grade_item->courseid} AND gi.id IN ($gis)
              ORDER BY g.userid";

        // group the results by userid and aggregate the grades in this group
        if ($rs = get_recordset_sql($sql)) {
            if ($rs->RecordCount() > 0) {
                $prevuser = 0;
                $grades   = array();
                $final    = null;
                while ($used = rs_fetch_next_record($rs)) {
                    if ($used->userid != $prevuser) {
                        $this->aggregate_grades($prevuser, $items, $grades, $dependson, $final);
                        $prevuser = $used->userid;
                        $grades   = array();
                        $final    = null;
                    }
                    if ($used->itemid == $this->grade_item->id) {
                        $final = new grade_grades($used, false);
                    }
                    $grades[$used->itemid] = $used->finalgrade;
                }
                $this->aggregate_grades($prevuser, $items, $grades, $dependson, $final);
            }
        }

        return true;
    }

    /**
     * internal function for category grades aggregation
     */
    function aggregate_grades($userid, $items, $grades, $dependson, $final) {
        if (empty($userid)) {
            //ignore first run
            return;
        }

        // no circular references allowed
        unset($grades[$this->grade_item->id]);

        // insert final grade - it will needed anyway later
        if (empty($final)) {
            $final = new grade_grades(array('itemid'=>$this->grade_item->id, 'userid'=>$userid), false);
            $final->insert();
        }

        // if no grades calculation possible or grading not allowed clear both final and raw
        if (empty($grades) or empty($items) or ($this->grade_item->gradetype != GRADE_TYPE_VALUE and $this->grade_item->gradetype != GRADE_TYPE_SCALE)) {
            $final->finalgrade = null;
            $final->rawgrade   = null;
            $final->update();
            return;
        }

        // normalize the grades first - all will have value 0...1
        // ungraded items are not used in aggreagation
        foreach ($grades as $k=>$v) {
            if (is_null($v)) {
                // null means no grade
                unset($grades[$k]);
                continue;
            }
            $grades[$k] = grade_grades::standardise_score($v, $items[$k]->grademin, $items[$k]->grademax, 0, 1);
        }

        //limit and sort
        $this->apply_limit_rules($grades);
        sort($grades, SORT_NUMERIC);

        // let's see we have still enough grades to do any statisctics
        if (count($grades) == 0) {
            // not enough attempts yet
            if (!is_null($final->finalgrade) or !is_null($final->rawgrade)) {
                $final->finalgrade = null;
                $final->rawgrade   = null;
                $final->update();
            }
            return;
        }

        switch ($this->aggregation) {
            case GRADE_AGGREGATE_MEDIAN: // Middle point value in the set: ignores frequencies
                $num = count($grades);
                $halfpoint = intval($num / 2);

                if($num % 2 == 0) {
                    $rawgrade = ($grades[ceil($halfpoint)] + $grades[floor($halfpoint)]) / 2;
                } else {
                    $rawgrade = $grades[$halfpoint];
                }
                break;

            case GRADE_AGGREGATE_MIN:
                $rawgrade = reset($grades);
                break;

            case GRADE_AGGREGATE_MAX:
                $rawgrade = array_pop($grades);
                break;

            case GRADE_AGGREGATE_MEAN_ALL:    // Arithmetic average of all grade items including even NULLs; NULL grade caunted as minimum
                $num = count($dependson);     // you can calculate sum from this one if you multiply it with count($this->dependson() ;-)
                $sum = array_sum($grades);
                $rawgrade = $sum / $num;
                break;

            case GRADE_AGGREGATE_MODE:       // the most common value, the highest one if multimode
                $freq = array_count_values($grades);
                arsort($freq);                      // sort by frequency keeping keys
                $top = reset($freq);               // highest frequency count
                $modes = array_keys($freq, $top);  // search for all modes (have the same highest count)
                rsort($modes, SORT_NUMERIC);       // get highes mode
                $rawgrade = reset($modes);

            case GRADE_AGGREGATE_MEAN_GRADED: // Arithmetic average of all final grades, unfinished are not calculated
            default:
                $num = count($grades);
                $sum = array_sum($grades);
                $rawgrade = $sum / $num;
                break;
        }

        // recalculate the rawgrade back to requested range
        $rawgrade = $this->grade_item->adjust_grade($rawgrade, 0, 1);

        // prepare update of new raw grade
        $final->rawgrade    = $rawgrade;
        $final->finalgrade  = null;
        $final->rawgrademin = $this->grade_item->grademin;
        $final->rawgrademax = $this->grade_item->grademax;
        $final->rawscaleid  = $this->grade_item->scaleid;

        // TODO - add some checks to prevent updates when not needed
        $final->update();
    }

    /**
     * Given an array of grade values (numerical indices), applies droplow or keephigh
     * rules to limit the final array.
     * @param array $grades
     * @return array Limited grades.
     */
    function apply_limit_rules(&$grades) {
        rsort($grades, SORT_NUMERIC);
        if (!empty($this->droplow)) {
            for ($i = 0; $i < $this->droplow; $i++) {
                array_pop($grades);
            }
        } elseif (!empty($this->keephigh)) {
            while (count($grades) > $this->keephigh) {
                array_pop($grades);
            }
        }
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
            $child = new $object_type($child, false);
            if ($arraytype == 'nested') {
                $children_array[$child->get_sortorder()] = array('object' => $child);
            } else {
                $children_array[$child->get_sortorder()] = $child;
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
     * Checks whether an existing child exists for this category. If the new child is of a
     * different type, the method will return false (not allowed). Otherwise it will return true.
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
     * Disassociates this category from its category parent(s). The object is then updated in DB.
     * @return boolean Success or Failure
     */
    function divorce_parent() {
        $this->old_parent = $this->get_parent_category();
        $this->parent = null;
        $this->parent_category = null;
        $this->depth = 1;
        $this->path = '/' . $this->id;
        return $this->update();
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
     * as well as all levels (0). The elements are indexed by sort order.
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
                            $children_array[$cat->get_sortorder()] = array('object' => $cat, 'children' => $cat->get_children($newdepth, $arraytype));
                        } else {
                            $children_array[$cat->get_sortorder()] = $cat;
                            $cat_children = $cat->get_children($newdepth, $arraytype);
                            foreach ($cat_children as $id => $cat_child) {
                                $children_array[$cat_child->get_sortorder()] = new grade_category($cat_child, false);
                            }
                        }
                    } else {
                        if ($arraytype == 'nested') {
                            $children_array[$cat->get_sortorder()] = array('object' => $cat);
                        } else {
                            $children_array[$cat->get_sortorder()] = $cat;
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
     * Check the type of the first child of this category, to see whether it is a
     * grade_category or a grade_item, and returns that type as a string (get_class).
     * @return string
     */
    function get_childrentype() {
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
        reset($this->children);
        return get_class(current($this->children));
    }

    /**
     * Uses get_grade_item to load or create a grade_item, then saves it as $this->grade_item.
     * @return object Grade_item
     */
    function load_grade_item() {
        if (empty($this->grade_item)) {
            $this->grade_item = $this->get_grade_item();
        }
        return $this->grade_item;
    }

    /**
     * Retrieves from DB and instantiates the associated grade_item object.
     * If no grade_item exists yet, create one.
     * @return object Grade_item
     */
    function get_grade_item() {
        if (empty($this->id)) {
            debugging("Attempt to obtain a grade_category's associated grade_item without the category's ID being set.");
            return false;
        }

        $grade_item = new grade_item(array('courseid'=>$this->courseid, 'itemtype'=>'category', 'iteminstance'=>$this->id), false);
        if (!$grade_items = $grade_item->fetch_all_using_this()) {
            // create a new one
            $grade_item->gradetype = GRADE_TYPE_VALUE;
            $grade_item->insert();

        } else if (count($grade_items) == 1){
            // found existing one
            $grade_item = reset($grade_items);

        } else {
            debugging("Found more than one grade_item attached to category id:".$this->id);
            // return first one
            $grade_item = reset($grade_items);
        }

        return $grade_item;
    }

    /**
     * Uses $this->parent to instantiate $this->parent_category based on the
     * referenced record in the DB.
     * @return object Parent_category
     */
    function load_parent_category() {
        if (empty($this->parent_category) && !empty($this->parent)) {
            $this->parent_category = $this->get_parent_category();
        }
        return $this->parent_category;
    }

    /**
     * Uses $this->parent to instantiate and return a grade_category object.
     * @return object Parent_category
     */
    function get_parent_category() {
        if (!empty($this->parent)) {
            $parent_category = new grade_category(array('id' => $this->parent));
            return $parent_category;
        } else {
            return null;
        }
    }

    /**
     * Sets this category as the parent for the given children. If the category's courseid isn't set, it uses that of the children items.
     * A number of constraints are necessary:
     *    - The children must all be of the same type and at the same level
     *    - The children cannot already be top categories
     *    - The children all belong to the same course
     * @param array $children An array of fully instantiated grade_category OR grade_item objects
     *
     * @return boolean Success or Failure
     * @TODO big problem of performance
     */
    function set_as_parent($children) {
        global $CFG;

        if (empty($children) || !is_array($children)) {
            debugging("Passed an empty or non-array variable to grade_category::set_as_parent()");
            return false;
        }

        // Check type and sortorder of first child
        $first_child = current($children);
        $first_child_type = get_class($first_child);

        // If this->courseid is not set, set it to the first child's courseid
        if (empty($this->courseid)) {
            $this->courseid = $first_child->courseid;
        }

        $grade_tree = new grade_tree();

        foreach ($children as $child) {
            if (get_class($child) != $first_child_type) {
                debugging("Violated constraint: Attempted to set a category as a parent over children of 2 different types.");
                return false;
            }

            if ($grade_tree->get_element_type($child) == 'topcat') {
                debugging("Violated constraint: Attempted to set a category over children which are already top categories.");
                return false;
            }

            if ($first_child_type == 'grade_category' or $first_child_type == 'grade_item') {
                if (!empty($child->parent)) {
                    debugging("Violated constraint: Attempted to set a category over children that already have a top category.");
                    return false;
                }
            } else {
                debugging("Attempted to set a category over children that are neither grade_items nor grade_categories.");
                return false;
            }

            if ($child->courseid != $this->courseid) {
                debugging("Attempted to set a category over children which do not belong to the same course.");
                return false;
            }
        }

        // We passed all the checks, time to set the category as a parent.
        foreach ($children as $child) {
            $child->divorce_parent();
            $child->set_parent_id($this->id);
            if (!$child->update()) {
                debugging("Could not set this category as a parent for one of its children, DB operation failed.");
                return false;
            }
        }

        // TODO Assign correct sortorders to the newly assigned children and parent. Simply add 1 to all of them!
        $this->load_grade_item();
        $this->grade_item->sortorder = $first_child->get_sortorder();

        if (!$this->update()) {
            debugging("Could not update this category's sortorder in DB.");
            return false;
        }

        $query = "UPDATE {$CFG->prefix}grade_items SET sortorder = sortorder + 1 WHERE sortorder >= {$this->grade_item->sortorder}";
        $query .= " AND courseid = $this->courseid";

        if (!execute_sql($query)) {
            debugging("Could not update the sortorder of grade_items listed after this category.");
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the most descriptive field for this object. This is a standard method used
     * when we do not know the exact type of an object.
     * @return string name
     */
    function get_name() {
        return $this->fullname;
    }

    /**
     * Returns this category's grade_item's id. This is specified for cases where we do not
     * know an object's type, and want to get either an item's id or a category's item's id.
     *
     * @return int
     */
    function get_item_id() {
        $this->load_grade_item();
        return $this->grade_item->id;
    }

    /**
     * Returns this category's parent id. A generic method shared by objects that have a parent id of some kind.
     * @return id $parentid
     */
    function get_parent_id() {
        return $this->parent;
    }

    /**
     * Sets this category's parent id. A generic method shared by objects that have a parent id of some kind.
     * @param id $parentid
     */
    function set_parent_id($parentid) {
        $this->parent = $parentid;
        $this->path = grade_category::build_path($this);
        $this->depth = $this->get_depth_from_path();
    }

    /**
     * Returns the sortorder of the associated grade_item. This method is also available in
     * grade_item, for cases where the object type is not known.
     * @return int Sort order
     */
    function get_sortorder() {
        if (empty($this->sortorder)) {
            $this->load_grade_item();
            if (!empty($this->grade_item)) {
                return $this->grade_item->sortorder;
            }
        } else {
            return $this->sortorder;
        }
    }

    /**
     * Sets a temporary sortorder variable for this category. It is used in the update() method to update the grade_item.
     * This method is also available in grade_item, for cases where the object type is not know.
     * @param int $sortorder
     * @return void
     */
    function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
    }

    /**
     * Returns the locked state/date of the associated grade_item. This method is also available in
     * grade_item, for cases where the object type is not known.
     * @return int 0, 1 or timestamp int(10)
     */
    function get_locked() {
        $this->load_grade_item();
        if (!empty($this->grade_item)) {
            return $this->grade_item->locked;
        } else {
            return false;
        }
    }

    /**
     * Sets the grade_item's locked variable and updates the grade_item.
     * Method named after grade_item::set_locked().
     * @param int $locked 0, 1 or a timestamp int(10) after which date the item will be locked.
     * @return void
     */
    function set_locked($locked) {
        $this->load_grade_item();
        if (!empty($this->grade_item)) {
            $this->grade_item->locked = $locked;
            return $this->grade_item->update();
        } else {
            return false;
        }
    }

    /**
     * Returns the hidden state/date of the associated grade_item. This method is also available in
     * grade_item, for cases where the object type is not known.
     * @return int 0, 1 or timestamp int(10)
     */
    function get_hidden() {
        $this->load_grade_item();
        if (!empty($this->grade_item)) {
            return $this->grade_item->hidden;
        } else {
            return false;
        }
    }

    /**
     * Sets the grade_item's hidden variable and updates the grade_item.
     * Method named after grade_item::set_hidden().
     * @param int $hidden 0, 1 or a timestamp int(10) after which date the item will be hidden.
     * @return void
     */
    function set_hidden($hidden) {
        $this->load_grade_item();
        if (!empty($this->grade_item)) {
            $this->grade_item->hidden = $hidden;
            return $this->grade_item->update();
        } else {
            return false;
        }
    }

    /**
     * If the old parent is set (after an update), this checks and returns whether it has any children. Important for
     * deleting childless categories.
     * @return boolean
     */
    function is_old_parent_childless() {
        if (!empty($this->old_parent)) {
            return !$this->old_parent->has_children();
        } else {
            return false;
        }
    }
}
?>
