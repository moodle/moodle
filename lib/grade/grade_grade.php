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
 * Definition of a class to represent an individual user's grade
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('grade_object.php');

/**
 * grade_grades is an object mapped to DB table {prefix}grade_grades
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_grade extends grade_object {

    /**
     * The DB table.
     * @var string $table
     */
    public $table = 'grade_grades';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'itemid', 'userid', 'rawgrade', 'rawgrademax', 'rawgrademin',
                                 'rawscaleid', 'usermodified', 'finalgrade', 'hidden', 'locked',
                                 'locktime', 'exported', 'overridden', 'excluded', 'timecreated',
                                 'timemodified', 'aggregationstatus', 'aggregationweight');

    /**
     * Array of optional fields with default values (these should match db defaults)
     * @var array $optional_fields
     */
    public $optional_fields = array('feedback'=>null, 'feedbackformat'=>0, 'information'=>null, 'informationformat'=>0);

    /**
     * The id of the grade_item this grade belongs to.
     * @var int $itemid
     */
    public $itemid;

    /**
     * The grade_item object referenced by $this->itemid.
     * @var grade_item $grade_item
     */
    public $grade_item;

    /**
     * The id of the user this grade belongs to.
     * @var int $userid
     */
    public $userid;

    /**
     * The grade value of this raw grade, if such was provided by the module.
     * @var float $rawgrade
     */
    public $rawgrade;

    /**
     * The maximum allowable grade when this grade was created.
     * @var float $rawgrademax
     */
    public $rawgrademax = 100;

    /**
     * The minimum allowable grade when this grade was created.
     * @var float $rawgrademin
     */
    public $rawgrademin = 0;

    /**
     * id of the scale, if this grade is based on a scale.
     * @var int $rawscaleid
     */
    public $rawscaleid;

    /**
     * The userid of the person who last modified this grade.
     * @var int $usermodified
     */
    public $usermodified;

    /**
     * The final value of this grade.
     * @var float $finalgrade
     */
    public $finalgrade;

    /**
     * 0 if visible, 1 always hidden or date not visible until
     * @var float $hidden
     */
    public $hidden = 0;

    /**
     * 0 not locked, date when the item was locked
     * @var float locked
     */
    public $locked = 0;

    /**
     * 0 no automatic locking, date when to lock the grade automatically
     * @var float $locktime
     */
    public $locktime = 0;

    /**
     * Exported flag
     * @var bool $exported
     */
    public $exported = 0;

    /**
     * Overridden flag
     * @var bool $overridden
     */
    public $overridden = 0;

    /**
     * Grade excluded from aggregation functions
     * @var bool $excluded
     */
    public $excluded = 0;

    /**
     * TODO: HACK: create a new field datesubmitted - the date of submission if any (MDL-31377)
     * @var bool $timecreated
     */
    public $timecreated = null;

    /**
     * TODO: HACK: create a new field dategraded - the date of grading (MDL-31378)
     * @var bool $timemodified
     */
    public $timemodified = null;

    /**
     * Aggregation status flag. Can be one of 'unknown', 'dropped', 'novalue' or 'used'.
     * @var string $aggregationstatus
     */
    public $aggregationstatus = 'unknown';

    /**
     * Aggregation weight is the specific weight used in the aggregation calculation for this grade.
     * @var float $aggregationweight
     */
    public $aggregationweight = null;

    /**
     * Feedback files to copy.
     *
     * Example -
     *
     * [
     *     'contextid' => 1,
     *     'component' => 'mod_xyz',
     *     'filearea' => 'mod_xyz_feedback',
     *     'itemid' => 2
     * ];
     *
     * @var array
     */
    public $feedbackfiles = [];

    /**
     * Returns array of grades for given grade_item+users
     *
     * @param grade_item $grade_item
     * @param array $userids
     * @param bool $include_missing include grades that do not exist yet
     * @return array userid=>grade_grade array
     */
    public static function fetch_users_grades($grade_item, $userids, $include_missing=true) {
        global $DB;

        // hmm, there might be a problem with length of sql query
        // if there are too many users requested - we might run out of memory anyway
        $limit = 2000;
        $count = count($userids);
        if ($count > $limit) {
            $half = (int)($count/2);
            $first  = array_slice($userids, 0, $half);
            $second = array_slice($userids, $half);
            return grade_grade::fetch_users_grades($grade_item, $first, $include_missing) + grade_grade::fetch_users_grades($grade_item, $second, $include_missing);
        }

        list($user_ids_cvs, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'uid0');
        $params['giid'] = $grade_item->id;
        $result = array();
        if ($grade_records = $DB->get_records_select('grade_grades', "itemid=:giid AND userid $user_ids_cvs", $params)) {
            foreach ($grade_records as $record) {
                $result[$record->userid] = new grade_grade($record, false);
            }
        }
        if ($include_missing) {
            foreach ($userids as $userid) {
                if (!array_key_exists($userid, $result)) {
                    $grade_grade = new grade_grade();
                    $grade_grade->userid = $userid;
                    $grade_grade->itemid = $grade_item->id;
                    $result[$userid] = $grade_grade;
                }
            }
        }

        return $result;
    }

    /**
     * Loads the grade_item object referenced by $this->itemid and saves it as $this->grade_item for easy access
     *
     * @return grade_item The grade_item instance referenced by $this->itemid
     */
    public function load_grade_item() {
        if (empty($this->itemid)) {
            debugging('Missing itemid');
            $this->grade_item = null;
            return null;
        }

        if (empty($this->grade_item)) {
            $this->grade_item = grade_item::fetch(array('id'=>$this->itemid));

        } else if ($this->grade_item->id != $this->itemid) {
            debugging('Itemid mismatch');
            $this->grade_item = grade_item::fetch(array('id'=>$this->itemid));
        }

        if (empty($this->grade_item)) {
            debugging("Missing grade item id $this->itemid", DEBUG_DEVELOPER);
        }

        return $this->grade_item;
    }

    /**
     * Is grading object editable?
     *
     * @return bool
     */
    public function is_editable() {
        if ($this->is_locked()) {
            return false;
        }

        $grade_item = $this->load_grade_item();

        if ($grade_item->gradetype == GRADE_TYPE_NONE) {
            return false;
        }

        if ($grade_item->is_course_item() or $grade_item->is_category_item()) {
            return (bool)get_config('moodle', 'grade_overridecat');
        }

        return true;
    }

    /**
     * Check grade lock status. Uses both grade item lock and grade lock.
     * Internally any date in locked field (including future ones) means locked,
     * the date is stored for logging purposes only.
     *
     * @return bool True if locked, false if not
     */
    public function is_locked() {
        $this->load_grade_item();
        if (empty($this->grade_item)) {
            return !empty($this->locked);
        } else {
            return !empty($this->locked) or $this->grade_item->is_locked();
        }
    }

    /**
     * Checks if grade overridden
     *
     * @return bool True if grade is overriden
     */
    public function is_overridden() {
        return !empty($this->overridden);
    }

    /**
     * Returns timestamp of submission related to this grade, null if not submitted.
     *
     * @return int Timestamp
     */
    public function get_datesubmitted() {
        //TODO: HACK - create new fields (MDL-31379)
        return $this->timecreated;
    }

    /**
     * Returns the weight this grade contributed to the aggregated grade
     *
     * @return float|null
     */
    public function get_aggregationweight() {
        return $this->aggregationweight;
    }

    /**
     * Set aggregationweight.
     *
     * @param float $aggregationweight
     * @return void
     */
    public function set_aggregationweight($aggregationweight) {
        $this->aggregationweight = $aggregationweight;
        $this->update();
    }

    /**
     * Returns the info on how this value was used in the aggregated grade
     *
     * @return string One of 'dropped', 'excluded', 'novalue', 'used' or 'extra'
     */
    public function get_aggregationstatus() {
        return $this->aggregationstatus;
    }

    /**
     * Set aggregationstatus flag
     *
     * @param string $aggregationstatus
     * @return void
     */
    public function set_aggregationstatus($aggregationstatus) {
        $this->aggregationstatus = $aggregationstatus;
        $this->update();
    }

    /**
     * Returns the minimum and maximum number of points this grade is graded with respect to.
     *
     * @since  Moodle 2.8.7, 2.9.1
     * @return array A list containing, in order, the minimum and maximum number of points.
     */
    protected function get_grade_min_and_max() {
        global $CFG;
        $this->load_grade_item();

        // When the following setting is turned on we use the grade_grade raw min and max values.
        $minmaxtouse = grade_get_setting($this->grade_item->courseid, 'minmaxtouse', $CFG->grade_minmaxtouse);

        // Check to see if the gradebook is frozen. This allows grades to not be altered at all until a user verifies that they
        // wish to update the grades.
        $gradebookcalculationsfreeze = 'gradebook_calculations_freeze_' . $this->grade_item->courseid;
        // Gradebook is frozen, run through old code.
        if (isset($CFG->$gradebookcalculationsfreeze) && (int)$CFG->$gradebookcalculationsfreeze <= 20150627) {
            // Only aggregate items use separate min grades.
            if ($minmaxtouse == GRADE_MIN_MAX_FROM_GRADE_GRADE || $this->grade_item->is_aggregate_item()) {
                return array($this->rawgrademin, $this->rawgrademax);
            } else {
                return array($this->grade_item->grademin, $this->grade_item->grademax);
            }
        } else {
            // Only aggregate items use separate min grades, unless they are calculated grade items.
            if (($this->grade_item->is_aggregate_item() && !$this->grade_item->is_calculated())
                    || $minmaxtouse == GRADE_MIN_MAX_FROM_GRADE_GRADE) {
                return array($this->rawgrademin, $this->rawgrademax);
            } else {
                return array($this->grade_item->grademin, $this->grade_item->grademax);
            }
        }
    }

    /**
     * Returns the minimum number of points this grade is graded with.
     *
     * @since  Moodle 2.8.7, 2.9.1
     * @return float The minimum number of points
     */
    public function get_grade_min() {
        list($min, $max) = $this->get_grade_min_and_max();

        return $min;
    }

    /**
     * Returns the maximum number of points this grade is graded with respect to.
     *
     * @since  Moodle 2.8.7, 2.9.1
     * @return float The maximum number of points
     */
    public function get_grade_max() {
        list($min, $max) = $this->get_grade_min_and_max();

        return $max;
    }

    /**
     * Returns timestamp when last graded, null if no grade present
     *
     * @return int
     */
    public function get_dategraded() {
        //TODO: HACK - create new fields (MDL-31379)
        if (is_null($this->finalgrade) and is_null($this->feedback)) {
            return null; // no grade == no date
        } else if ($this->overridden) {
            return $this->overridden;
        } else {
            return $this->timemodified;
        }
    }

    /**
     * Set the overridden status of grade
     *
     * @param bool $state requested overridden state
     * @param bool $refresh refresh grades from external activities if needed
     * @return bool true is db state changed
     */
    public function set_overridden($state, $refresh = true) {
        if (empty($this->overridden) and $state) {
            $this->overridden = time();
            $this->update();
            return true;

        } else if (!empty($this->overridden) and !$state) {
            $this->overridden = 0;
            $this->update();

            if ($refresh) {
                //refresh when unlocking
                $this->grade_item->refresh_grades($this->userid);
            }

            return true;
        }
        return false;
    }

    /**
     * Checks if grade excluded from aggregation functions
     *
     * @return bool True if grade is excluded from aggregation
     */
    public function is_excluded() {
        return !empty($this->excluded);
    }

    /**
     * Set the excluded status of grade
     *
     * @param bool $state requested excluded state
     * @return bool True is database state changed
     */
    public function set_excluded($state) {
        if (empty($this->excluded) and $state) {
            $this->excluded = time();
            $this->update();
            return true;

        } else if (!empty($this->excluded) and !$state) {
            $this->excluded = 0;
            $this->update();
            return true;
        }
        return false;
    }

    /**
     * Lock/unlock this grade.
     *
     * @param int $lockedstate 0, 1 or a timestamp int(10) after which date the item will be locked.
     * @param bool $cascade Ignored param
     * @param bool $refresh Refresh grades when unlocking
     * @return bool True if successful, false if can not set new lock state for grade
     */
    public function set_locked($lockedstate, $cascade=false, $refresh=true) {
        $this->load_grade_item();

        if ($lockedstate) {
            if ($this->grade_item->needsupdate) {
                //can not lock grade if final not calculated!
                return false;
            }

            $this->locked = time();
            $this->update();

            return true;

        } else {
            if (!empty($this->locked) and $this->locktime < time()) {
                //we have to reset locktime or else it would lock up again
                $this->locktime = 0;
            }

            // remove the locked flag
            $this->locked = 0;
            $this->update();

            if ($refresh and !$this->is_overridden()) {
                //refresh when unlocking and not overridden
                $this->grade_item->refresh_grades($this->userid);
            }

            return true;
        }
    }

    /**
     * Lock the grade if needed. Make sure this is called only when final grades are valid
     *
     * @param array $items array of all grade item ids
     * @return void
     */
    public static function check_locktime_all($items) {
        global $CFG, $DB;

        $now = time(); // no rounding needed, this is not supposed to be called every 10 seconds
        list($usql, $params) = $DB->get_in_or_equal($items);
        $params[] = $now;
        $rs = $DB->get_recordset_select('grade_grades', "itemid $usql AND locked = 0 AND locktime > 0 AND locktime < ?", $params);
        foreach ($rs as $grade) {
            $grade_grade = new grade_grade($grade, false);
            $grade_grade->locked = time();
            $grade_grade->update('locktime');
        }
        $rs->close();
    }

    /**
     * Set the locktime for this grade.
     *
     * @param int $locktime timestamp for lock to activate
     * @return void
     */
    public function set_locktime($locktime) {
        $this->locktime = $locktime;
        $this->update();
    }

    /**
     * Get the locktime for this grade.
     *
     * @return int $locktime timestamp for lock to activate
     */
    public function get_locktime() {
        $this->load_grade_item();

        $item_locktime = $this->grade_item->get_locktime();

        if (empty($this->locktime) or ($item_locktime and $item_locktime < $this->locktime)) {
            return $item_locktime;

        } else {
            return $this->locktime;
        }
    }

    /**
     * Check grade hidden status. Uses data from both grade item and grade.
     *
     * @return bool true if hidden, false if not
     */
    public function is_hidden() {
        $this->load_grade_item();
        if (empty($this->grade_item)) {
            return $this->hidden == 1 or ($this->hidden != 0 and $this->hidden > time());
        } else {
            return $this->hidden == 1 or ($this->hidden != 0 and $this->hidden > time()) or $this->grade_item->is_hidden();
        }
    }

    /**
     * Check grade hidden status. Uses data from both grade item and grade.
     *
     * @return bool true if hiddenuntil, false if not
     */
    public function is_hiddenuntil() {
        $this->load_grade_item();

        if ($this->hidden == 1 or $this->grade_item->hidden == 1) {
            return false; //always hidden
        }

        if ($this->hidden > 1 or $this->grade_item->hidden > 1) {
            return true;
        }

        return false;
    }

    /**
     * Check grade hidden status. Uses data from both grade item and grade.
     *
     * @return int 0 means visible, 1 hidden always, timestamp hidden until
     */
    public function get_hidden() {
        $this->load_grade_item();

        $item_hidden = $this->grade_item->get_hidden();

        if ($item_hidden == 1) {
            return 1;

        } else if ($item_hidden == 0) {
            return $this->hidden;

        } else {
            if ($this->hidden == 0) {
                return $item_hidden;
            } else if ($this->hidden == 1) {
                return 1;
            } else if ($this->hidden > $item_hidden) {
                return $this->hidden;
            } else {
                return $item_hidden;
            }
        }
    }

    /**
     * Set the hidden status of grade, 0 mean visible, 1 always hidden, number means date to hide until.
     *
     * @param int $hidden new hidden status
     * @param bool $cascade ignored
     */
    public function set_hidden($hidden, $cascade=false) {
       $this->hidden = $hidden;
       $this->update();
    }

    /**
     * Finds and returns a grade_grade instance based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return grade_grade Returns a grade_grade instance or false if none found
     */
    public static function fetch($params) {
        return grade_object::fetch_helper('grade_grades', 'grade_grade', $params);
    }

    /**
     * Finds and returns all grade_grade instances based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_grade instances or false if none found.
     */
    public static function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_grades', 'grade_grade', $params);
    }

    /**
     * Given a float value situated between a source minimum and a source maximum, converts it to the
     * corresponding value situated between a target minimum and a target maximum. Thanks to Darlene
     * for the formula :-)
     *
     * @param float $rawgrade
     * @param float $source_min
     * @param float $source_max
     * @param float $target_min
     * @param float $target_max
     * @return float Converted value
     */
    public static function standardise_score($rawgrade, $source_min, $source_max, $target_min, $target_max) {
        if (is_null($rawgrade)) {
          return null;
        }

        if ($source_max == $source_min or $target_min == $target_max) {
            // prevent division by 0
            return $target_max;
        }

        $factor = ($rawgrade - $source_min) / ($source_max - $source_min);
        $diff = $target_max - $target_min;
        $standardised_value = $factor * $diff + $target_min;
        return $standardised_value;
    }

    /**
     * Given an array like this:
     * $a = array(1=>array(2, 3),
     *            2=>array(4),
     *            3=>array(1),
     *            4=>array())
     * this function fully resolves the dependencies so each value will be an array of
     * the all items this item depends on and their dependencies (and their dependencies...).
     * It should not explode if there are circular dependencies.
     * The dependency depth array will list the number of branches in the tree above each leaf.
     *
     * @param array $dependson Array to flatten
     * @param array $dependencydepth Array of itemids => depth. Initially these should be all set to 1.
     * @return array Flattened array
     */
    protected static function flatten_dependencies_array(&$dependson, &$dependencydepth) {
        // Flatten the nested dependencies - this will handle recursion bombs because it removes duplicates.
        $somethingchanged = true;
        while ($somethingchanged) {
            $somethingchanged = false;

            foreach ($dependson as $itemid => $depends) {
                // Make a copy so we can tell if it changed.
                $before = $dependson[$itemid];
                foreach ($depends as $subitemid => $subdepends) {
                    $dependson[$itemid] = array_unique(array_merge($depends, $dependson[$subdepends]));
                    sort($dependson[$itemid], SORT_NUMERIC);
                }
                if ($before != $dependson[$itemid]) {
                    $somethingchanged = true;
                    if (!isset($dependencydepth[$itemid])) {
                        $dependencydepth[$itemid] = 1;
                    } else {
                        $dependencydepth[$itemid]++;
                    }
                }
            }
        }
    }

    /**
     * Return array of grade item ids that are either hidden or indirectly depend
     * on hidden grades, excluded grades are not returned.
     * THIS IS A REALLY BIG HACK! to be replaced by conditional aggregation of hidden grades in 2.0
     *
     * @param array $grade_grades all course grades of one user, & used for better internal caching
     * @param array $grade_items array of grade items, & used for better internal caching
     * @return array This is an array of following arrays:
     *      unknown => list of item ids that may be affected by hiding (with the ITEM ID as both the key and the value) - for BC with old gradereport plugins
     *      unknowngrades => list of item ids that may be affected by hiding (with the calculated grade as the value)
     *      altered => list of item ids that are definitely affected by hiding (with the calculated grade as the value)
     *      alteredgrademax => for each item in altered or unknown, the new value of the grademax
     *      alteredgrademin => for each item in altered or unknown, the new value of the grademin
     *      alteredgradestatus => for each item with a modified status - the value of the new status
     *      alteredgradeweight => for each item with a modified weight - the value of the new weight
     */
    public static function get_hiding_affected(&$grade_grades, &$grade_items) {
        global $CFG;

        if (count($grade_grades) !== count($grade_items)) {
            print_error('invalidarraysize', 'debug', '', 'grade_grade::get_hiding_affected()!');
        }

        $dependson = array();
        $todo = array();
        $unknown = array();  // can not find altered
        $altered = array();  // altered grades
        $alteredgrademax = array();  // Altered grade max values.
        $alteredgrademin = array();  // Altered grade min values.
        $alteredaggregationstatus = array();  // Altered aggregation status.
        $alteredaggregationweight = array();  // Altered aggregation weight.
        $dependencydepth = array();

        $hiddenfound = false;
        foreach($grade_grades as $itemid=>$unused) {
            $grade_grade =& $grade_grades[$itemid];
            // We need the immediate dependencies of all every grade_item so we can calculate nested dependencies.
            $dependson[$grade_grade->itemid] = $grade_items[$grade_grade->itemid]->depends_on();
            if ($grade_grade->is_excluded()) {
                //nothing to do, aggregation is ok
            } else if ($grade_grade->is_hidden()) {
                $hiddenfound = true;
                $altered[$grade_grade->itemid] = null;
                $alteredaggregationstatus[$grade_grade->itemid] = 'dropped';
                $alteredaggregationweight[$grade_grade->itemid] = 0;
            } else if ($grade_grade->is_locked() or $grade_grade->is_overridden()) {
                // no need to recalculate locked or overridden grades
            } else {
                if (!empty($dependson[$grade_grade->itemid])) {
                    $dependencydepth[$grade_grade->itemid] = 1;
                    $todo[] = $grade_grade->itemid;
                }
            }
        }

        // Flatten the dependency tree and count number of branches to each leaf.
        self::flatten_dependencies_array($dependson, $dependencydepth);

        if (!$hiddenfound) {
            return array('unknown' => array(),
                         'unknowngrades' => array(),
                         'altered' => array(),
                         'alteredgrademax' => array(),
                         'alteredgrademin' => array(),
                         'alteredaggregationstatus' => array(),
                         'alteredaggregationweight' => array());
        }
        // This line ensures that $dependencydepth has the same number of items as $todo.
        $dependencydepth = array_intersect_key($dependencydepth, array_flip($todo));
        // We need to resort the todo list by the dependency depth. This guarantees we process the leaves, then the branches.
        array_multisort($dependencydepth, $todo);

        $max = count($todo);
        $hidden_precursors = null;
        for($i=0; $i<$max; $i++) {
            $found = false;
            foreach($todo as $key=>$do) {
                $hidden_precursors = array_intersect($dependson[$do], array_keys($unknown));
                if ($hidden_precursors) {
                    // this item depends on hidden grade indirectly
                    $unknown[$do] = $grade_grades[$do]->finalgrade;
                    unset($todo[$key]);
                    $found = true;
                    continue;

                } else if (!array_intersect($dependson[$do], $todo)) {
                    $hidden_precursors = array_intersect($dependson[$do], array_keys($altered));
                    // If the dependency is a sum aggregation, we need to process it as if it had hidden items.
                    // The reason for this, is that the code will recalculate the maxgrade by removing ungraded
                    // items and accounting for 'drop x grades' and then stored back in our virtual grade_items.
                    // This recalculation is necessary because there will be a call to:
                    //              $grade_category->aggregate_values_and_adjust_bounds
                    // for the top level grade that will depend on knowing what that caclulated grademax is
                    // and it finds that value by checking the virtual grade_items.
                    $issumaggregate = false;
                    if ($grade_items[$do]->itemtype == 'category') {
                        $issumaggregate = $grade_items[$do]->load_item_category()->aggregation == GRADE_AGGREGATE_SUM;
                    }
                    if (!$hidden_precursors && !$issumaggregate) {
                        unset($todo[$key]);
                        $found = true;
                        continue;

                    } else {
                        // depends on altered grades - we should try to recalculate if possible
                        if ($grade_items[$do]->is_calculated() or
                            (!$grade_items[$do]->is_category_item() and !$grade_items[$do]->is_course_item())
                        ) {
                            // This is a grade item that is not a category or course and has been affected by grade hiding.
                            // I guess this means it is a calculation that needs to be recalculated.
                            $unknown[$do] = $grade_grades[$do]->finalgrade;
                            unset($todo[$key]);
                            $found = true;
                            continue;

                        } else {
                            // This is a grade category (or course).
                            $grade_category = $grade_items[$do]->load_item_category();

                            // Build a new list of the grades in this category.
                            $values = array();
                            $immediatedepends = $grade_items[$do]->depends_on();
                            foreach ($immediatedepends as $itemid) {
                                if (array_key_exists($itemid, $altered)) {
                                    //nulling an altered precursor
                                    $values[$itemid] = $altered[$itemid];
                                    if (is_null($values[$itemid])) {
                                        // This means this was a hidden grade item removed from the result.
                                        unset($values[$itemid]);
                                    }
                                } elseif (empty($values[$itemid])) {
                                    $values[$itemid] = $grade_grades[$itemid]->finalgrade;
                                }
                            }

                            foreach ($values as $itemid=>$value) {
                                if ($grade_grades[$itemid]->is_excluded()) {
                                    unset($values[$itemid]);
                                    $alteredaggregationstatus[$itemid] = 'excluded';
                                    $alteredaggregationweight[$itemid] = null;
                                    continue;
                                }
                                // The grade min/max may have been altered by hiding.
                                $grademin = $grade_items[$itemid]->grademin;
                                if (isset($alteredgrademin[$itemid])) {
                                    $grademin = $alteredgrademin[$itemid];
                                }
                                $grademax = $grade_items[$itemid]->grademax;
                                if (isset($alteredgrademax[$itemid])) {
                                    $grademax = $alteredgrademax[$itemid];
                                }
                                $values[$itemid] = grade_grade::standardise_score($value, $grademin, $grademax, 0, 1);
                            }

                            if ($grade_category->aggregateonlygraded) {
                                foreach ($values as $itemid=>$value) {
                                    if (is_null($value)) {
                                        unset($values[$itemid]);
                                        $alteredaggregationstatus[$itemid] = 'novalue';
                                        $alteredaggregationweight[$itemid] = null;
                                    }
                                }
                            } else {
                                foreach ($values as $itemid=>$value) {
                                    if (is_null($value)) {
                                        $values[$itemid] = 0;
                                    }
                                }
                            }

                            // limit and sort
                            $allvalues = $values;
                            $grade_category->apply_limit_rules($values, $grade_items);

                            $moredropped = array_diff($allvalues, $values);
                            foreach ($moredropped as $drop => $unused) {
                                $alteredaggregationstatus[$drop] = 'dropped';
                                $alteredaggregationweight[$drop] = null;
                            }

                            foreach ($values as $itemid => $val) {
                                if ($grade_category->is_extracredit_used() && ($grade_items[$itemid]->aggregationcoef > 0)) {
                                    $alteredaggregationstatus[$itemid] = 'extra';
                                }
                            }

                            asort($values, SORT_NUMERIC);

                            // let's see we have still enough grades to do any statistics
                            if (count($values) == 0) {
                                // not enough attempts yet
                                $altered[$do] = null;
                                unset($todo[$key]);
                                $found = true;
                                continue;
                            }

                            $usedweights = array();
                            $adjustedgrade = $grade_category->aggregate_values_and_adjust_bounds($values, $grade_items, $usedweights);

                            // recalculate the rawgrade back to requested range
                            $finalgrade = grade_grade::standardise_score($adjustedgrade['grade'],
                                                                         0,
                                                                         1,
                                                                         $adjustedgrade['grademin'],
                                                                         $adjustedgrade['grademax']);

                            foreach ($usedweights as $itemid => $weight) {
                                if (!isset($alteredaggregationstatus[$itemid])) {
                                    $alteredaggregationstatus[$itemid] = 'used';
                                }
                                $alteredaggregationweight[$itemid] = $weight;
                            }

                            $finalgrade = $grade_items[$do]->bounded_grade($finalgrade);
                            $alteredgrademin[$do] = $adjustedgrade['grademin'];
                            $alteredgrademax[$do] = $adjustedgrade['grademax'];
                            // We need to muck with the "in-memory" grade_items records so
                            // that subsequent calculations will use the adjusted grademin and grademax.
                            $grade_items[$do]->grademin = $adjustedgrade['grademin'];
                            $grade_items[$do]->grademax = $adjustedgrade['grademax'];

                            $altered[$do] = $finalgrade;
                            unset($todo[$key]);
                            $found = true;
                            continue;
                        }
                    }
                }
            }
            if (!$found) {
                break;
            }
        }

        return array('unknown' => array_combine(array_keys($unknown), array_keys($unknown)), // Left for BC in case some gradereport plugins expect it.
                     'unknowngrades' => $unknown,
                     'altered' => $altered,
                     'alteredgrademax' => $alteredgrademax,
                     'alteredgrademin' => $alteredgrademin,
                     'alteredaggregationstatus' => $alteredaggregationstatus,
                     'alteredaggregationweight' => $alteredaggregationweight);
    }

    /**
     * Returns true if the grade's value is superior or equal to the grade item's gradepass value, false otherwise.
     *
     * @param grade_item $grade_item An optional grade_item of which gradepass value we can use, saves having to load the grade_grade's grade_item
     * @return bool
     */
    public function is_passed($grade_item = null) {
        if (empty($grade_item)) {
            if (!isset($this->grade_item)) {
                $this->load_grade_item();
            }
        } else {
            $this->grade_item = $grade_item;
            $this->itemid = $grade_item->id;
        }

        // Return null if finalgrade is null
        if (is_null($this->finalgrade)) {
            return null;
        }

        // Return null if gradepass == grademin, gradepass is null, or grade item is a scale and gradepass is 0.
        if (is_null($this->grade_item->gradepass)) {
            return null;
        } else if ($this->grade_item->gradepass == $this->grade_item->grademin) {
            return null;
        } else if ($this->grade_item->gradetype == GRADE_TYPE_SCALE && !grade_floats_different($this->grade_item->gradepass, 0.0)) {
            return null;
        }

        return $this->finalgrade >= $this->grade_item->gradepass;
    }

    /**
     * Insert the grade_grade instance into the database.
     *
     * @param string $source From where was the object inserted (mod/forum, manual, etc.)
     * @return int The new grade_grade ID if successful, false otherwise
     */
    public function insert($source=null) {
        // TODO: dategraded hack - do not update times, they are used for submission and grading (MDL-31379)
        //$this->timecreated = $this->timemodified = time();
        return parent::insert($source);
    }

    /**
     * In addition to update() as defined in grade_object rounds the float numbers using php function,
     * the reason is we need to compare the db value with computed number to skip updates if possible.
     *
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return bool success
     */
    public function update($source=null) {
        $this->rawgrade = grade_floatval($this->rawgrade);
        $this->finalgrade = grade_floatval($this->finalgrade);
        $this->rawgrademin = grade_floatval($this->rawgrademin);
        $this->rawgrademax = grade_floatval($this->rawgrademax);
        return parent::update($source);
    }


    /**
     * Handles adding feedback files in the gradebook.
     *
     * @param int|null $historyid
     */
    protected function add_feedback_files(int $historyid = null) {
        global $CFG;

        // We only support feedback files for modules atm.
        if ($this->grade_item && $this->grade_item->is_external_item()) {
            $context = $this->get_context();
            $this->copy_feedback_files($context, GRADE_FEEDBACK_FILEAREA, $this->id);

            if (empty($CFG->disablegradehistory) && $historyid) {
                $this->copy_feedback_files($context, GRADE_HISTORY_FEEDBACK_FILEAREA, $historyid);
            }
        }

        return $this->id;
    }

    /**
     * Handles updating feedback files in the gradebook.
     *
     * @param int|null $historyid
     */
    protected function update_feedback_files(int $historyid = null) {
        global $CFG;

        // We only support feedback files for modules atm.
        if ($this->grade_item && $this->grade_item->is_external_item()) {
            $context = $this->get_context();

            $fs = new file_storage();
            $fs->delete_area_files($context->id, GRADE_FILE_COMPONENT, GRADE_FEEDBACK_FILEAREA, $this->id);

            $this->copy_feedback_files($context, GRADE_FEEDBACK_FILEAREA, $this->id);

            if (empty($CFG->disablegradehistory) && $historyid) {
                $this->copy_feedback_files($context, GRADE_HISTORY_FEEDBACK_FILEAREA, $historyid);
            }
        }

        return true;
    }

    /**
     * Handles deleting feedback files in the gradebook.
     */
    protected function delete_feedback_files() {
        // We only support feedback files for modules atm.
        if ($this->grade_item && $this->grade_item->is_external_item()) {
            $context = $this->get_context();

            $fs = new file_storage();
            $fs->delete_area_files($context->id, GRADE_FILE_COMPONENT, GRADE_FEEDBACK_FILEAREA, $this->id);

            // Grade history only gets deleted when we delete the whole grade item.
        }

        return true;
    }

    /**
     * Deletes the grade_grade instance from the database.
     *
     * @param string $source The location the deletion occurred (mod/forum, manual, etc.).
     * @return bool Returns true if the deletion was successful, false otherwise.
     */
    public function delete($source = null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $success = parent::delete($source);

        // If the grade was deleted successfully trigger a grade_deleted event.
        if ($success && !empty($this->grade_item)) {
            \core\event\grade_deleted::create_from_grade($this)->trigger();
        }

        $transaction->allow_commit();
        return $success;
    }

    /**
     * Used to notify the completion system (if necessary) that a user's grade
     * has changed, and clear up a possible score cache.
     *
     * @param bool $deleted True if grade was actually deleted
     */
    protected function notify_changed($deleted) {
        global $CFG;

        // Condition code may cache the grades for conditional availability of
        // modules or sections. (This code should use a hook for communication
        // with plugin, but hooks are not implemented at time of writing.)
        if (!empty($CFG->enableavailability) && class_exists('\availability_grade\callbacks')) {
            \availability_grade\callbacks::grade_changed($this->userid);
        }

        require_once($CFG->libdir.'/completionlib.php');

        // Bail out immediately if completion is not enabled for site (saves loading
        // grade item & requiring the restore stuff).
        if (!completion_info::is_enabled_for_site()) {
            return;
        }

        // Ignore during restore, as completion data will be updated anyway and
        // doing it now will result in incorrect dates (it will say they got the
        // grade completion now, instead of the correct time).
        if (class_exists('restore_controller', false) && restore_controller::is_executing()) {
            return;
        }

        // Load information about grade item, exit if the grade item is missing.
        if (!$this->load_grade_item()) {
            return;
        }

        // Only course-modules have completion data
        if ($this->grade_item->itemtype!='mod') {
            return;
        }

        // Use $COURSE if available otherwise get it via item fields
        $course = get_course($this->grade_item->courseid, false);

        // Bail out if completion is not enabled for course
        $completion = new completion_info($course);
        if (!$completion->is_enabled()) {
            return;
        }

        // Get course-module
        $cm = get_coursemodule_from_instance($this->grade_item->itemmodule,
              $this->grade_item->iteminstance, $this->grade_item->courseid);
        // If the course-module doesn't exist, display a warning...
        if (!$cm) {
            // ...unless the grade is being deleted in which case it's likely
            // that the course-module was just deleted too, so that's okay.
            if (!$deleted) {
                debugging("Couldn't find course-module for module '" .
                        $this->grade_item->itemmodule . "', instance '" .
                        $this->grade_item->iteminstance . "', course '" .
                        $this->grade_item->courseid . "'");
            }
            return;
        }

        // Pass information on to completion system
        $completion->inform_grade_changed($cm, $this->grade_item, $this, $deleted);
    }

    /**
     * Get some useful information about how this grade_grade is reflected in the aggregation
     * for the grade_category. For example this could be an extra credit item, and it could be
     * dropped because it's in the X lowest or highest.
     *
     * @return array(status, weight) - A keyword and a numerical weight that represents how this grade was included in the aggregation.
     */
    function get_aggregation_hint() {
        return array('status' => $this->get_aggregationstatus(),
                     'weight' => $this->get_aggregationweight());
    }

    /**
     * Handles copying feedback files to a specified gradebook file area.
     *
     * @param context $context
     * @param string $filearea
     * @param int $itemid
     */
    private function copy_feedback_files(context $context, string $filearea, int $itemid) {
        if ($this->feedbackfiles) {
            $filestocopycontextid = $this->feedbackfiles['contextid'];
            $filestocopycomponent = $this->feedbackfiles['component'];
            $filestocopyfilearea = $this->feedbackfiles['filearea'];
            $filestocopyitemid = $this->feedbackfiles['itemid'];

            $fs = new file_storage();
            if ($filestocopy = $fs->get_area_files($filestocopycontextid, $filestocopycomponent, $filestocopyfilearea,
                    $filestocopyitemid)) {
                foreach ($filestocopy as $filetocopy) {
                    $destination = [
                        'contextid' => $context->id,
                        'component' => GRADE_FILE_COMPONENT,
                        'filearea' => $filearea,
                        'itemid' => $itemid
                    ];
                    $fs->create_file_from_storedfile($destination, $filetocopy);
                }
            }
        }
    }

    /**
     * Determine the correct context for this grade_grade.
     *
     * @return context
     */
    public function get_context() {
        $this->load_grade_item();
        return $this->grade_item->get_context();
    }
}
