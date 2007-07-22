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
    var $nonfields = array('table', 'required_fields', 'nonfields', 'children', 'all_children', 'grade_item', 'parent_category', 'sortorder');

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
     * @param string $source from where was the object updated (mod/forum, manual, etc.)
     * @return boolean success
     */
    function update($source=null) {
        // load the grade item or create a new one
        $this->load_grade_item();

        // force recalculation of path;
        if (empty($this->path)) {
            $this->path  = grade_category::build_path($this);
            $this->depth = substr_count($this->path, '/');
        }


        // Recalculate grades if needed
        if ($this->qualifies_for_regrading()) {
            $this->force_regrading();
        }

        return parent::update($source);
    }

    /**
     * If parent::delete() is successful, send force_regrading message to parent category.
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return boolean success
     */
    function delete($source=null) {
        if ($this->is_course_category()) {
            debuggin('Can not delete top course category!');
            return false;
        }

        $this->force_regrading();

        $grade_item = $this->load_grade_item();
        $parent = $this->load_parent_category();

        // Update children's categoryid/parent field first
        if ($children = grade_item::fetch_all(array('categoryid'=>$this->id))) {
            foreach ($children as $child) {
                $child->set_parent($parent->id);
            }
        }
        if ($children = grade_category::fetch_all(array('parent'=>$this->id))) {
            foreach ($children as $child) {
                $child->set_parent($parent->id);
            }
        }

        // first delete the attached grade item and grades
        $grade_item->delete($source);

        // delete category itself
        return parent::delete($source);
    }

    /**
     * In addition to the normal insert() defined in grade_object, this method sets the depth
     * and path for this object, and update the record accordingly. The reason why this must
     * be done here instead of in the constructor, is that they both need to know the record's
     * id number, which only gets created at insertion time.
     * This method also creates an associated grade_item if this wasn't done during construction.
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return int PK ID if successful, false otherwise
     */
    function insert($source=null) {

        if (empty($this->courseid)) {
            error('Can not insert grade category without course id!');
        }

        if (empty($this->parent)) {
            $course_category = grade_category::fetch_course_category($this->courseid);
            $this->parent = $course_category->id;
        }

        $this->path = null;

        if (!parent::insert($source)) {
            debugging("Could not insert this category: " . print_r($this, true));
            return false;
        }

        $this->force_regrading();

        // build path and depth
        $this->update($source);

        return $this->id;
    }

    function insert_course_category($courseid) {
        $this->courseid = $courseid;
        $this->fullname = 'course grade category';
        $this->path     = null;
        $this->parent   = null;

        if (!parent::insert('system')) {
            debugging("Could not insert this category: " . print_r($this, true));
            return false;
        }

        // build path and depth
        $this->update('system');

        return $this->id;
    }

    /**
     * Compares the values held by this object with those of the matching record in DB, and returns
     * whether or not these differences are sufficient to justify an update of all parent objects.
     * This assumes that this object has an id number and a matching record in DB. If not, it will return false.
     * @return boolean
     */
    function qualifies_for_regrading() {
        if (empty($this->id)) {
            debugging("Can not regrade non existing category");
            return false;
        }

        $db_item = grade_category::fetch(array('id'=>$this->id));

        $aggregationdiff = $db_item->aggregation != $this->aggregation;
        $keephighdiff    = $db_item->keephigh    != $this->keephigh;
        $droplowdiff     = $db_item->droplow     != $this->droplow;

        return ($aggregationdiff || $keephighdiff || $droplowdiff);
    }

    /**
     * Marks the category and course item as needing update - categories are always regraded.
     * @return void
     */
    function force_regrading() {
        $grade_item = $this->load_grade_item();
        $grade_item->force_regrading();
    }

    /**
     * Generates and saves raw_grades in associated category grade item.
     * These immediate children must alrady have their own final grades.
     * The category's aggregation method is used to generate raw grades.
     *
     * Please note that category grade is either calculated or aggregated - not both at the same time.
     *
     * This method must be used ONLY from grade_item::regrade_final_grades(),
     * because the calculation must be done in correct order!
     *
     * Steps to follow:
     *  1. Get final grades from immediate children
     *  3. Aggregate these grades
     *  4. Save them in raw grades of associated category grade item
     */
    function generate_grades($userid=null) {
        global $CFG;

        $this->load_grade_item();

        if ($this->grade_item->is_locked()) {
            return true; // no need to recalculate locked items
        }

        $this->grade_item->load_scale();

        // find grade items of immediate children (category or grade items)
        $depends_on = $this->grade_item->depends_on();

        if (empty($depends_on)) {
            $items = false;
        } else {
            $gis = implode(',', $depends_on);
            $sql = "SELECT *
                      FROM {$CFG->prefix}grade_items
                     WHERE id IN ($gis)";
            $items = get_records_sql($sql);
        }

        if ($userid) {
            $usersql = "AND g.userid=$userid";
        } else {
            $usersql = "";
        }

        // where to look for final grades - include grade of this item too, we will store the results there
        $gis = implode(',', array_merge($depends_on, array($this->grade_item->id)));
        $sql = "SELECT g.*
                  FROM {$CFG->prefix}grade_grades g, {$CFG->prefix}grade_items gi
                 WHERE gi.id = g.itemid AND gi.id IN ($gis) $usersql
              ORDER BY g.userid";

        // group the results by userid and aggregate the grades for this user
        if ($rs = get_recordset_sql($sql)) {
            if ($rs->RecordCount() > 0) {
                $prevuser = 0;
                $grade_values = array();
                $excluded     = array();
                $oldgrade     = null;
                while ($used = rs_fetch_next_record($rs)) {
                    if ($used->userid != $prevuser) {
                        $this->aggregate_grades($prevuser, $items, $grade_values, $oldgrade, $excluded);
                        $prevuser = $used->userid;
                        $grade_values = array();
                        $excluded     = array();
                        $oldgrade     = null;
                    }
                    $grade_values[$used->itemid] = $used->finalgrade;
                    if ($used->excluded) {
                        $excluded[] = $used->itemid;
                    }
                    if ($this->grade_item->id == $used->itemid) {
                        $oldgrade = $used;
                    }
                }
                $this->aggregate_grades($prevuser, $items, $grade_values, $oldgrade, $excluded);//the last one
            }
            rs_close($rs);
        }

        return true;
    }

    /**
     * internal function for category grades aggregation
     */
    function aggregate_grades($userid, $items, $grade_values, $oldgrade, $excluded) {
        if (empty($userid)) {
            //ignore first call
            return;
        }

        if ($oldgrade) {
            $grade = new grade_grade($oldgrade, false);
            $grade->grade_item =& $this->grade_item;

        } else {
            // insert final grade - it will be needed later anyway
            $grade = new grade_grade(array('itemid'=>$this->grade_item->id, 'userid'=>$userid), false);
            $grade->insert('system');
            $grade->grade_item =& $this->grade_item;

            $oldgrade = new object();
            $oldgrade->finalgrade  = $grade->finalgrade;
            $oldgrade->rawgrade    = $grade->rawgrade;
            $oldgrade->rawgrademin = $grade->rawgrademin;
            $oldgrade->rawgrademax = $grade->rawgrademax;
            $oldgrade->rawscaleid  = $grade->rawscaleid;
        }

        // no need to recalculate locked or overridden grades
        if ($grade->is_locked() or $grade->is_overridden()) {
            return;
        }

        // can not use own final category grade in calculation
        unset($grade_values[$this->grade_item->id]);

        // if no grades calculation possible or grading not allowed clear both final and raw
        if (empty($grade_values) or empty($items) or ($this->grade_item->gradetype != GRADE_TYPE_VALUE and $this->grade_item->gradetype != GRADE_TYPE_SCALE)) {
            $grade->finalgrade = null;
            $grade->rawgrade   = null;
            if ($grade->finalgrade !== $oldgrade->finalgrade or $grade->rawgrade !== $oldgrade->rawgrade) {
                $grade->update('system');
            }
            return;
        }

    /// normalize the grades first - all will have value 0...1
        // ungraded items are not used in aggregation
        foreach ($grade_values as $itemid=>$v) {
            if (is_null($v)) {
                // null means no grade
                unset($grade_values[$itemid]);
                continue;
            } else if (in_array($itemid, $excluded)) {
                unset($grade_values[$itemid]);
                continue;
            }

            $grade_values[$itemid] = grade_grade::standardise_score($v, $items[$itemid]->grademin, $items[$itemid]->grademax, 0, 1);
        }

        // use min grade if grade missing for these types
        switch ($this->aggregation) {
            case GRADE_AGGREGATE_MEAN_ALL:
            case GRADE_AGGREGATE_MEDIAN_ALL:
            case GRADE_AGGREGATE_MIN_ALL:
            case GRADE_AGGREGATE_MAX_ALL:
            case GRADE_AGGREGATE_MODE_ALL:
            case GRADE_AGGREGATE_WEIGHTED_MEAN_ALL:
            case GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL:
                foreach($items as $itemid=>$value) {
                    if (!isset($grade_values[$itemid]) and !in_array($itemid, $excluded)) {
                        $grade_values[$itemid] = 0;
                    }
                }
                break;
        }

        // limit and sort
        $this->apply_limit_rules($grade_values);
        asort($grade_values, SORT_NUMERIC);

        // let's see we have still enough grades to do any statistics
        if (count($grade_values) == 0) {
            // not enough attempts yet
            $grade->finalgrade = null;
            $grade->rawgrade   = null;
            if ($grade->finalgrade !== $oldgrade->finalgrade or $grade->rawgrade !== $oldgrade->rawgrade) {
                $grade->update('system');
            }
            return;
        }

    /// start the aggregation
        switch ($this->aggregation) {
            case GRADE_AGGREGATE_MEDIAN_ALL: // Middle point value in the set: ignores frequencies
            case GRADE_AGGREGATE_MEDIAN_GRADED:
                $num = count($grade_values);
                $grades = array_values($grade_values);
                if ($num % 2 == 0) {
                    $rawgrade = ($grades[intval($num/2)-1] + $grades[intval($num/2)]) / 2;
                } else {
                    $rawgrade = $grades[intval(($num/2)-0.5)];
                }
                break;

            case GRADE_AGGREGATE_MIN_ALL:
            case GRADE_AGGREGATE_MIN_GRADED:
                $rawgrade = reset($grade_values);
                break;

            case GRADE_AGGREGATE_MAX_ALL:
            case GRADE_AGGREGATE_MAX_GRADED:
                $rawgrade = array_pop($grade_values);
                break;

            case GRADE_AGGREGATE_MODE_ALL:       // the most common value, average used if multimode
            case GRADE_AGGREGATE_MODE_GRADED:
                $freq = array_count_values($grade_values);
                arsort($freq);                      // sort by frequency keeping keys
                $top = reset($freq);               // highest frequency count
                $modes = array_keys($freq, $top);  // search for all modes (have the same highest count)
                rsort($modes, SORT_NUMERIC);       // get highes mode
                $rawgrade = reset($modes);
                break;

            case GRADE_AGGREGATE_WEIGHTED_MEAN_GRADED: // Weighted average of all existing final grades
            case GRADE_AGGREGATE_WEIGHTED_MEAN_ALL:
                $weightsum = 0;
                $sum       = 0;
                foreach($grade_values as $itemid=>$grade_value) {
                    if ($items[$itemid]->aggregationcoef <= 0) {
                        continue;
                    }
                    $weightsum += $items[$itemid]->aggregationcoef;
                    $sum       += $items[$itemid]->aggregationcoef * $grade_value;
                }
                if ($weightsum == 0) {
                    $rawgrade = null;
                } else {
                    $rawgrade = $sum / $weightsum;
                }
                break;

            case GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL: // special average
            case GRADE_AGGREGATE_EXTRACREDIT_MEAN_GRADED:
                $num = 0;
                $sum = 0;
                foreach($grade_values as $itemid=>$grade_value) {
                    if ($items[$itemid]->aggregationcoef == 0) {
                        $num += 1;
                        $sum += $grade_value;
                    } else if ($items[$itemid]->aggregationcoef > 0) {
                        $sum += $items[$itemid]->aggregationcoef * $grade_value;
                    }
                }
                if ($num == 0) {
                    $rawgrade = $sum; // only extra credits or wrong coefs
                } else {
                    $rawgrade = $sum / $num;
                }
                break;

            case GRADE_AGGREGATE_MEAN_ALL:    // Arithmetic average of all grade items including even NULLs; NULL grade counted as minimum
            case GRADE_AGGREGATE_MEAN_GRADED: // Arithmetic average of all final grades, unfinished are not calculated
            default:
                $num = count($grade_values);
                $sum = array_sum($grade_values);
                $rawgrade = $sum / $num;
                break;
        }

    /// prepare update of new raw grade
        $grade->rawgrademin = $this->grade_item->grademin;
        $grade->rawgrademax = $this->grade_item->grademax;
        $grade->rawscaleid  = $this->grade_item->scaleid;

        // recalculate the rawgrade back to requested range
        $grade->rawgrade = grade_grade::standardise_score($rawgrade, 0, 1, $grade->rawgrademin, $grade->rawgrademax);

        // calculate final grade
        $grade->finalgrade = $this->grade_item->adjust_grade($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);

        // update in db if changed
        if (   $grade->finalgrade  !== $oldgrade->finalgrade
            or $grade->rawgrade    !== $oldgrade->rawgrade
            or $grade->rawgrademin !== $oldgrade->rawgrademin
            or $grade->rawgrademax !== $oldgrade->rawgrademax
            or $grade->rawscaleid  !== $oldgrade->rawscaleid) {

            $grade->update('system');
        }

        return;
    }

    /**
     * Given an array of grade values (numerical indices), applies droplow or keephigh
     * rules to limit the final array.
     * @param array $grade_values
     * @return array Limited grades.
     */
    function apply_limit_rules(&$grade_values) {
        arsort($grade_values, SORT_NUMERIC);
        if (!empty($this->droplow)) {
            for ($i = 0; $i < $this->droplow; $i++) {
                array_pop($grade_values);
            }
        } elseif (!empty($this->keephigh)) {
            while (count($grade_values) > $this->keephigh) {
                array_pop($grade_values);
            }
        }
    }


    /**
     * Returns true if category uses special aggregation coeficient
     * @return boolean true if coeficient used
     */
    function is_aggregationcoef_used() {
        return ($this->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN_ALL
             or $this->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN_GRADED
             or $this->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL
             or $this->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN_GRADED);

    }

    /**
     * Returns true if this category has any child grade_category or grade_item.
     * @return int number of direct children, or false if none found.
     */
    function has_children() {
        return count_records('grade_categories', 'parent', $this->id) + count_records('grade_items', 'categoryid', $this->id);
    }

    /**
     * Returns tree with all grade_items and categories as elements
     * @static
     * @param int $courseid
     * @param boolean $include_category_items as category children
     * @return array
     */
    function fetch_course_tree($courseid, $include_category_items=false) {
        $course_category = grade_category::fetch_course_category($courseid);
        $category_array = array('object'=>$course_category, 'type'=>'category', 'depth'=>1,
                                'children'=>$course_category->get_children($include_category_items));
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
    function get_children($include_category_items=false) {

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
                //debugging("$sortorder exists in item loop");
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
                    //debugging("$sortorder exists in cat loop");
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

        $children_array = grade_category::_get_children_recursion($category);

        ksort($children_array);

        return $children_array;

    }

    function _get_children_recursion($category) {

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
                $children = grade_category::_get_children_recursion($child);
                $grade_category = new grade_category($child, false);
                if (empty($children)) {
                    $children = array();
                }
                $children_array[$sortorder] = array('object'=>$grade_category, 'type'=>'category', 'depth'=>$grade_category->depth, 'children'=>$children);
            }
        }

        // sort the array
        ksort($children_array);

        return $children_array;
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
            $grade_item->insert('system');

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
     * Sets this category's parent id. A generic method shared by objects that have a parent id of some kind.
     * @param int parentid
     * @return boolean success
     */
    function set_parent($parentid, $source=null) {
        if ($this->parent == $parentid) {
            return true;
        }

        if ($parentid == $this->id) {
            error('Can not assign self as parent!');
        }

        if (empty($this->parent) and $this->is_course_category()) {
            error('Course category can not have parent!');
        }

        // find parent and check course id
        if (!$parent_category = grade_category::fetch(array('id'=>$parentid, 'courseid'=>$this->courseid))) {
            return false;
        }

        $this->force_regrading();

        // set new parent category
        $this->parent          = $parent_category->id;
        $this->parent_category =& $parent_category;
        $this->path            = null;       // remove old path and depth - will be recalculated in update()
        $this->depth           = null;       // remove old path and depth - will be recalculated in update()
        $this->update($source);

        return $this->update($source);
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
     * Move this category after the given sortorder - does not change the parent
     * @param int $sortorder to place after
     */
    function move_after_sortorder($sortorder) {
        $this->load_grade_item();
        $this->grade_item->move_after_sortorder($sortorder);
    }

    /**
     * Return true if this is the top most category that represents the total course grade.
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
     * Is grading object editable?
     * @return boolean
     */
    function is_editable() {
        return true;
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
     * @return boolean success if category locked (not all children mayb be locked though)
     */
    function set_locked($lockedstate) {
        $this->load_grade_item();
        $result = $this->grade_item->set_locked($lockedstate);
        if ($children = grade_item::fetch_all(array('categoryid'=>$this->id))) {
            foreach($children as $child) {
                $child->set_locked($lockedstate);
            }
        }
        if ($children = grade_category::fetch_all(array('parent'=>$this->id))) {
            foreach($children as $child) {
                $child->set_locked($lockedstate);
            }
        }
        return $result;
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
        if ($children = grade_item::fetch_all(array('categoryid'=>$this->id))) {
            foreach($children as $child) {
                $child->set_hidden($hidden);
            }
        }
        if ($children = grade_category::fetch_all(array('parent'=>$this->id))) {
            foreach($children as $child) {
                $child->set_hidden($hidden);
            }
        }
    }

}
?>
