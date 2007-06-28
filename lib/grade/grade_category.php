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
    var $nonfields = array('table', 'nonfields', 'children', 'all_children', 'grade_item', 'parent_category', 'sortorder');

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
    var $aggregation = GRADE_AGGREGATE_MEAN_ALL;

    /**
     * Keep only the X highest items.
     * @var int $keephigh
     */
    var $keephigh = 0;

    /**
     * Drop the X lowest items.
     * @var int $droplow
     */
    var $droplow = 0;

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
     * Temporary sortorder for speedup of children resorting
     */
    var $sortorder;

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
            return '/'.$grade_category->id;
        } else {
            $parent = get_record('grade_categories', 'id', $grade_category->parent);
            return grade_category::build_path($parent).'/'.$grade_category->id;
        }
    }


    /**
     * Finds and returns a grade_category instance based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return object grade_category instance or false if none found.
     */
    function fetch($params) {
        return grade_object::fetch_helper('grade_categories', 'grade_category', $params);
    }

    /**
     * Finds and returns all grade_category instances based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_category insatnces or false if none found.
     */
    function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_categories', 'grade_category', $params);
    }

    /**
     * In addition to update() as defined in grade_object, call force_regrading of parent categories, if applicable.
     */
    function update() {
        // load the grade item or create a new one
        $this->load_grade_item();

        // force recalculation of path;
        if (empty($this->path)) {
            $this->path  = grade_category::build_path($this);
            $this->depth = substr_count($this->path, '/');
        }

        if (!parent::update()) {
            return false;
        }

        // Recalculate grades if needed
        if ($this->qualifies_for_regrading()) {
            $this->grade_item->force_regrading();
        }
        return true;
    }

    /**
     * If parent::delete() is successful, send force_regrading message to parent category.
     * @return boolean Success or failure.
     */
    function delete() {
        $result = parent::delete();

        if ($result) {
            $this->load_parent_category();
            if (!empty($this->parent_category)) {
                $result = $result && $this->parent_category->force_regrading();
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

        if (empty($this->courseid)) {
            error('Can not insert grade category without course id!');
        }

        if (empty($this->parent)) {
            $course_category = grade_category::fetch_course_category($this->courseid);
            $this->parent = $course_category->id;

        }

        $this->path = null;

        if (!parent::insert()) {
            debugging("Could not insert this category: " . print_r($this, true));
            return false;
        }

        // build path and depth
        $this->update();

        return true;
    }

    function insert_course_category($courseid) {
        $this->courseid = $courseid;
        $this->fullname = 'course grade category';
        $this->path     = null;
        $this->parent   = null;

        if (!parent::insert()) {
            debugging("Could not insert this category: " . print_r($this, true));
            return false;
        }

        // build path and depth
        $this->update();

        return true;
    }

    /**
     * Compares the values held by this object with those of the matching record in DB, and returns
     * whether or not these differences are sufficient to justify an update of all parent objects.
     * This assumes that this object has an id number and a matching record in DB. If not, it will return false.
     * @return boolean
     */
    function qualifies_for_regrading() {
        if (empty($this->id)) {
            return false;
        }

        $db_item = new grade_category(array('id' => $this->id));

        $aggregationdiff = $db_item->aggregation != $this->aggregation;
        $keephighdiff    = $db_item->keephigh    != $this->keephigh;
        $droplowdiff     = $db_item->droplow     != $this->droplow;

        return ($aggregationdiff || $keephighdiff || $droplowdiff);
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
    function force_regrading() {
        if (empty($this->id)) {
            debugging("Needsupdate requested before insering grade category.");
            return true;
        }

        $this->load_grade_item();

        if ($this->grade_item->needsupdate) {
            // this grade_item (and category) already needs update, no need to set it again here or in parent categories
            return true;
        }

        $paths = explode('/', $this->path);

        // Remove the first index, which is always empty
        unset($paths[0]);

        $result = true;

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

        if ($this->grade_item->is_locked()) {
            return true; // no need to recalculate locked items
        }

        $this->grade_item->load_scale();


        // find grade items of immediate children (category or grade items)
        $depends_on = $this->grade_item->depends_on();

        $items = array();

        foreach($depends_on as $dep) {
            $items[$dep] = grade_item::fetch(array('id'=>$dep));
        }

        // where to look for final grades - include or grade item too
        $gis = implode(',', array_merge($depends_on, array($this->grade_item->id)));

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
                        $this->aggregate_grades($prevuser, $items, $grades, $depends_on, $final);
                        $prevuser = $used->userid;
                        $grades   = array();
                        $final    = null;
                    }
                    if ($used->itemid == $this->grade_item->id) {
                        $final = new grade_grades($used, false);
                        $final->grade_item =& $this->grade_item;
                    }
                    $grades[$used->itemid] = $used->finalgrade;
                }
                $this->aggregate_grades($prevuser, $items, $grades, $depends_on, $final);
            }
        }

        return true;
    }

    /**
     * internal function for category grades aggregation
     */
    function aggregate_grades($userid, $items, $grades, $depends_on, $final) {
        if (empty($userid)) {
            //ignore first run
            return;
        }

        // no circular references allowed
        unset($grades[$this->grade_item->id]);

        // insert final grade - it will be needed later anyway
        if (empty($final)) {
            $final = new grade_grades(array('itemid'=>$this->grade_item->id, 'userid'=>$userid), false);
            $final->insert();
            $final->grade_item =& $this->grade_item;

        } else if ($final->is_locked()) {
            // no need to recalculate locked grades
            return;
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
                $num = count($depends_on);     // you can calculate sum from this one if you multiply it with count($this->depends_on() ;-)
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
        if ($this->is_course_category()) {
            return true;

        } else if ($this->has_children()) {
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
     * Returns tree with all grade_items and categories as elements
     * @static
     * @param int $courseid
     * @param boolean $include_grades include final grades
     * @param boolean $include_category_items as category children
     * @return array
     */
    function fetch_course_tree($courseid, $include_grades=false, $include_category_items=false) {
        $course_category = grade_category::fetch_course_category($courseid);
        $category_array = array('object'=>$course_category, 'type'=>'category', 'depth'=>1,
                                'children'=>$course_category->get_children($include_grades, $include_category_items));
        if ($include_grades) {
            $category_array['finalgrades'] = $course_category->get_final();
        }
        $sortorder = 1;
        $course_category->set_sortorder($sortorder);
        $course_category->sortorder = $sortorder;
        return grade_category::_fetch_course_tree_recursion($category_array, $sortorder);
    }

    function _fetch_course_tree_recursion($category_array, &$sortorder) {
        // update the sortorder in db if needed
        if ($category_array['object']->sortorder != $sortorder) {
            $category_array['object']->set_sortorder($sortorder);
        }

        // store the grade_item or grade_category instance with extra info
        $result = array('object'=>$category_array['object'], 'type'=>$category_array['type'], 'depth'=>$category_array['depth']);

        // reuse final grades if there
        if (array_key_exists('finalgrades', $category_array)) {
            $result['finalgrades'] = $category_array['finalgrades'];
        }

        // recursively resort children
        if (!empty($category_array['children'])) {
            $result['children'] = array();
            foreach($category_array['children'] as $oldorder=>$child_array) {
                if ($child_array['type'] == 'courseitem' or $child_array['type'] == 'categoryitem') {
                    $result['children'][$sortorder] = grade_category::_fetch_course_tree_recursion($child_array, $sortorder);
                } else {
                    $result['children'][++$sortorder] = grade_category::_fetch_course_tree_recursion($child_array, $sortorder);
                }
            }
        }

        return $result;
    }

    /**
     * Fetches and returns all the children categories and/or grade_items belonging to this category.
     * By default only returns the immediate children (depth=1), but deeper levels can be requested,
     * as well as all levels (0). The elements are indexed by sort order.
     * @return array Array of child objects (grade_category and grade_item).
     */
    function get_children($include_grades=false, $include_category_items=false) {

        // This function must be as fast as possible ;-)
        // fetch all course grade items and categories into memory - we do not expect hundreds of these in course
        // we have to limit the number of queries though, because it will be used often in grade reports

        $cats  = get_records('grade_categories', 'courseid', $this->courseid);
        $items = get_records('grade_items', 'courseid', $this->courseid);

        // init children array first
        foreach ($cats as $catid=>$cat) {
            $cats[$catid]->children = array();
        }

        //first attach items to cats and add category sortorder
        foreach ($items as $item) {
            if ($item->itemtype == 'course' or $item->itemtype == 'category') {
                $cats[$item->iteminstance]->sortorder = $item->sortorder;

                if (!$include_category_items) {
                    continue;
                }
                $categoryid = $item->iteminstance;
            } else {
                $categoryid = $item->categoryid;
            }

            // prevent problems with duplicate sortorders in db
            $sortorder = $item->sortorder;
            while(array_key_exists($sortorder, $cats[$categoryid]->children)) {
                echo "$sortorder exists in item loop<br>";
                $sortorder++;
            }

            $cats[$categoryid]->children[$sortorder] = $item;

        }

        // now find the requested category and connect categories as children
        $category = false;
        foreach ($cats as $catid=>$cat) {
            if (!empty($cat->parent)) {
                // prevent problems with duplicate sortorders in db
                $sortorder = $cat->sortorder;
                while(array_key_exists($sortorder, $cats[$cat->parent]->children)) {
                    echo "$sortorder exists in cat loop<br>";
                    $sortorder++;
                }

                $cats[$cat->parent]->children[$sortorder] = $cat;
            }

            if ($catid == $this->id) {
                $category = &$cats[$catid];
            }
        }

        unset($items); // not needed
        unset($cats); // not needed

        $children_array = grade_category::_get_children_recursion($category, $include_grades);

        ksort($children_array);

        return $children_array;

    }

    function _get_children_recursion($category, $include_grades) {

        $children_array = array();
        foreach($category->children as $sortorder=>$child) {
            if (array_key_exists('itemtype', $child)) {
                $grade_item = new grade_item($child, false);
                if (in_array($grade_item->itemtype, array('course', 'category'))) {
                    $type  = $grade_item->itemtype.'item';
                    $depth = $category->depth;
                } else {
                    $type  = 'item';
                    $depth = $category->depth; // we use this to set the same colour
                }
                $children_array[$sortorder] = array('object'=>$grade_item, 'type'=>$type, 'depth'=>$depth);

            } else {
                $children = grade_category::_get_children_recursion($child, $include_grades);
                $grade_category = new grade_category($child, false);
                if (empty($children)) {
                    $children = array();
                }
                $children_array[$sortorder] = array('object'=>$grade_category, 'type'=>'category', 'depth'=>$grade_category->depth, 'children'=>$children);
            }

            if ($include_grades) {
                $children_array[$sortorder]['finalgrades'] = $grade_item->get_final();
            }
        }

        // sort the array
        ksort($children_array);

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

        if (empty($this->parent)) {
            $params = array('courseid'=>$this->courseid, 'itemtype'=>'course', 'iteminstance'=>$this->id);

        } else {
            $params = array('courseid'=>$this->courseid, 'itemtype'=>'category', 'iteminstance'=>$this->id);
        }

        if (!$grade_items = grade_item::fetch_all($params)) {
            // create a new one
            $grade_item = new grade_item($params, false);
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
     * Sets this category as the parent for the given children.
     * A number of constraints are necessary:
     *    - The children must all be of the same type and at the same level (top level is exception)
     *    - The children all belong to the same course
     * @param array $children An array of fully instantiated grade_category OR grade_item objects
     *
     * @return boolean Success or Failure
     */
    function set_as_parent($children) {
        global $CFG;

        if (empty($children) || !is_array($children)) {
            debugging("Passed an empty or non-array variable to grade_category::set_as_parent()");
            return false;
        }

        $result = true;

        foreach ($children as $child) {
            // check sanity of course id
            if ($child->courseid != $this->courseid) {
                debugging("Attempted to set a category over children which do not belong to the same course.");
                continue;
            }
            // change parrent if possible
            if (!$child->set_parent_id($this->id)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Returns the most descriptive field for this object. This is a standard method used
     * when we do not know the exact type of an object.
     * @return string name
     */
    function get_name() {
        if (empty($this->parent)) {
            return "Top course category"; //TODO: localize
        } else {
            return $this->fullname;
        }
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
        if (!$parent_category = grade_category::fetch(array('id'=>$parentid))) {
            return false;
        }
        if (!$parent_category->can_add_child($this)) {
            return false;
        }

        $this->force_regrading();            // mark old parent as needing regrading

        // set new parent category
        $this->parent          = $parentid;
        $this->path            = null;       // remove old path and depth - will be recalculated in update()
        $this->parent_category = null;
        $this->update();

        $grade_item = $this->load_grade_item();
        $grade_item->parent_category = null;
        return $grade_item->update();               // marks new parent as needing regrading too
    }

    /**
     * Returns the final values for this grade category.
     * @param int $userid Optional: to retrieve a single final grade
     * @return mixed An array of all final_grades (stdClass objects) for this grade_item, or a single final_grade.
     */
    function get_final($userid=NULL) {
        $this->load_grade_item();
        return $this->grade_item->get_final($userid);
    }

    /**
     * Returns the sortorder of the associated grade_item. This method is also available in
     * grade_item, for cases where the object type is not known.
     * @return int Sort order
     */
    function get_sortorder() {
        $this->load_grade_item();
        return $this->grade_item->get_sortorder();
    }

    /**
     * Sets sortorder variable for this category.
     * This method is also available in grade_item, for cases where the object type is not know.
     * @param int $sortorder
     * @return void
     */
    function set_sortorder($sortorder) {
        $this->load_grade_item();
        $this->grade_item->set_sortorder($sortorder);
    }

    /**
     * Return true if this is the top most categroy that represents the total course grade.
     * @return boolean
     */
    function is_course_category() {
        $this->load_grade_item();
        return $this->grade_item->is_course_item();
    }

    /**
     * Return the top most course category.
     * @static
     * @return object grade_category instance for course grade
     */
    function fetch_course_category($courseid) {

        // course category has no parent
        if ($course_category = grade_category::fetch(array('courseid'=>$courseid, 'parent'=>null))) {
            return $course_category;
        }

        // create a new one
        $course_category = new grade_category();
        $course_category->insert_course_category($courseid);

        return $course_category;
    }

    /**
     * Returns the locked state/date of the associated grade_item. This method is also available in
     * grade_item, for cases where the object type is not known.
     * @return boolean
     */
    function is_locked() {
        $this->load_grade_item();
        return $this->grade_item->is_locked();
    }

    /**
     * Sets the grade_item's locked variable and updates the grade_item.
     * Method named after grade_item::set_locked().
     * @param int $locked 0, 1 or a timestamp int(10) after which date the item will be locked.
     * @return boolean success
     */
    function set_locked($lockedstate) {
        $this->load_grade_item();
        return $this->grade_item->set_locked($lockedstate);
    }

    /**
     * Returns the hidden state/date of the associated grade_item. This method is also available in
     * grade_item.
     * @return boolean
     */
    function is_hidden() {
        $this->load_grade_item();
        return $this->grade_item->is_hidden();
    }

    /**
     * Sets the grade_item's hidden variable and updates the grade_item.
     * Method named after grade_item::set_hidden().
     * @param int $hidden 0, 1 or a timestamp int(10) after which date the item will be hidden.
     * @return void
     */
    function set_hidden($hidden) {
        $this->load_grade_item();
        $this->grade_item->set_hidden($hidden);
    }

}
?>
