<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Definition of a class to represent a grade category
 *
 * @package   core_grades
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/grade_object.php');

/**
 * grade_category is an object mapped to DB table {prefix}grade_categories
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_category extends grade_object {
    /**
     * The DB table.
     * @var string $table
     */
    public $table = 'grade_categories';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'courseid', 'parent', 'depth', 'path', 'fullname', 'aggregation',
                                 'keephigh', 'droplow', 'aggregateonlygraded', 'aggregateoutcomes',
                                 'timecreated', 'timemodified', 'hidden');

    /**
     * The course this category belongs to.
     * @var int $courseid
     */
    public $courseid;

    /**
     * The category this category belongs to (optional).
     * @var int $parent
     */
    public $parent;

    /**
     * The grade_category object referenced by $this->parent (PK).
     * @var grade_category $parent_category
     */
    public $parent_category;

    /**
     * The number of parents this category has.
     * @var int $depth
     */
    public $depth = 0;

    /**
     * Shows the hierarchical path for this category as /1/2/3/ (like course_categories), the last number being
     * this category's autoincrement ID number.
     * @var string $path
     */
    public $path;

    /**
     * The name of this category.
     * @var string $fullname
     */
    public $fullname;

    /**
     * A constant pointing to one of the predefined aggregation strategies (none, mean, median, sum etc) .
     * @var int $aggregation
     */
    public $aggregation = GRADE_AGGREGATE_SUM;

    /**
     * Keep only the X highest items.
     * @var int $keephigh
     */
    public $keephigh = 0;

    /**
     * Drop the X lowest items.
     * @var int $droplow
     */
    public $droplow = 0;

    /**
     * Aggregate only graded items
     * @var int $aggregateonlygraded
     */
    public $aggregateonlygraded = 0;

    /**
     * Aggregate outcomes together with normal items
     * @var int $aggregateoutcomes
     */
    public $aggregateoutcomes = 0;

    /**
     * Array of grade_items or grade_categories nested exactly 1 level below this category
     * @var array $children
     */
    public $children;

    /**
     * A hierarchical array of all children below this category. This is stored separately from
     * $children because it is more memory-intensive and may not be used as often.
     * @var array $all_children
     */
    public $all_children;

    /**
     * An associated grade_item object, with itemtype=category, used to calculate and cache a set of grade values
     * for this category.
     * @var grade_item $grade_item
     */
    public $grade_item;

    /**
     * Temporary sortorder for speedup of children resorting
     * @var int $sortorder
     */
    public $sortorder;

    /**
     * List of options which can be "forced" from site settings.
     * @var array $forceable
     */
    public $forceable = array('aggregation', 'keephigh', 'droplow', 'aggregateonlygraded', 'aggregateoutcomes');

    /**
     * String representing the aggregation coefficient. Variable is used as cache.
     * @var string $coefstring
     */
    public $coefstring = null;

    /**
     * Static variable storing the result from {@link self::can_apply_limit_rules}.
     * @var bool
     */
    protected $canapplylimitrules;

    /**
     * e.g. 'category', 'course' and 'mod', 'blocks', 'import', etc...
     * @var string $itemtype
     */
    public $itemtype;

    /**
     * Builds this category's path string based on its parents (if any) and its own id number.
     * This is typically done just before inserting this object in the DB for the first time,
     * or when a new parent is added or changed. It is a recursive function: once the calling
     * object no longer has a parent, the path is complete.
     *
     * @param grade_category $grade_category A Grade_Category object
     * @return string The category's path string
     */
    public static function build_path($grade_category) {
        global $DB;

        if (empty($grade_category->parent)) {
            return '/'.$grade_category->id.'/';

        } else {
            $parent = $DB->get_record('grade_categories', array('id' => $grade_category->parent));
            return grade_category::build_path($parent).$grade_category->id.'/';
        }
    }

    /**
     * Finds and returns a grade_category instance based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return grade_category The retrieved grade_category instance or false if none found.
     */
    public static function fetch($params) {
        if ($records = self::retrieve_record_set($params)) {
            return reset($records);
        }

        $record = grade_object::fetch_helper('grade_categories', 'grade_category', $params);

        // We store it as an array to keep a key => result set interface in the cache, grade_object::fetch_helper is
        // managing exceptions. We return only the first element though.
        $records = false;
        if ($record) {
            $records = array($record->id => $record);
        }

        self::set_record_set($params, $records);

        return $record;
    }

    /**
     * Finds and returns all grade_category instances based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_category insatnces or false if none found.
     */
    public static function fetch_all($params) {
        if ($records = self::retrieve_record_set($params)) {
            return $records;
        }

        $records = grade_object::fetch_all_helper('grade_categories', 'grade_category', $params);
        self::set_record_set($params, $records);

        return $records;
    }

    /**
     * In addition to update() as defined in grade_object, call force_regrading of parent categories, if applicable.
     *
     * @param string $source from where was the object updated (mod/forum, manual, etc.)
     * @param bool $isbulkupdate If bulk grade update is happening.
     * @return bool success
     */
    public function update($source = null, $isbulkupdate = false) {
        // load the grade item or create a new one
        $this->load_grade_item();

        // force recalculation of path;
        if (empty($this->path)) {
            $this->path  = grade_category::build_path($this);
            $this->depth = substr_count($this->path, '/') - 1;
            $updatechildren = true;

        } else {
            $updatechildren = false;
        }

        $this->apply_forced_settings();

        // these are exclusive
        if ($this->droplow > 0) {
            $this->keephigh = 0;

        } else if ($this->keephigh > 0) {
            $this->droplow = 0;
        }

        // Recalculate grades if needed
        if ($this->qualifies_for_regrading()) {
            $this->force_regrading();
        }

        $this->timemodified = time();

        $result = parent::update($source);

        // now update paths in all child categories
        if ($result and $updatechildren) {

            if ($children = grade_category::fetch_all(array('parent'=>$this->id))) {

                foreach ($children as $child) {
                    $child->path  = null;
                    $child->depth = 0;
                    $child->update($source);
                }
            }
        }

        return $result;
    }

    /**
     * If parent::delete() is successful, send force_regrading message to parent category.
     *
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return bool success
     */
    public function delete($source=null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $grade_item = $this->load_grade_item();

        if ($this->is_course_category()) {

            if ($categories = grade_category::fetch_all(array('courseid'=>$this->courseid))) {

                foreach ($categories as $category) {

                    if ($category->id == $this->id) {
                        continue; // do not delete course category yet
                    }
                    $category->delete($source);
                }
            }

            if ($items = grade_item::fetch_all(array('courseid'=>$this->courseid))) {

                foreach ($items as $item) {

                    if ($item->id == $grade_item->id) {
                        continue; // do not delete course item yet
                    }
                    $item->delete($source);
                }
            }

        } else {
            $this->force_regrading();

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
        }

        // first delete the attached grade item and grades
        $grade_item->delete($source);

        // delete category itself
        $success = parent::delete($source);

        $transaction->allow_commit();
        return $success;
    }

    /**
     * In addition to the normal insert() defined in grade_object, this method sets the depth
     * and path for this object, and update the record accordingly.
     *
     * We do this here instead of in the constructor as they both need to know the record's
     * ID number, which only gets created at insertion time.
     * This method also creates an associated grade_item if this wasn't done during construction.
     *
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @param bool $isbulkupdate If bulk grade update is happening.
     * @return int PK ID if successful, false otherwise
     */
    public function insert($source = null, $isbulkupdate = false) {

        if (empty($this->courseid)) {
            throw new \moodle_exception('cannotinsertgrade');
        }

        if (empty($this->parent)) {
            $course_category = grade_category::fetch_course_category($this->courseid);
            $this->parent = $course_category->id;
        }

        $this->path = null;

        $this->timecreated = $this->timemodified = time();

        if (!parent::insert($source)) {
            debugging("Could not insert this category: " . print_r($this, true));
            return false;
        }

        $this->force_regrading();

        // build path and depth
        $this->update($source);

        return $this->id;
    }

    /**
     * Internal function - used only from fetch_course_category()
     * Normal insert() can not be used for course category
     *
     * @param int $courseid The course ID
     * @return int The ID of the new course category
     */
    public function insert_course_category($courseid) {
        $this->courseid    = $courseid;
        $this->fullname    = '?';
        $this->path        = null;
        $this->parent      = null;
        $this->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2;

        $this->apply_default_settings();
        $this->apply_forced_settings();

        $this->timecreated = $this->timemodified = time();

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
     * This assumes that this object has an ID number and a matching record in DB. If not, it will return false.
     *
     * @return bool
     */
    public function qualifies_for_regrading() {
        if (empty($this->id)) {
            debugging("Can not regrade non existing category");
            return false;
        }

        $db_item = grade_category::fetch(array('id'=>$this->id));

        $aggregationdiff = $db_item->aggregation         != $this->aggregation;
        $keephighdiff    = $db_item->keephigh            != $this->keephigh;
        $droplowdiff     = $db_item->droplow             != $this->droplow;
        $aggonlygrddiff  = $db_item->aggregateonlygraded != $this->aggregateonlygraded;
        $aggoutcomesdiff = $db_item->aggregateoutcomes   != $this->aggregateoutcomes;

        return ($aggregationdiff || $keephighdiff || $droplowdiff || $aggonlygrddiff || $aggoutcomesdiff);
    }

    /**
     * Marks this grade categories' associated grade item as needing regrading
     */
    public function force_regrading() {
        $grade_item = $this->load_grade_item();
        $grade_item->force_regrading();
    }

    /**
     * Something that should be called before we start regrading the whole course.
     *
     * @return void
     */
    public function pre_regrade_final_grades() {
        $this->auto_update_weights();
        $this->auto_update_max();
    }

    /**
     * Generates and saves final grades in associated category grade item.
     * These immediate children must already have their own final grades.
     * The category's aggregation method is used to generate final grades.
     *
     * Please note that category grade is either calculated or aggregated, not both at the same time.
     *
     * This method must be used ONLY from grade_item::regrade_final_grades(),
     * because the calculation must be done in correct order!
     *
     * Steps to follow:
     *  1. Get final grades from immediate children
     *  3. Aggregate these grades
     *  4. Save them in final grades of associated category grade item
     *
     * @param int $userid The user ID if final grade generation should be limited to a single user
     * @param \core\progress\base|null $progress Optional progress indicator
     * @return bool
     */
    public function generate_grades($userid=null, ?\core\progress\base $progress = null) {
        global $CFG, $DB;

        $this->load_grade_item();

        if ($this->grade_item->is_locked()) {
            return true; // no need to recalculate locked items
        }

        // find grade items of immediate children (category or grade items) and force site settings
        $depends_on = $this->grade_item->depends_on();

        if (empty($depends_on)) {
            $items = false;

        } else {
            list($usql, $params) = $DB->get_in_or_equal($depends_on);
            $sql = "SELECT *
                      FROM {grade_items}
                     WHERE id $usql";
            $items = $DB->get_records_sql($sql, $params);
            foreach ($items as $id => $item) {
                $items[$id] = new grade_item($item, false);
            }
        }

        $grade_inst = new grade_grade();
        $fields = 'g.'.implode(',g.', $grade_inst->required_fields);

        // where to look for final grades - include grade of this item too, we will store the results there
        $gis = array_merge($depends_on, array($this->grade_item->id));
        list($usql, $params) = $DB->get_in_or_equal($gis);

        if ($userid) {
            $usersql = "AND g.userid=?";
            $params[] = $userid;

        } else {
            $usersql = "";
        }

        $sql = "SELECT $fields
                  FROM {grade_grades} g, {grade_items} gi
                 WHERE gi.id = g.itemid AND gi.id $usql $usersql
              ORDER BY g.userid";

        // group the results by userid and aggregate the grades for this user
        $rs = $DB->get_recordset_sql($sql, $params);
        if ($rs->valid()) {
            $prevuser = 0;
            $grade_values = array();
            $excluded     = array();
            $oldgrade     = null;
            $grademaxoverrides = array();
            $grademinoverrides = array();

            foreach ($rs as $used) {
                $grade = new grade_grade($used, false);
                if (isset($items[$grade->itemid])) {
                    // Prevent grade item to be fetched from DB.
                    $grade->grade_item =& $items[$grade->itemid];
                } else if ($grade->itemid == $this->grade_item->id) {
                    // This grade's grade item is not in $items.
                    $grade->grade_item =& $this->grade_item;
                }
                if ($grade->userid != $prevuser) {
                    $this->aggregate_grades($prevuser,
                                            $items,
                                            $grade_values,
                                            $oldgrade,
                                            $excluded,
                                            $grademinoverrides,
                                            $grademaxoverrides);
                    $prevuser = $grade->userid;
                    $grade_values = array();
                    $excluded     = array();
                    $oldgrade     = null;
                    $grademaxoverrides = array();
                    $grademinoverrides = array();
                }
                $grade_values[$grade->itemid] = $grade->finalgrade;
                $grademaxoverrides[$grade->itemid] = $grade->get_grade_max();
                $grademinoverrides[$grade->itemid] = $grade->get_grade_min();

                if ($grade->excluded) {
                    $excluded[] = $grade->itemid;
                }

                if ($this->grade_item->id == $grade->itemid) {
                    $oldgrade = $grade;
                }

                if ($progress) {
                    // Incrementing the progress by nothing causes it to send an update (once per second)
                    // to the web browser so as to prevent the connection timing out.
                    $progress->increment_progress(0);
                }
            }
            $this->aggregate_grades($prevuser,
                                    $items,
                                    $grade_values,
                                    $oldgrade,
                                    $excluded,
                                    $grademinoverrides,
                                    $grademaxoverrides);//the last one
        }
        $rs->close();

        return true;
    }

    /**
     * Internal function for grade category grade aggregation
     *
     * @param int    $userid The User ID
     * @param array  $items Grade items
     * @param array  $grade_values Array of grade values
     * @param object $oldgrade Old grade
     * @param array  $excluded Excluded
     * @param array  $grademinoverrides User specific grademin values if different to the grade_item grademin (key is itemid)
     * @param array  $grademaxoverrides User specific grademax values if different to the grade_item grademax (key is itemid)
     */
    private function aggregate_grades($userid,
                                      $items,
                                      $grade_values,
                                      $oldgrade,
                                      $excluded,
                                      $grademinoverrides,
                                      $grademaxoverrides) {
        global $CFG, $DB;

        // Remember these so we can set flags on them to describe how they were used in the aggregation.
        $novalue = array();
        $dropped = array();
        $extracredit = array();
        $usedweights = array();

        if (empty($userid)) {
            //ignore first call
            return;
        }

        if ($oldgrade) {
            $oldfinalgrade = $oldgrade->finalgrade;
            $grade = new grade_grade($oldgrade, false);
            $grade->grade_item =& $this->grade_item;

        } else {
            // insert final grade - it will be needed later anyway
            $grade = new grade_grade(array('itemid'=>$this->grade_item->id, 'userid'=>$userid), false);
            $grade->grade_item =& $this->grade_item;
            $grade->insert('system');
            $oldfinalgrade = null;
        }

        // no need to recalculate locked or overridden grades
        if ($grade->is_locked() or $grade->is_overridden()) {
            return;
        }

        // can not use own final category grade in calculation
        unset($grade_values[$this->grade_item->id]);

        // Make sure a grade_grade exists for every grade_item.
        // We need to do this so we can set the aggregationstatus
        // with a set_field call instead of checking if each one exists and creating/updating.
        if (!empty($items)) {
            list($ggsql, $params) = $DB->get_in_or_equal(array_keys($items), SQL_PARAMS_NAMED, 'g');


            $params['userid'] = $userid;
            $sql = "SELECT itemid
                      FROM {grade_grades}
                     WHERE itemid $ggsql AND userid = :userid";
            $existingitems = $DB->get_records_sql($sql, $params);

            $notexisting = array_diff(array_keys($items), array_keys($existingitems));
            foreach ($notexisting as $itemid) {
                $gradeitem = $items[$itemid];
                $gradegrade = new grade_grade(array('itemid' => $itemid,
                                                    'userid' => $userid,
                                                    'rawgrademin' => $gradeitem->grademin,
                                                    'rawgrademax' => $gradeitem->grademax), false);
                $gradegrade->grade_item = $gradeitem;
                $gradegrade->insert('system');
            }
        }

        // if no grades calculation possible or grading not allowed clear final grade
        if (empty($grade_values) or empty($items) or ($this->grade_item->gradetype != GRADE_TYPE_VALUE and $this->grade_item->gradetype != GRADE_TYPE_SCALE)) {
            $grade->finalgrade = null;

            if (!is_null($oldfinalgrade)) {
                $grade->timemodified = time();
                $success = $grade->update('aggregation');

                // If successful trigger a user_graded event.
                if ($success) {
                    \core\event\user_graded::create_from_grade($grade, \core\event\base::USER_OTHER)->trigger();
                }
            }
            $dropped = $grade_values;
            $this->set_usedinaggregation($userid, $usedweights, $novalue, $dropped, $extracredit);
            return;
        }

        // Normalize the grades first - all will have value 0...1
        // ungraded items are not used in aggregation.
        foreach ($grade_values as $itemid=>$v) {
            if (is_null($v)) {
                // If null, it means no grade.
                if ($this->aggregateonlygraded) {
                    unset($grade_values[$itemid]);
                    // Mark this item as "excluded empty" because it has no grade.
                    $novalue[$itemid] = 0;
                    continue;
                }
            }
            if (in_array($itemid, $excluded)) {
                unset($grade_values[$itemid]);
                $dropped[$itemid] = 0;
                continue;
            }
            // Check for user specific grade min/max overrides.
            $usergrademin = $items[$itemid]->grademin;
            $usergrademax = $items[$itemid]->grademax;
            if (isset($grademinoverrides[$itemid])) {
                $usergrademin = $grademinoverrides[$itemid];
            }
            if (isset($grademaxoverrides[$itemid])) {
                $usergrademax = $grademaxoverrides[$itemid];
            }
            if ($this->aggregation == GRADE_AGGREGATE_SUM) {
                // Assume that the grademin is 0 when standardising the score, to preserve negative grades.
                $grade_values[$itemid] = grade_grade::standardise_score($v, 0, $usergrademax, 0, 1);
            } else {
                $grade_values[$itemid] = grade_grade::standardise_score($v, $usergrademin, $usergrademax, 0, 1);
            }

        }

        // First, check if all grades are null, because the final grade will be null
        // even when aggreateonlygraded is true.
        $allnull = true;
        foreach ($grade_values as $v) {
            if (!is_null($v)) {
                $allnull = false;
                break;
            }
        }

        // For items with no value, and not excluded - either set their grade to 0 or exclude them.
        foreach ($items as $itemid=>$value) {
            if (!isset($grade_values[$itemid]) and !in_array($itemid, $excluded)) {
                if (!$this->aggregateonlygraded) {
                    $grade_values[$itemid] = 0;
                } else {
                    // We are specifically marking these items as "excluded empty".
                    $novalue[$itemid] = 0;
                }
            }
        }

        // limit and sort
        $allvalues = $grade_values;
        if ($this->can_apply_limit_rules()) {
            $this->apply_limit_rules($grade_values, $items);
        }

        $moredropped = array_diff($allvalues, $grade_values);
        foreach ($moredropped as $drop => $unused) {
            $dropped[$drop] = 0;
        }

        foreach ($grade_values as $itemid => $val) {
            if (self::is_extracredit_used() && ($items[$itemid]->aggregationcoef > 0)) {
                $extracredit[$itemid] = 0;
            }
        }

        asort($grade_values, SORT_NUMERIC);

        // let's see we have still enough grades to do any statistics
        if (count($grade_values) == 0) {
            // not enough attempts yet
            $grade->finalgrade = null;

            if (!is_null($oldfinalgrade)) {
                $grade->timemodified = time();
                $success = $grade->update('aggregation');

                // If successful trigger a user_graded event.
                if ($success) {
                    \core\event\user_graded::create_from_grade($grade, \core\event\base::USER_OTHER)->trigger();
                }
            }
            $this->set_usedinaggregation($userid, $usedweights, $novalue, $dropped, $extracredit);
            return;
        }

        // do the maths
        $result = $this->aggregate_values_and_adjust_bounds($grade_values,
                                                            $items,
                                                            $usedweights,
                                                            $grademinoverrides,
                                                            $grademaxoverrides);
        $agg_grade = $result['grade'];

        // Set the actual grademin and max to bind the grade properly.
        $this->grade_item->grademin = $result['grademin'];
        $this->grade_item->grademax = $result['grademax'];

        if ($this->aggregation == GRADE_AGGREGATE_SUM) {
            // The natural aggregation always displays the range as coming from 0 for categories.
            // However, when we bind the grade we allow for negative values.
            $result['grademin'] = 0;
        }

        if ($allnull) {
            $grade->finalgrade = null;
        } else {
            // Recalculate the grade back to requested range.
            $finalgrade = grade_grade::standardise_score($agg_grade, 0, 1, $result['grademin'], $result['grademax']);
            $grade->finalgrade = $this->grade_item->bounded_grade($finalgrade);
        }

        $oldrawgrademin = $grade->rawgrademin;
        $oldrawgrademax = $grade->rawgrademax;
        $grade->rawgrademin = $result['grademin'];
        $grade->rawgrademax = $result['grademax'];

        // Update in db if changed.
        if (grade_floats_different($grade->finalgrade, $oldfinalgrade) ||
                grade_floats_different($grade->rawgrademax, $oldrawgrademax) ||
                grade_floats_different($grade->rawgrademin, $oldrawgrademin)) {
            $grade->timemodified = time();
            $success = $grade->update('aggregation');

            // If successful trigger a user_graded event.
            if ($success) {
                \core\event\user_graded::create_from_grade($grade, \core\event\base::USER_OTHER)->trigger();
            }
        }

        $this->set_usedinaggregation($userid, $usedweights, $novalue, $dropped, $extracredit);

        return;
    }

    /**
     * Set the flags on the grade_grade items to indicate how individual grades are used
     * in the aggregation.
     *
     * WARNING: This function is called a lot during gradebook recalculation, be very performance considerate.
     *
     * @param int $userid The user we have aggregated the grades for.
     * @param array $usedweights An array with keys for each of the grade_item columns included in the aggregation. The value are the relative weight.
     * @param array $novalue An array with keys for each of the grade_item columns skipped because
     *                       they had no value in the aggregation.
     * @param array $dropped An array with keys for each of the grade_item columns dropped
     *                       because of any drop lowest/highest settings in the aggregation.
     * @param array $extracredit An array with keys for each of the grade_item columns
     *                       considered extra credit by the aggregation.
     */
    private function set_usedinaggregation($userid, $usedweights, $novalue, $dropped, $extracredit) {
        global $DB;

        // We want to know all current user grades so we can decide whether they need to be updated or they already contain the
        // expected value.
        $sql = "SELECT gi.id, gg.aggregationstatus, gg.aggregationweight FROM {grade_grades} gg
                  JOIN {grade_items} gi ON (gg.itemid = gi.id)
                 WHERE gg.userid = :userid";
        $params = array('categoryid' => $this->id, 'userid' => $userid);

        // These are all grade_item ids which grade_grades will NOT end up being 'unknown' (because they are not unknown or
        // because we will update them to something different that 'unknown').
        $giids = array_keys($usedweights + $novalue + $dropped + $extracredit);

        if ($giids) {
            // We include grade items that might not be in categoryid.
            list($itemsql, $itemlist) = $DB->get_in_or_equal($giids, SQL_PARAMS_NAMED, 'gg');
            $sql .= ' AND (gi.categoryid = :categoryid OR gi.id ' . $itemsql . ')';
            $params = $params + $itemlist;
        } else {
            $sql .= ' AND gi.categoryid = :categoryid';
        }
        $currentgrades = $DB->get_recordset_sql($sql, $params);

        // We will store here the grade_item ids that need to be updated on db.
        $toupdate = array();

        if ($currentgrades->valid()) {

            // Iterate through the user grades to see if we really need to update any of them.
            foreach ($currentgrades as $currentgrade) {

                // Unset $usedweights that we do not need to update.
                if (!empty($usedweights) && isset($usedweights[$currentgrade->id]) && $currentgrade->aggregationstatus === 'used') {
                    // We discard the ones that already have the contribution specified in $usedweights and are marked as 'used'.
                    if (grade_floats_equal($currentgrade->aggregationweight, $usedweights[$currentgrade->id])) {
                        unset($usedweights[$currentgrade->id]);
                    }
                    // Used weights can be present in multiple set_usedinaggregation arguments.
                    if (!isset($novalue[$currentgrade->id]) && !isset($dropped[$currentgrade->id]) &&
                            !isset($extracredit[$currentgrade->id])) {
                        continue;
                    }
                }

                // No value grades.
                if (!empty($novalue) && isset($novalue[$currentgrade->id])) {
                    if ($currentgrade->aggregationstatus !== 'novalue' ||
                            grade_floats_different($currentgrade->aggregationweight, 0)) {
                        $toupdate['novalue'][] = $currentgrade->id;
                    }
                    continue;
                }

                // Dropped grades.
                if (!empty($dropped) && isset($dropped[$currentgrade->id])) {
                    if ($currentgrade->aggregationstatus !== 'dropped' ||
                            grade_floats_different($currentgrade->aggregationweight, 0)) {
                        $toupdate['dropped'][] = $currentgrade->id;
                    }
                    continue;
                }

                // Extra credit grades.
                if (!empty($extracredit) && isset($extracredit[$currentgrade->id])) {

                    // If this grade item is already marked as 'extra' and it already has the provided $usedweights value would be
                    // silly to update to 'used' to later update to 'extra'.
                    if (!empty($usedweights) && isset($usedweights[$currentgrade->id]) &&
                            grade_floats_equal($currentgrade->aggregationweight, $usedweights[$currentgrade->id])) {
                        unset($usedweights[$currentgrade->id]);
                    }

                    // Update the item to extra if it is not already marked as extra in the database or if the item's
                    // aggregationweight will be updated when going through $usedweights items.
                    if ($currentgrade->aggregationstatus !== 'extra' ||
                            (!empty($usedweights) && isset($usedweights[$currentgrade->id]))) {
                        $toupdate['extracredit'][] = $currentgrade->id;
                    }
                    continue;
                }

                // If is not in any of the above groups it should be set to 'unknown', checking that the item is not already
                // unknown, if it is we don't need to update it.
                if ($currentgrade->aggregationstatus !== 'unknown' || grade_floats_different($currentgrade->aggregationweight, 0)) {
                    $toupdate['unknown'][] = $currentgrade->id;
                }
            }
            $currentgrades->close();
        }

        // Update items to 'unknown' status.
        if (!empty($toupdate['unknown'])) {
            list($itemsql, $itemlist) = $DB->get_in_or_equal($toupdate['unknown'], SQL_PARAMS_NAMED, 'g');

            $itemlist['userid'] = $userid;

            $sql = "UPDATE {grade_grades}
                       SET aggregationstatus = 'unknown',
                           aggregationweight = 0
                     WHERE itemid $itemsql AND userid = :userid";
            $DB->execute($sql, $itemlist);
        }

        // Update items to 'used' status and setting the proper weight.
        if (!empty($usedweights)) {
            // The usedweights items are updated individually to record the weights.
            foreach ($usedweights as $gradeitemid => $contribution) {
                $sql = "UPDATE {grade_grades}
                           SET aggregationstatus = 'used',
                               aggregationweight = :contribution
                         WHERE itemid = :itemid AND userid = :userid";

                $params = array('contribution' => $contribution, 'itemid' => $gradeitemid, 'userid' => $userid);
                $DB->execute($sql, $params);
            }
        }

        // Update items to 'novalue' status.
        if (!empty($toupdate['novalue'])) {
            list($itemsql, $itemlist) = $DB->get_in_or_equal($toupdate['novalue'], SQL_PARAMS_NAMED, 'g');

            $itemlist['userid'] = $userid;

            $sql = "UPDATE {grade_grades}
                       SET aggregationstatus = 'novalue',
                           aggregationweight = 0
                     WHERE itemid $itemsql AND userid = :userid";

            $DB->execute($sql, $itemlist);
        }

        // Update items to 'dropped' status.
        if (!empty($toupdate['dropped'])) {
            list($itemsql, $itemlist) = $DB->get_in_or_equal($toupdate['dropped'], SQL_PARAMS_NAMED, 'g');

            $itemlist['userid'] = $userid;

            $sql = "UPDATE {grade_grades}
                       SET aggregationstatus = 'dropped',
                           aggregationweight = 0
                     WHERE itemid $itemsql AND userid = :userid";

            $DB->execute($sql, $itemlist);
        }

        // Update items to 'extracredit' status.
        if (!empty($toupdate['extracredit'])) {
            list($itemsql, $itemlist) = $DB->get_in_or_equal($toupdate['extracredit'], SQL_PARAMS_NAMED, 'g');

            $itemlist['userid'] = $userid;

            $DB->set_field_select('grade_grades',
                                  'aggregationstatus',
                                  'extra',
                                  "itemid $itemsql AND userid = :userid",
                                  $itemlist);
        }
    }

    /**
     * Internal function that calculates the aggregated grade and new min/max for this grade category
     *
     * Must be public as it is used by grade_grade::get_hiding_affected()
     *
     * @param array $grade_values An array of values to be aggregated
     * @param array $items The array of grade_items
     * @since Moodle 2.6.5, 2.7.2
     * @param array & $weights If provided, will be filled with the normalized weights
     *                         for each grade_item as used in the aggregation.
     *                         Some rules for the weights are:
     *                         1. The weights must add up to 1 (unless there are extra credit)
     *                         2. The contributed points column must add up to the course
     *                         final grade and this column is calculated from these weights.
     * @param array  $grademinoverrides User specific grademin values if different to the grade_item grademin (key is itemid)
     * @param array  $grademaxoverrides User specific grademax values if different to the grade_item grademax (key is itemid)
     * @return array containing values for:
     *                'grade' => the new calculated grade
     *                'grademin' => the new calculated min grade for the category
     *                'grademax' => the new calculated max grade for the category
     */
    public function aggregate_values_and_adjust_bounds($grade_values,
                                                       $items,
                                                       & $weights = null,
                                                       $grademinoverrides = array(),
                                                       $grademaxoverrides = array()) {
        global $CFG;

        $category_item = $this->load_grade_item();
        $grademin = $category_item->grademin;
        $grademax = $category_item->grademax;

        switch ($this->aggregation) {

            case GRADE_AGGREGATE_MEDIAN: // Middle point value in the set: ignores frequencies
                $num = count($grade_values);
                $grades = array_values($grade_values);

                // The median gets 100% - others get 0.
                if ($weights !== null && $num > 0) {
                    $count = 0;
                    foreach ($grade_values as $itemid=>$grade_value) {
                        if (($num % 2 == 0) && ($count == intval($num/2)-1 || $count == intval($num/2))) {
                            $weights[$itemid] = 0.5;
                        } else if (($num % 2 != 0) && ($count == intval(($num/2)-0.5))) {
                            $weights[$itemid] = 1.0;
                        } else {
                            $weights[$itemid] = 0;
                        }
                        $count++;
                    }
                }
                if ($num % 2 == 0) {
                    $agg_grade = ($grades[intval($num/2)-1] + $grades[intval($num/2)]) / 2;
                } else {
                    $agg_grade = $grades[intval(($num/2)-0.5)];
                }

                break;

            case GRADE_AGGREGATE_MIN:
                $agg_grade = reset($grade_values);
                // Record the weights as used.
                if ($weights !== null) {
                    foreach ($grade_values as $itemid=>$grade_value) {
                        $weights[$itemid] = 0;
                    }
                }
                // Set the first item to 1.
                $itemids = array_keys($grade_values);
                $weights[reset($itemids)] = 1;
                break;

            case GRADE_AGGREGATE_MAX:
                // Record the weights as used.
                if ($weights !== null) {
                    foreach ($grade_values as $itemid=>$grade_value) {
                        $weights[$itemid] = 0;
                    }
                }
                // Set the last item to 1.
                $itemids = array_keys($grade_values);
                $weights[end($itemids)] = 1;
                $agg_grade = end($grade_values);
                break;

            case GRADE_AGGREGATE_MODE:       // the most common value
                // array_count_values only counts INT and STRING, so if grades are floats we must convert them to string
                $converted_grade_values = array();

                foreach ($grade_values as $k => $gv) {

                    if (!is_int($gv) && !is_string($gv)) {
                        $converted_grade_values[$k] = (string) $gv;

                    } else {
                        $converted_grade_values[$k] = $gv;
                    }
                    if ($weights !== null) {
                        $weights[$k] = 0;
                    }
                }

                $freq = array_count_values($converted_grade_values);
                arsort($freq);                      // sort by frequency keeping keys
                $top = reset($freq);               // highest frequency count
                $modes = array_keys($freq, $top);  // search for all modes (have the same highest count)
                rsort($modes, SORT_NUMERIC);       // get highest mode
                $agg_grade = reset($modes);
                // Record the weights as used.
                if ($weights !== null && $top > 0) {
                    foreach ($grade_values as $k => $gv) {
                        if ($gv == $agg_grade) {
                            $weights[$k] = 1.0 / $top;
                        }
                    }
                }
                break;

            case GRADE_AGGREGATE_WEIGHTED_MEAN: // Weighted average of all existing final grades, weight specified in coef
                $weightsum = 0;
                $sum       = 0;

                foreach ($grade_values as $itemid=>$grade_value) {
                    if ($weights !== null) {
                        $weights[$itemid] = $items[$itemid]->aggregationcoef;
                    }
                    if ($items[$itemid]->aggregationcoef <= 0) {
                        continue;
                    }
                    $weightsum += $items[$itemid]->aggregationcoef;
                    $sum       += $items[$itemid]->aggregationcoef * $grade_value;
                }
                if ($weightsum == 0) {
                    $agg_grade = null;

                } else {
                    $agg_grade = $sum / $weightsum;
                    if ($weights !== null) {
                        // Normalise the weights.
                        foreach ($weights as $itemid => $weight) {
                            $weights[$itemid] = $weight / $weightsum;
                        }
                    }

                }
                break;

            case GRADE_AGGREGATE_WEIGHTED_MEAN2:
                // Weighted average of all existing final grades with optional extra credit flag,
                // weight is the range of grade (usually grademax)
                $this->load_grade_item();
                $weightsum = 0;
                $sum       = null;

                foreach ($grade_values as $itemid=>$grade_value) {
                    if ($items[$itemid]->aggregationcoef > 0) {
                        continue;
                    }

                    $weight = $items[$itemid]->grademax - $items[$itemid]->grademin;
                    if ($weight <= 0) {
                        continue;
                    }

                    $weightsum += $weight;
                    $sum += $weight * $grade_value;
                }

                // Handle the extra credit items separately to calculate their weight accurately.
                foreach ($grade_values as $itemid => $grade_value) {
                    if ($items[$itemid]->aggregationcoef <= 0) {
                        continue;
                    }

                    $weight = $items[$itemid]->grademax - $items[$itemid]->grademin;
                    if ($weight <= 0) {
                        $weights[$itemid] = 0;
                        continue;
                    }

                    $oldsum = $sum;
                    $weightedgrade = $weight * $grade_value;
                    $sum += $weightedgrade;

                    if ($weights !== null) {
                        if ($weightsum <= 0) {
                            $weights[$itemid] = 0;
                            continue;
                        }

                        $oldgrade = $oldsum / $weightsum;
                        $grade = $sum / $weightsum;
                        $normoldgrade = grade_grade::standardise_score($oldgrade, 0, 1, $grademin, $grademax);
                        $normgrade = grade_grade::standardise_score($grade, 0, 1, $grademin, $grademax);
                        $boundedoldgrade = $this->grade_item->bounded_grade($normoldgrade);
                        $boundedgrade = $this->grade_item->bounded_grade($normgrade);

                        if ($boundedgrade - $boundedoldgrade <= 0) {
                            // Nothing new was added to the grade.
                            $weights[$itemid] = 0;
                        } else if ($boundedgrade < $normgrade) {
                            // The grade has been bounded, the extra credit item needs to have a different weight.
                            $gradediff = $boundedgrade - $normoldgrade;
                            $gradediffnorm = grade_grade::standardise_score($gradediff, $grademin, $grademax, 0, 1);
                            $weights[$itemid] = $gradediffnorm / $grade_value;
                        } else {
                            // Default weighting.
                            $weights[$itemid] = $weight / $weightsum;
                        }
                    }
                }

                if ($weightsum == 0) {
                    $agg_grade = $sum; // only extra credits

                } else {
                    $agg_grade = $sum / $weightsum;
                }

                // Record the weights as used.
                if ($weights !== null) {
                    foreach ($grade_values as $itemid=>$grade_value) {
                        if ($items[$itemid]->aggregationcoef > 0) {
                            // Ignore extra credit items, the weights have already been computed.
                            continue;
                        }
                        if ($weightsum > 0) {
                            $weight = $items[$itemid]->grademax - $items[$itemid]->grademin;
                            $weights[$itemid] = $weight / $weightsum;
                        } else {
                            $weights[$itemid] = 0;
                        }
                    }
                }
                break;

            case GRADE_AGGREGATE_EXTRACREDIT_MEAN: // special average
                $this->load_grade_item();
                $num = 0;
                $sum = null;

                foreach ($grade_values as $itemid=>$grade_value) {
                    if ($items[$itemid]->aggregationcoef == 0) {
                        $num += 1;
                        $sum += $grade_value;
                        if ($weights !== null) {
                            $weights[$itemid] = 1;
                        }
                    }
                }

                // Treating the extra credit items separately to get a chance to calculate their effective weights.
                foreach ($grade_values as $itemid=>$grade_value) {
                    if ($items[$itemid]->aggregationcoef > 0) {
                        $oldsum = $sum;
                        $sum += $items[$itemid]->aggregationcoef * $grade_value;

                        if ($weights !== null) {
                            if ($num <= 0) {
                                // The category only contains extra credit items, not setting the weight.
                                continue;
                            }

                            $oldgrade = $oldsum / $num;
                            $grade = $sum / $num;
                            $normoldgrade = grade_grade::standardise_score($oldgrade, 0, 1, $grademin, $grademax);
                            $normgrade = grade_grade::standardise_score($grade, 0, 1, $grademin, $grademax);
                            $boundedoldgrade = $this->grade_item->bounded_grade($normoldgrade);
                            $boundedgrade = $this->grade_item->bounded_grade($normgrade);

                            if ($boundedgrade - $boundedoldgrade <= 0) {
                                // Nothing new was added to the grade.
                                $weights[$itemid] = 0;
                            } else if ($boundedgrade < $normgrade) {
                                // The grade has been bounded, the extra credit item needs to have a different weight.
                                $gradediff = $boundedgrade - $normoldgrade;
                                $gradediffnorm = grade_grade::standardise_score($gradediff, $grademin, $grademax, 0, 1);
                                $weights[$itemid] = $gradediffnorm / $grade_value;
                            } else {
                                // Default weighting.
                                $weights[$itemid] = 1.0 / $num;
                            }
                        }
                    }
                }

                if ($weights !== null && $num > 0) {
                    foreach ($grade_values as $itemid=>$grade_value) {
                        if ($items[$itemid]->aggregationcoef > 0) {
                            // Extra credit weights were already calculated.
                            continue;
                        }
                        if ($weights[$itemid]) {
                            $weights[$itemid] = 1.0 / $num;
                        }
                    }
                }

                if ($num == 0) {
                    $agg_grade = $sum; // only extra credits or wrong coefs

                } else {
                    $agg_grade = $sum / $num;
                }

                break;

            case GRADE_AGGREGATE_SUM:    // Add up all the items.
                $this->load_grade_item();
                $num = count($grade_values);
                $sum = 0;

                // This setting indicates if we should use algorithm prior to MDL-49257 fix for calculating extra credit weights.
                // Even though old algorith has bugs in it, we need to preserve existing grades.
                $gradebookcalculationfreeze = 'gradebook_calculations_freeze_' . $this->courseid;
                $oldextracreditcalculation = isset($CFG->$gradebookcalculationfreeze)
                        && ($CFG->$gradebookcalculationfreeze <= 20150619);

                $sumweights = 0;
                $grademin = 0;
                $grademax = 0;
                $extracredititems = array();
                foreach ($grade_values as $itemid => $gradevalue) {
                    // We need to check if the grademax/min was adjusted per user because of excluded items.
                    $usergrademin = $items[$itemid]->grademin;
                    $usergrademax = $items[$itemid]->grademax;
                    if (isset($grademinoverrides[$itemid])) {
                        $usergrademin = $grademinoverrides[$itemid];
                    }
                    if (isset($grademaxoverrides[$itemid])) {
                        $usergrademax = $grademaxoverrides[$itemid];
                    }

                    // Keep track of the extra credit items, we will need them later on.
                    if ($items[$itemid]->aggregationcoef > 0) {
                        $extracredititems[$itemid] = $items[$itemid];
                    }

                    // Ignore extra credit and items with a weight of 0.
                    if (!isset($extracredititems[$itemid]) && $items[$itemid]->aggregationcoef2 > 0) {
                        $grademin += $usergrademin;
                        $grademax += $usergrademax;
                        $sumweights += $items[$itemid]->aggregationcoef2;
                    }
                }
                $userweights = array();
                $totaloverriddenweight = 0;
                $totaloverriddengrademax = 0;
                // We first need to rescale all manually assigned weights down by the
                // percentage of weights missing from the category.
                foreach ($grade_values as $itemid => $gradevalue) {
                    if ($items[$itemid]->weightoverride) {
                        if ($items[$itemid]->aggregationcoef2 <= 0) {
                            // Records the weight of 0 and continue.
                            $userweights[$itemid] = 0;
                            continue;
                        }
                        $userweights[$itemid] = $sumweights ? ($items[$itemid]->aggregationcoef2 / $sumweights) : 0;
                        if (!$oldextracreditcalculation && isset($extracredititems[$itemid])) {
                            // Extra credit items do not affect totals.
                            continue;
                        }
                        $totaloverriddenweight += $userweights[$itemid];
                        $usergrademax = $items[$itemid]->grademax;
                        if (isset($grademaxoverrides[$itemid])) {
                            $usergrademax = $grademaxoverrides[$itemid];
                        }
                        $totaloverriddengrademax += $usergrademax;
                    }
                }
                $nonoverriddenpoints = $grademax - $totaloverriddengrademax;

                // Then we need to recalculate the automatic weights except for extra credit items.
                foreach ($grade_values as $itemid => $gradevalue) {
                    if (!$items[$itemid]->weightoverride && ($oldextracreditcalculation || !isset($extracredititems[$itemid]))) {
                        $usergrademax = $items[$itemid]->grademax;
                        if (isset($grademaxoverrides[$itemid])) {
                            $usergrademax = $grademaxoverrides[$itemid];
                        }
                        if ($nonoverriddenpoints > 0) {
                            $userweights[$itemid] = ($usergrademax/$nonoverriddenpoints) * (1 - $totaloverriddenweight);
                        } else {
                            $userweights[$itemid] = 0;
                            if ($items[$itemid]->aggregationcoef2 > 0) {
                                // Items with a weight of 0 should not count for the grade max,
                                // though this only applies if the weight was changed to 0.
                                $grademax -= $usergrademax;
                            }
                        }
                    }
                }

                // Now when we finally know the grademax we can adjust the automatic weights of extra credit items.
                if (!$oldextracreditcalculation) {
                    foreach ($grade_values as $itemid => $gradevalue) {
                        if (!$items[$itemid]->weightoverride && isset($extracredititems[$itemid])) {
                            $usergrademax = $items[$itemid]->grademax;
                            if (isset($grademaxoverrides[$itemid])) {
                                $usergrademax = $grademaxoverrides[$itemid];
                            }
                            $userweights[$itemid] = $grademax ? ($usergrademax / $grademax) : 0;
                        }
                    }
                }

                // We can use our freshly corrected weights below.
                foreach ($grade_values as $itemid => $gradevalue) {
                    if (isset($extracredititems[$itemid])) {
                        // We skip the extra credit items first.
                        continue;
                    }
                    $sum += $gradevalue * $userweights[$itemid] * $grademax;
                    if ($weights !== null) {
                        $weights[$itemid] = $userweights[$itemid];
                    }
                }

                // No we proceed with the extra credit items. They might have a different final
                // weight in case the final grade was bounded. So we need to treat them different.
                // Also, as we need to use the bounded_grade() method, we have to inject the
                // right values there, and restore them afterwards.
                $oldgrademax = $this->grade_item->grademax;
                $oldgrademin = $this->grade_item->grademin;
                foreach ($grade_values as $itemid => $gradevalue) {
                    if (!isset($extracredititems[$itemid])) {
                        continue;
                    }
                    $oldsum = $sum;
                    $weightedgrade = $gradevalue * $userweights[$itemid] * $grademax;
                    $sum += $weightedgrade;

                    // Only go through this when we need to record the weights.
                    if ($weights !== null) {
                        if ($grademax <= 0) {
                            // There are only extra credit items in this category,
                            // all the weights should be accurate (and be 0).
                            $weights[$itemid] = $userweights[$itemid];
                            continue;
                        }

                        $oldfinalgrade = $this->grade_item->bounded_grade($oldsum);
                        $newfinalgrade = $this->grade_item->bounded_grade($sum);
                        $finalgradediff = $newfinalgrade - $oldfinalgrade;
                        if ($finalgradediff <= 0) {
                            // This item did not contribute to the category total at all.
                            $weights[$itemid] = 0;
                        } else if ($finalgradediff < $weightedgrade) {
                            // The weight needs to be adjusted because only a portion of the
                            // extra credit item contributed to the category total.
                            $weights[$itemid] = $finalgradediff / ($gradevalue * $grademax);
                        } else {
                            // The weight was accurate.
                            $weights[$itemid] = $userweights[$itemid];
                        }
                    }
                }
                $this->grade_item->grademax = $oldgrademax;
                $this->grade_item->grademin = $oldgrademin;

                if ($grademax > 0) {
                    $agg_grade = $sum / $grademax; // Re-normalize score.
                } else {
                    // Every item in the category is extra credit.
                    $agg_grade = $sum;
                    $grademax = $sum;
                }

                break;

            case GRADE_AGGREGATE_MEAN:    // Arithmetic average of all grade items (if ungraded aggregated, NULL counted as minimum)
            default:
                $num = count($grade_values);
                $sum = array_sum($grade_values);
                $agg_grade = $sum / $num;
                // Record the weights evenly.
                if ($weights !== null && $num > 0) {
                    foreach ($grade_values as $itemid=>$grade_value) {
                        $weights[$itemid] = 1.0 / $num;
                    }
                }
                break;
        }

        return array('grade' => $agg_grade, 'grademin' => $grademin, 'grademax' => $grademax);
    }

    /**
     * Internal function that calculates the aggregated grade for this grade category
     *
     * Must be public as it is used by grade_grade::get_hiding_affected()
     *
     * @deprecated since Moodle 2.8
     * @param array $grade_values An array of values to be aggregated
     * @param array $items The array of grade_items
     * @return float The aggregate grade for this grade category
     */
    public function aggregate_values($grade_values, $items) {
        debugging('grade_category::aggregate_values() is deprecated.
                   Call grade_category::aggregate_values_and_adjust_bounds() instead.', DEBUG_DEVELOPER);
        $result = $this->aggregate_values_and_adjust_bounds($grade_values, $items);
        return $result['grade'];
    }

    /**
     * Some aggregation types may need to update their max grade.
     *
     * This must be executed after updating the weights as it relies on them.
     *
     * @return void
     */
    private function auto_update_max() {
        global $CFG, $DB;
        if ($this->aggregation != GRADE_AGGREGATE_SUM) {
            // not needed at all
            return;
        }

        // Find grade items of immediate children (category or grade items) and force site settings.
        $this->load_grade_item();
        $depends_on = $this->grade_item->depends_on();

        // Check to see if the gradebook is frozen. This allows grades to not be altered at all until a user verifies that they
        // wish to update the grades.
        $gradebookcalculationfreeze = 'gradebook_calculations_freeze_' . $this->courseid;
        $oldextracreditcalculation = isset($CFG->$gradebookcalculationfreeze) && ($CFG->$gradebookcalculationfreeze <= 20150627);
        // Only run if the gradebook isn't frozen.
        if (!$oldextracreditcalculation) {
            // Don't automatically update the max for calculated items.
            if ($this->grade_item->is_calculated()) {
                return;
            }
        }

        $items = false;
        if (!empty($depends_on)) {
            list($usql, $params) = $DB->get_in_or_equal($depends_on);
            $sql = "SELECT *
                      FROM {grade_items}
                     WHERE id $usql";
            $items = $DB->get_records_sql($sql, $params);
        }

        if (!$items) {

            if ($this->grade_item->grademax != 0 or $this->grade_item->gradetype != GRADE_TYPE_VALUE) {
                $this->grade_item->grademax  = 0;
                $this->grade_item->grademin  = 0;
                $this->grade_item->gradetype = GRADE_TYPE_VALUE;
                $this->grade_item->update('aggregation');
            }
            return;
        }

        //find max grade possible
        $maxes = array();

        foreach ($items as $item) {

            if ($item->aggregationcoef > 0) {
                // extra credit from this activity - does not affect total
                continue;
            } else if ($item->aggregationcoef2 <= 0) {
                // Items with a weight of 0 do not affect the total.
                continue;
            }

            if ($item->gradetype == GRADE_TYPE_VALUE) {
                $maxes[$item->id] = $item->grademax;

            } else if ($item->gradetype == GRADE_TYPE_SCALE) {
                $maxes[$item->id] = $item->grademax; // 0 = nograde, 1 = first scale item, 2 = second scale item
            }
        }

        if ($this->can_apply_limit_rules()) {
            // Apply droplow and keephigh.
            $this->apply_limit_rules($maxes, $items);
        }
        $max = array_sum($maxes);

        // update db if anything changed
        if ($this->grade_item->grademax != $max or $this->grade_item->grademin != 0 or $this->grade_item->gradetype != GRADE_TYPE_VALUE) {
            $this->grade_item->grademax  = $max;
            $this->grade_item->grademin  = 0;
            $this->grade_item->gradetype = GRADE_TYPE_VALUE;
            $this->grade_item->update('aggregation');
        }
    }

    /**
     * Recalculate the weights of the grade items in this category.
     *
     * The category total is not updated here, a further call to
     * {@link self::auto_update_max()} is required.
     *
     * @return void
     */
    private function auto_update_weights() {
        global $CFG;
        if ($this->aggregation != GRADE_AGGREGATE_SUM) {
            // This is only required if we are using natural weights.
            return;
        }
        $children = $this->get_children();

        $gradeitem = null;

        // Calculate the sum of the grademax's of all the items within this category.
        $totalnonoverriddengrademax = 0;
        $totalgrademax = 0;

        // Out of 1, how much weight has been manually overriden by a user?
        $totaloverriddenweight  = 0;
        $totaloverriddengrademax  = 0;

        // Has every assessment in this category been overridden?
        $automaticgradeitemspresent = false;
        // Does the grade item require normalising?
        $requiresnormalising = false;

        // This array keeps track of the id and weight of every grade item that has been overridden.
        $overridearray = array();
        foreach ($children as $sortorder => $child) {
            $gradeitem = null;

            if ($child['type'] == 'item') {
                $gradeitem = $child['object'];
            } else if ($child['type'] == 'category') {
                $gradeitem = $child['object']->load_grade_item();
            }

            if ($gradeitem->gradetype == GRADE_TYPE_NONE || $gradeitem->gradetype == GRADE_TYPE_TEXT) {
                // Text items and none items do not have a weight.
                continue;
            } else if (!$this->aggregateoutcomes && $gradeitem->is_outcome_item()) {
                // We will not aggregate outcome items, so we can ignore them.
                continue;
            } else if (empty($CFG->grade_includescalesinaggregation) && $gradeitem->gradetype == GRADE_TYPE_SCALE) {
                // The scales are not included in the aggregation, ignore them.
                continue;
            }

            // Record the ID and the weight for this grade item.
            $overridearray[$gradeitem->id] = array();
            $overridearray[$gradeitem->id]['extracredit'] = intval($gradeitem->aggregationcoef);
            $overridearray[$gradeitem->id]['weight'] = $gradeitem->aggregationcoef2;
            $overridearray[$gradeitem->id]['weightoverride'] = intval($gradeitem->weightoverride);
            // If this item has had its weight overridden then set the flag to true, but
            // only if all previous items were also overridden. Note that extra credit items
            // are counted as overridden grade items.
            if (!$gradeitem->weightoverride && $gradeitem->aggregationcoef == 0) {
                $automaticgradeitemspresent = true;
            }

            if ($gradeitem->aggregationcoef > 0) {
                // An extra credit grade item doesn't contribute to $totaloverriddengrademax.
                continue;
            } else if ($gradeitem->weightoverride > 0 && $gradeitem->aggregationcoef2 <= 0) {
                // An overriden item that defines a weight of 0 does not contribute to $totaloverriddengrademax.
                continue;
            }

            $totalgrademax += $gradeitem->grademax;
            if ($gradeitem->weightoverride > 0) {
                $totaloverriddenweight += $gradeitem->aggregationcoef2;
                $totaloverriddengrademax += $gradeitem->grademax;
            }
        }

        // Initialise this variable (used to keep track of the weight override total).
        $normalisetotal = 0;
        // Keep a record of how much the override total is to see if it is above 100. It it is then we need to set the
        // other weights to zero and normalise the others.
        $overriddentotal = 0;
        // If the overridden weight total is higher than 1 then set the other untouched weights to zero.
        $setotherweightstozero = false;
        // Total up all of the weights.
        foreach ($overridearray as $gradeitemdetail) {
            // If the grade item has extra credit, then don't add it to the normalisetotal.
            if (!$gradeitemdetail['extracredit']) {
                $normalisetotal += $gradeitemdetail['weight'];
            }
            // The overridden total comprises of items that are set as overridden, that aren't extra credit and have a value
            // greater than zero.
            if ($gradeitemdetail['weightoverride'] && !$gradeitemdetail['extracredit'] && $gradeitemdetail['weight'] > 0) {
                // Add overriden weights up to see if they are greater than 1.
                $overriddentotal += $gradeitemdetail['weight'];
            }
        }
        if ($overriddentotal > 1) {
            // Make sure that this catergory of weights gets normalised.
            $requiresnormalising = true;
            // The normalised weights are only the overridden weights, so we just use the total of those.
            $normalisetotal = $overriddentotal;
        }

        $totalnonoverriddengrademax = $totalgrademax - $totaloverriddengrademax;

        // This setting indicates if we should use algorithm prior to MDL-49257 fix for calculating extra credit weights.
        // Even though old algorith has bugs in it, we need to preserve existing grades.
        $gradebookcalculationfreeze = (int)get_config('core', 'gradebook_calculations_freeze_' . $this->courseid);
        $oldextracreditcalculation = $gradebookcalculationfreeze && ($gradebookcalculationfreeze <= 20150619);

        reset($children);
        foreach ($children as $sortorder => $child) {
            $gradeitem = null;

            if ($child['type'] == 'item') {
                $gradeitem = $child['object'];
            } else if ($child['type'] == 'category') {
                $gradeitem = $child['object']->load_grade_item();
            }

            if ($gradeitem->gradetype == GRADE_TYPE_NONE || $gradeitem->gradetype == GRADE_TYPE_TEXT) {
                // Text items and none items do not have a weight, no need to set their weight to
                // zero as they must never be used during aggregation.
                continue;
            } else if (!$this->aggregateoutcomes && $gradeitem->is_outcome_item()) {
                // We will not aggregate outcome items, so we can ignore updating their weights.
                continue;
            } else if (empty($CFG->grade_includescalesinaggregation) && $gradeitem->gradetype == GRADE_TYPE_SCALE) {
                // We will not aggregate the scales, so we can ignore upating their weights.
                continue;
            } else if (!$oldextracreditcalculation && $gradeitem->aggregationcoef > 0 && $gradeitem->weightoverride) {
                // For an item with extra credit ignore other weigths and overrides but do not change anything at all
                // if it's weight was already overridden.
                continue;
            }

            // Store the previous value here, no need to update if it is the same value.
            $prevaggregationcoef2 = $gradeitem->aggregationcoef2;

            if (!$oldextracreditcalculation && $gradeitem->aggregationcoef > 0 && !$gradeitem->weightoverride) {
                // For an item with extra credit ignore other weigths and overrides.
                $gradeitem->aggregationcoef2 = $totalgrademax ? ($gradeitem->grademax / $totalgrademax) : 0;

            } else if (!$gradeitem->weightoverride) {
                // Calculations with a grade maximum of zero will cause problems. Just set the weight to zero.
                if ($totaloverriddenweight >= 1 || $totalnonoverriddengrademax == 0 || $gradeitem->grademax == 0) {
                    // There is no more weight to distribute.
                    $gradeitem->aggregationcoef2 = 0;
                } else {
                    // Calculate this item's weight as a percentage of the non-overridden total grade maxes
                    // then convert it to a proportion of the available non-overriden weight.
                    $gradeitem->aggregationcoef2 = ($gradeitem->grademax/$totalnonoverriddengrademax) *
                            (1 - $totaloverriddenweight);
                }

            } else if ((!$automaticgradeitemspresent && $normalisetotal != 1) || ($requiresnormalising)
                    || $overridearray[$gradeitem->id]['weight'] < 0) {
                // Just divide the overriden weight for this item against the total weight override of all
                // items in this category.
                if ($normalisetotal == 0 || $overridearray[$gradeitem->id]['weight'] < 0) {
                    // If the normalised total equals zero, or the weight value is less than zero,
                    // set the weight for the grade item to zero.
                    $gradeitem->aggregationcoef2 = 0;
                } else {
                    $gradeitem->aggregationcoef2 = $overridearray[$gradeitem->id]['weight'] / $normalisetotal;
                }
            }

            if (grade_floatval($prevaggregationcoef2) !== grade_floatval($gradeitem->aggregationcoef2)) {
                // Update the grade item to reflect these changes.
                $gradeitem->update();
            }
        }
    }

    /**
     * Given an array of grade values (numerical indices) applies droplow or keephigh rules to limit the final array.
     *
     * @param array $grade_values itemid=>$grade_value float
     * @param array $items grade item objects
     * @return array Limited grades.
     */
    public function apply_limit_rules(&$grade_values, $items) {
        $extraused = $this->is_extracredit_used();

        if (!empty($this->droplow)) {
            asort($grade_values, SORT_NUMERIC);
            $dropped = 0;

            // If we have fewer grade items available to drop than $this->droplow, use this flag to escape the loop
            // May occur because of "extra credit" or if droplow is higher than the number of grade items
            $droppedsomething = true;

            while ($dropped < $this->droplow && $droppedsomething) {
                $droppedsomething = false;

                $grade_keys = array_keys($grade_values);
                $gradekeycount = count($grade_keys);

                if ($gradekeycount === 0) {
                    //We've dropped all grade items
                    break;
                }

                $originalindex = $founditemid = $foundmax = null;

                // Find the first remaining grade item that is available to be dropped
                foreach ($grade_keys as $gradekeyindex=>$gradekey) {
                    if (!$extraused || $items[$gradekey]->aggregationcoef <= 0) {
                        // Found a non-extra credit grade item that is eligible to be dropped
                        $originalindex = $gradekeyindex;
                        $founditemid = $grade_keys[$originalindex];
                        $foundmax = $items[$founditemid]->grademax;
                        break;
                    }
                }

                if (empty($founditemid)) {
                    // No grade items available to drop
                    break;
                }

                // Now iterate over the remaining grade items
                // We're looking for other grade items with the same grade value but a higher grademax
                $i = 1;
                while ($originalindex + $i < $gradekeycount) {

                    $possibleitemid = $grade_keys[$originalindex+$i];
                    $i++;

                    if ($grade_values[$founditemid] != $grade_values[$possibleitemid]) {
                        // The next grade item has a different grade value. Stop looking.
                        break;
                    }

                    if ($extraused && $items[$possibleitemid]->aggregationcoef > 0) {
                        // Don't drop extra credit grade items. Continue the search.
                        continue;
                    }

                    if ($foundmax < $items[$possibleitemid]->grademax) {
                        // Found a grade item with the same grade value and a higher grademax
                        $foundmax = $items[$possibleitemid]->grademax;
                        $founditemid = $possibleitemid;
                        // Continue searching to see if there is an even higher grademax
                    }
                }

                // Now drop whatever grade item we have found
                unset($grade_values[$founditemid]);
                $dropped++;
                $droppedsomething = true;
            }

        } else if (!empty($this->keephigh)) {
            arsort($grade_values, SORT_NUMERIC);
            $kept = 0;

            foreach ($grade_values as $itemid=>$value) {

                if ($extraused and $items[$itemid]->aggregationcoef > 0) {
                    // we keep all extra credits

                } else if ($kept < $this->keephigh) {
                    $kept++;

                } else {
                    unset($grade_values[$itemid]);
                }
            }
        }
    }

    /**
     * Returns whether or not we can apply the limit rules.
     *
     * There are cases where drop lowest or keep highest should not be used
     * at all. This method will determine whether or not this logic can be
     * applied considering the current setup of the category.
     *
     * @return bool
     */
    public function can_apply_limit_rules() {
        if ($this->canapplylimitrules !== null) {
            return $this->canapplylimitrules;
        }

        // Set it to be supported by default.
        $this->canapplylimitrules = true;

        // Natural aggregation.
        if ($this->aggregation == GRADE_AGGREGATE_SUM) {
            $canapply = true;

            // Check until one child breaks the rules.
            $gradeitems = $this->get_children();
            $validitems = 0;
            $lastweight = null;
            $lastmaxgrade = null;
            foreach ($gradeitems as $gradeitem) {
                $gi = $gradeitem['object'];

                if ($gradeitem['type'] == 'category') {
                    // Sub categories are not allowed because they can have dynamic weights/maxgrades.
                    $canapply = false;
                    break;
                }

                if ($gi->aggregationcoef > 0) {
                    // Extra credit items are not allowed.
                    $canapply = false;
                    break;
                }

                if ($lastweight !== null && $lastweight != $gi->aggregationcoef2) {
                    // One of the weight differs from another item.
                    $canapply = false;
                    break;
                }

                if ($lastmaxgrade !== null && $lastmaxgrade != $gi->grademax) {
                    // One of the max grade differ from another item. This is not allowed for now
                    // because we could be end up with different max grade between users for this category.
                    $canapply = false;
                    break;
                }

                $lastweight = $gi->aggregationcoef2;
                $lastmaxgrade = $gi->grademax;
            }

            $this->canapplylimitrules = $canapply;
        }

        return $this->canapplylimitrules;
    }

    /**
     * Returns true if category uses extra credit of any kind
     *
     * @return bool True if extra credit used
     */
    public function is_extracredit_used() {
        return self::aggregation_uses_extracredit($this->aggregation);
    }

    /**
     * Returns true if aggregation passed is using extracredit.
     *
     * @param int $aggregation Aggregation const.
     * @return bool True if extra credit used
     */
    public static function aggregation_uses_extracredit($aggregation) {
        return ($aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2
             or $aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN
             or $aggregation == GRADE_AGGREGATE_SUM);
    }

    /**
     * Returns true if category uses special aggregation coefficient
     *
     * @return bool True if an aggregation coefficient is being used
     */
    public function is_aggregationcoef_used() {
        return self::aggregation_uses_aggregationcoef($this->aggregation);

    }

    /**
     * Returns true if aggregation uses aggregationcoef
     *
     * @param int $aggregation Aggregation const.
     * @return bool True if an aggregation coefficient is being used
     */
    public static function aggregation_uses_aggregationcoef($aggregation) {
        return ($aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN
             or $aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2
             or $aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN
             or $aggregation == GRADE_AGGREGATE_SUM);

    }

    /**
     * Recursive function to find which weight/extra credit field to use in the grade item form.
     *
     * @param string $first Whether or not this is the first item in the recursion
     * @return string
     */
    public function get_coefstring($first=true) {
        if (!is_null($this->coefstring)) {
            return $this->coefstring;
        }

        $overriding_coefstring = null;

        // Stop recursing upwards if this category has no parent
        if (!$first) {

            if ($parent_category = $this->load_parent_category()) {
                return $parent_category->get_coefstring(false);

            } else {
                return null;
            }

        } else if ($first) {

            if ($parent_category = $this->load_parent_category()) {
                $overriding_coefstring = $parent_category->get_coefstring(false);
            }
        }

        // If an overriding coefstring has trickled down from one of the parent categories, return it. Otherwise, return self.
        if (!is_null($overriding_coefstring)) {
            return $overriding_coefstring;
        }

        // No parent category is overriding this category's aggregation, return its string
        if ($this->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
            $this->coefstring = 'aggregationcoefweight';

        } else if ($this->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2) {
            $this->coefstring = 'aggregationcoefextrasum';

        } else if ($this->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
            $this->coefstring = 'aggregationcoefextraweight';

        } else if ($this->aggregation == GRADE_AGGREGATE_SUM) {
            $this->coefstring = 'aggregationcoefextraweightsum';

        } else {
            $this->coefstring = 'aggregationcoef';
        }
        return $this->coefstring;
    }

    /**
     * Returns tree with all grade_items and categories as elements
     *
     * @param int $courseid The course ID
     * @param bool $include_category_items as category children
     * @return array
     */
    public static function fetch_course_tree($courseid, $include_category_items=false) {
        $course_category = grade_category::fetch_course_category($courseid);
        $category_array = array('object'=>$course_category, 'type'=>'category', 'depth'=>1,
                                'children'=>$course_category->get_children($include_category_items));

        $course_category->sortorder = $course_category->get_sortorder();
        $sortorder = $course_category->get_sortorder();
        return grade_category::_fetch_course_tree_recursion($category_array, $sortorder);
    }

    /**
     * An internal function that recursively sorts grade categories within a course
     *
     * @param array $category_array The seed of the recursion
     * @param int   $sortorder The current sortorder
     * @return array An array containing 'object', 'type', 'depth' and optionally 'children'
     */
    static private function _fetch_course_tree_recursion($category_array, &$sortorder) {
        if (isset($category_array['object']->gradetype) && $category_array['object']->gradetype==GRADE_TYPE_NONE) {
            return null;
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
            //process the category item first
            $child = null;

            foreach ($category_array['children'] as $oldorder=>$child_array) {

                if ($child_array['type'] == 'courseitem' or $child_array['type'] == 'categoryitem') {
                    $child = grade_category::_fetch_course_tree_recursion($child_array, $sortorder);
                    if (!empty($child)) {
                        $result['children'][$sortorder] = $child;
                    }
                }
            }

            foreach ($category_array['children'] as $oldorder=>$child_array) {

                if ($child_array['type'] != 'courseitem' and $child_array['type'] != 'categoryitem') {
                    $child = grade_category::_fetch_course_tree_recursion($child_array, $sortorder);
                    if (!empty($child)) {
                        $result['children'][++$sortorder] = $child;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Fetches and returns all the children categories and/or grade_items belonging to this category.
     * By default only returns the immediate children (depth=1), but deeper levels can be requested,
     * as well as all levels (0). The elements are indexed by sort order.
     *
     * @param bool $include_category_items Whether or not to include category grade_items in the children array
     * @return array Array of child objects (grade_category and grade_item).
     */
    public function get_children($include_category_items=false) {
        global $DB;

        // This function must be as fast as possible ;-)
        // fetch all course grade items and categories into memory - we do not expect hundreds of these in course
        // we have to limit the number of queries though, because it will be used often in grade reports

        $cats  = $DB->get_records('grade_categories', array('courseid' => $this->courseid));
        $items = $DB->get_records('grade_items', array('courseid' => $this->courseid));

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
                if (empty($categoryid)) {
                    debugging('Found a grade item that isnt in a category');
                }
            }

            // prevent problems with duplicate sortorders in db
            $sortorder = $item->sortorder;

            while (array_key_exists($categoryid, $cats)
                && array_key_exists($sortorder, $cats[$categoryid]->children)) {

                $sortorder++;
            }

            $cats[$categoryid]->children[$sortorder] = $item;

        }

        // now find the requested category and connect categories as children
        $category = false;

        foreach ($cats as $catid=>$cat) {

            if (empty($cat->parent)) {

                if ($cat->path !== '/'.$cat->id.'/') {
                    $grade_category = new grade_category($cat, false);
                    $grade_category->path  = '/'.$cat->id.'/';
                    $grade_category->depth = 1;
                    $grade_category->update('system');
                    return $this->get_children($include_category_items);
                }

            } else {

                if (empty($cat->path) or !preg_match('|/'.$cat->parent.'/'.$cat->id.'/$|', $cat->path)) {
                    //fix paths and depts
                    static $recursioncounter = 0; // prevents infinite recursion
                    $recursioncounter++;

                    if ($recursioncounter < 5) {
                        // fix paths and depths!
                        $grade_category = new grade_category($cat, false);
                        $grade_category->depth = 0;
                        $grade_category->path  = null;
                        $grade_category->update('system');
                        return $this->get_children($include_category_items);
                    }
                }
                // prevent problems with duplicate sortorders in db
                $sortorder = $cat->sortorder;

                while (array_key_exists($sortorder, $cats[$cat->parent]->children)) {
                    //debugging("$sortorder exists in cat loop");
                    $sortorder++;
                }

                $cats[$cat->parent]->children[$sortorder] = &$cats[$catid];
            }

            if ($catid == $this->id) {
                $category = &$cats[$catid];
            }
        }

        unset($items); // not needed
        unset($cats); // not needed

        $children_array = array();
        if (is_object($category)) {
            $children_array = grade_category::_get_children_recursion($category);
            ksort($children_array);
        }

        return $children_array;

    }

    /**
     * Private method used to retrieve all children of this category recursively
     *
     * @param grade_category $category Source of current recursion
     * @return array An array of child grade categories
     */
    private static function _get_children_recursion($category) {

        $children_array = array();
        foreach ($category->children as $sortorder=>$child) {

            if (property_exists($child, 'itemtype')) {
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
     * Uses {@link get_grade_item()} to load or create a grade_item, then saves it as $this->grade_item.
     *
     * @return grade_item
     */
    public function load_grade_item() {
        if (empty($this->grade_item)) {
            $this->grade_item = $this->get_grade_item();
        }
        return $this->grade_item;
    }

    /**
     * Retrieves this grade categories' associated grade_item from the database
     *
     * If no grade_item exists yet, creates one.
     *
     * @return grade_item
     */
    public function get_grade_item() {
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

        } else if (count($grade_items) == 1) {
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
     * Uses $this->parent to instantiate $this->parent_category based on the referenced record in the DB
     *
     * @return grade_category The parent category
     */
    public function load_parent_category() {
        if (empty($this->parent_category) && !empty($this->parent)) {
            $this->parent_category = $this->get_parent_category();
        }
        return $this->parent_category;
    }

    /**
     * Uses $this->parent to instantiate and return a grade_category object
     *
     * @return grade_category Returns the parent category or null if this category has no parent
     */
    public function get_parent_category() {
        if (!empty($this->parent)) {
            $parent_category = new grade_category(array('id' => $this->parent));
            return $parent_category;
        } else {
            return null;
        }
    }

    /**
     * Returns the most descriptive field for this grade category
     *
     * @return string name
     * @param bool $escape Whether the returned category name is to be HTML escaped or not.
     */
    public function get_name($escape = true) {
        global $DB;
        // For a course category, we return the course name if the fullname is set to '?' in the DB (empty in the category edit form)
        if (empty($this->parent) && $this->fullname == '?') {
            $course = $DB->get_record('course', array('id'=> $this->courseid));
            return format_string($course->fullname, false, ['context' => context_course::instance($this->courseid),
                'escape' => $escape]);

        } else {
            // Grade categories can't be set up at system context (unlike scales and outcomes)
            // We therefore must have a courseid, and don't need to handle system contexts when filtering.
            return format_string($this->fullname, false, ['context' => context_course::instance($this->courseid),
                'escape' => $escape]);
        }
    }

    /**
     * Describe the aggregation settings for this category so the reports make more sense.
     *
     * @return string description
     */
    public function get_description() {
        $allhelp = array();
        if ($this->aggregation != GRADE_AGGREGATE_SUM) {
            $aggrstrings = grade_helper::get_aggregation_strings();
            $allhelp[] = $aggrstrings[$this->aggregation];
        }

        if ($this->droplow && $this->can_apply_limit_rules()) {
            $allhelp[] = get_string('droplowestvalues', 'grades', $this->droplow);
        }
        if ($this->keephigh && $this->can_apply_limit_rules()) {
            $allhelp[] = get_string('keephighestvalues', 'grades', $this->keephigh);
        }
        if (!$this->aggregateonlygraded) {
            $allhelp[] = get_string('aggregatenotonlygraded', 'grades');
        }
        if ($allhelp) {
            return implode('. ', $allhelp) . '.';
        }
        return '';
    }

    /**
     * Sets this category's parent id
     *
     * @param int $parentid The ID of the category that is the new parent to $this
     * @param string $source From where was the object updated (mod/forum, manual, etc.)
     * @return bool success
     */
    public function set_parent($parentid, $source=null) {
        if ($this->parent == $parentid) {
            return true;
        }

        if ($parentid == $this->id) {
            throw new \moodle_exception('cannotassignselfasparent');
        }

        if (empty($this->parent) and $this->is_course_category()) {
            throw new \moodle_exception('cannothaveparentcate');
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
        $this->depth           = 0;          // remove old path and depth - will be recalculated in update()
        $this->update($source);

        return $this->update($source);
    }

    /**
     * Returns the final grade values for this grade category.
     *
     * @param int $userid Optional user ID to retrieve a single user's final grade
     * @return mixed An array of all final_grades (stdClass objects) for this grade_item, or a single final_grade.
     */
    public function get_final($userid=null) {
        $this->load_grade_item();
        return $this->grade_item->get_final($userid);
    }

    /**
     * Returns the sortorder of the grade categories' associated grade_item
     *
     * This method is also available in grade_item for cases where the object type is not known.
     *
     * @return int Sort order
     */
    public function get_sortorder() {
        $this->load_grade_item();
        return $this->grade_item->get_sortorder();
    }

    /**
     * Returns the idnumber of the grade categories' associated grade_item.
     *
     * This method is also available in grade_item for cases where the object type is not known.
     *
     * @return string idnumber
     */
    public function get_idnumber() {
        $this->load_grade_item();
        return $this->grade_item->get_idnumber();
    }

    /**
     * Sets the sortorder variable for this category.
     *
     * This method is also available in grade_item, for cases where the object type is not know.
     *
     * @param int $sortorder The sortorder to assign to this category
     */
    public function set_sortorder($sortorder) {
        $this->load_grade_item();
        $this->grade_item->set_sortorder($sortorder);
    }

    /**
     * Move this category after the given sortorder
     *
     * Does not change the parent
     *
     * @param int $sortorder to place after.
     * @return void
     */
    public function move_after_sortorder($sortorder) {
        $this->load_grade_item();
        $this->grade_item->move_after_sortorder($sortorder);
    }

    /**
     * Return true if this is the top most category that represents the total course grade.
     *
     * @return bool
     */
    public function is_course_category() {
        $this->load_grade_item();
        return $this->grade_item->is_course_item();
    }

    /**
     * Return the course level grade_category object
     *
     * @param int $courseid The Course ID
     * @return grade_category Returns the course level grade_category instance
     */
    public static function fetch_course_category($courseid) {
        if (empty($courseid)) {
            debugging('Missing course id!');
            return false;
        }

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
     *
     * @return bool
     */
    public function is_editable() {
        return true;
    }

    /**
     * Returns the locked state/date of the grade categories' associated grade_item.
     *
     * This method is also available in grade_item, for cases where the object type is not known.
     *
     * @return bool
     */
    public function is_locked() {
        $this->load_grade_item();
        return $this->grade_item->is_locked();
    }

    /**
     * Sets the grade_item's locked variable and updates the grade_item.
     *
     * Calls set_locked() on the categories' grade_item
     *
     * @param int  $lockedstate 0, 1 or a timestamp int(10) after which date the item will be locked.
     * @param bool $cascade lock/unlock child objects too
     * @param bool $refresh refresh grades when unlocking
     * @return bool success if category locked (not all children mayb be locked though)
     */
    public function set_locked($lockedstate, $cascade=false, $refresh=true) {
        $this->load_grade_item();

        $result = $this->grade_item->set_locked($lockedstate, $cascade, true);

        // Process all children - items and categories.
        if ($children = grade_item::fetch_all(['categoryid' => $this->id])) {
            foreach ($children as $child) {
                $child->set_locked($lockedstate, $cascade, false);

                if (empty($lockedstate) && $refresh) {
                    // Refresh when unlocking.
                    $child->refresh_grades();
                }
            }
        }

        if ($children = static::fetch_all(['parent' => $this->id])) {
            foreach ($children as $child) {
                $child->set_locked($lockedstate, $cascade, true);
            }
        }

        return $result;
    }

    /**
     * Overrides grade_object::set_properties() to add special handling for changes to category aggregation types
     *
     * @param grade_category $instance the object to set the properties on
     * @param array|stdClass $params Either an associative array or an object containing property name, property value pairs
     */
    public static function set_properties(&$instance, $params) {
        global $DB;

        $fromaggregation = $instance->aggregation;

        parent::set_properties($instance, $params);

        // The aggregation method is changing and this category has already been saved.
        if (isset($params->aggregation) && !empty($instance->id)) {
            $achildwasdupdated = false;

            // Get all its children.
            $children = $instance->get_children();
            foreach ($children as $child) {
                $item = $child['object'];
                if ($child['type'] == 'category') {
                    $item = $item->load_grade_item();
                }

                // Set the new aggregation fields.
                if ($item->set_aggregation_fields_for_aggregation($fromaggregation, $params->aggregation)) {
                    $item->update();
                    $achildwasdupdated = true;
                }
            }

            // If this is the course category, it is possible that its grade item was set as needsupdate
            // by one of its children. If we keep a reference to that stale object we might cause the
            // needsupdate flag to be lost. It's safer to just reload the grade_item from the database.
            if ($achildwasdupdated && !empty($instance->grade_item) && $instance->is_course_category()) {
                $instance->grade_item = null;
                $instance->load_grade_item();
            }
        }
    }

    /**
     * Sets the grade_item's hidden variable and updates the grade_item.
     *
     * Overrides grade_item::set_hidden() to add cascading of the hidden value to grade items in this grade category
     *
     * @param int $hidden 0 mean always visible, 1 means always hidden and a number > 1 is a timestamp to hide until
     * @param bool $cascade apply to child objects too
     */
    public function set_hidden($hidden, $cascade=false) {
        $this->load_grade_item();
        //this hides the category itself and everything it contains
        parent::set_hidden($hidden, $cascade);

        if ($cascade) {

            // This hides the associated grade item (the course/category total).
            $this->grade_item->set_hidden($hidden, $cascade);

            if ($children = grade_item::fetch_all(array('categoryid'=>$this->id))) {

                foreach ($children as $child) {
                    if ($child->can_control_visibility()) {
                        $child->set_hidden($hidden, $cascade);
                    }
                }
            }

            if ($children = grade_category::fetch_all(array('parent'=>$this->id))) {

                foreach ($children as $child) {
                    $child->set_hidden($hidden, $cascade);
                }
            }
        }

        //if marking category visible make sure parent category is visible MDL-21367
        if( !$hidden ) {
            $category_array = grade_category::fetch_all(array('id'=>$this->parent));
            if ($category_array && array_key_exists($this->parent, $category_array)) {
                $category = $category_array[$this->parent];
                //call set_hidden on the category regardless of whether it is hidden as its parent might be hidden
                $category->set_hidden($hidden, false);
            }
        }
    }

    /**
     * Applies default settings on this category
     *
     * @return bool True if anything changed
     */
    public function apply_default_settings() {
        global $CFG;

        foreach ($this->forceable as $property) {

            if (isset($CFG->{"grade_$property"})) {

                if ($CFG->{"grade_$property"} == -1) {
                    continue; //temporary bc before version bump
                }
                $this->$property = $CFG->{"grade_$property"};
            }
        }
    }

    /**
     * Applies forced settings on this category
     *
     * @return bool True if anything changed
     */
    public function apply_forced_settings() {
        global $CFG;

        $updated = false;

        foreach ($this->forceable as $property) {

            if (isset($CFG->{"grade_$property"}) and isset($CFG->{"grade_{$property}_flag"}) and
                                                    ((int) $CFG->{"grade_{$property}_flag"} & 1)) {

                if ($CFG->{"grade_$property"} == -1) {
                    continue; //temporary bc before version bump
                }
                $this->$property = $CFG->{"grade_$property"};
                $updated = true;
            }
        }

        return $updated;
    }

    /**
     * Notification of change in forced category settings.
     *
     * Causes all course and category grade items to be marked as needing to be updated
     */
    public static function updated_forced_settings() {
        global $CFG, $DB;
        $params = array(1, 'course', 'category');
        $sql = "UPDATE {grade_items} SET needsupdate=? WHERE itemtype=? or itemtype=?";
        $DB->execute($sql, $params);
    }

    /**
     * Determine the default aggregation values for a given aggregation method.
     *
     * @param int $aggregationmethod The aggregation method constant value.
     * @return array Containing the keys 'aggregationcoef', 'aggregationcoef2' and 'weightoverride'.
     */
    public static function get_default_aggregation_coefficient_values($aggregationmethod) {
        $defaultcoefficients = array(
            'aggregationcoef' => 0,
            'aggregationcoef2' => 0,
            'weightoverride' => 0
        );

        switch ($aggregationmethod) {
            case GRADE_AGGREGATE_WEIGHTED_MEAN:
                $defaultcoefficients['aggregationcoef'] = 1;
                break;
            case GRADE_AGGREGATE_SUM:
                $defaultcoefficients['aggregationcoef2'] = 1;
                break;
        }

        return $defaultcoefficients;
    }

    /**
     * Cleans the cache.
     *
     * We invalidate them all so it can be completely reloaded.
     *
     * Being conservative here, if there is a new grade_category we purge them, the important part
     * is that this is not purged when there are no changes in grade_categories.
     *
     * @param bool $deleted
     * @return void
     */
    protected function notify_changed($deleted) {
        self::clean_record_set();
    }

    /**
     * Generates a unique key per query.
     *
     * Not unique between grade_object children. self::retrieve_record_set and self::set_record_set will be in charge of
     * selecting the appropriate cache.
     *
     * @param array $params An array of conditions like $fieldname => $fieldvalue
     * @return string
     */
    protected static function generate_record_set_key($params) {
        return sha1(json_encode($params));
    }

    /**
     * Tries to retrieve a record set from the cache.
     *
     * @param array $params The query params
     * @return grade_object[]|bool An array of grade_objects or false if not found.
     */
    protected static function retrieve_record_set($params) {
        $cache = cache::make('core', 'grade_categories');
        return $cache->get(self::generate_record_set_key($params));
    }

    /**
     * Sets a result to the records cache, even if there were no results.
     *
     * @param string $params The query params
     * @param grade_object[]|bool $records An array of grade_objects or false if there are no records matching the $key filters
     * @return void
     */
    protected static function set_record_set($params, $records) {
        $cache = cache::make('core', 'grade_categories');
        return $cache->set(self::generate_record_set_key($params), $records);
    }

    /**
     * Cleans the cache.
     *
     * Aggressive deletion to be conservative given the gradebook design.
     * The key is based on the requested params, not easy nor worth to purge selectively.
     *
     * @return void
     */
    public static function clean_record_set() {
        cache_helper::purge_by_event('changesingradecategories');
    }
}
