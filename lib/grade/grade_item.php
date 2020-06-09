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
 * Definition of a class to represent a grade item
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('grade_object.php');

/**
 * Class representing a grade item.
 *
 * It is responsible for handling its DB representation, modifying and returning its metadata.
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2006 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_item extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    public $table = 'grade_items';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'courseid', 'categoryid', 'itemname', 'itemtype', 'itemmodule', 'iteminstance',
                                 'itemnumber', 'iteminfo', 'idnumber', 'calculation', 'gradetype', 'grademax', 'grademin',
                                 'scaleid', 'outcomeid', 'gradepass', 'multfactor', 'plusfactor', 'aggregationcoef',
                                 'aggregationcoef2', 'sortorder', 'display', 'decimals', 'hidden', 'locked', 'locktime',
                                 'needsupdate', 'weightoverride', 'timecreated', 'timemodified');

    /**
     * The course this grade_item belongs to.
     * @var int $courseid
     */
    public $courseid;

    /**
     * The category this grade_item belongs to (optional).
     * @var int $categoryid
     */
    public $categoryid;

    /**
     * The grade_category object referenced $this->iteminstance if itemtype == 'category' or == 'course'.
     * @var grade_category $item_category
     */
    public $item_category;

    /**
     * The grade_category object referenced by $this->categoryid.
     * @var grade_category $parent_category
     */
    public $parent_category;


    /**
     * The name of this grade_item (pushed by the module).
     * @var string $itemname
     */
    public $itemname;

    /**
     * e.g. 'category', 'course' and 'mod', 'blocks', 'import', etc...
     * @var string $itemtype
     */
    public $itemtype;

    /**
     * The module pushing this grade (e.g. 'forum', 'quiz', 'assignment' etc).
     * @var string $itemmodule
     */
    public $itemmodule;

    /**
     * ID of the item module
     * @var int $iteminstance
     */
    public $iteminstance;

    /**
     * Number of the item in a series of multiple grades pushed by an activity.
     * @var int $itemnumber
     */
    public $itemnumber;

    /**
     * Info and notes about this item.
     * @var string $iteminfo
     */
    public $iteminfo;

    /**
     * Arbitrary idnumber provided by the module responsible.
     * @var string $idnumber
     */
    public $idnumber;

    /**
     * Calculation string used for this item.
     * @var string $calculation
     */
    public $calculation;

    /**
     * Indicates if we already tried to normalize the grade calculation formula.
     * This flag helps to minimize db access when broken formulas used in calculation.
     * @var bool
     */
    public $calculation_normalized;
    /**
     * Math evaluation object
     * @var calc_formula A formula object
     */
    public $formula;

    /**
     * The type of grade (0 = none, 1 = value, 2 = scale, 3 = text)
     * @var int $gradetype
     */
    public $gradetype = GRADE_TYPE_VALUE;

    /**
     * Maximum allowable grade.
     * @var float $grademax
     */
    public $grademax = 100;

    /**
     * Minimum allowable grade.
     * @var float $grademin
     */
    public $grademin = 0;

    /**
     * id of the scale, if this grade is based on a scale.
     * @var int $scaleid
     */
    public $scaleid;

    /**
     * The grade_scale object referenced by $this->scaleid.
     * @var grade_scale $scale
     */
    public $scale;

    /**
     * The id of the optional grade_outcome associated with this grade_item.
     * @var int $outcomeid
     */
    public $outcomeid;

    /**
     * The grade_outcome this grade is associated with, if applicable.
     * @var grade_outcome $outcome
     */
    public $outcome;

    /**
     * grade required to pass. (grademin <= gradepass <= grademax)
     * @var float $gradepass
     */
    public $gradepass = 0;

    /**
     * Multiply all grades by this number.
     * @var float $multfactor
     */
    public $multfactor = 1.0;

    /**
     * Add this to all grades.
     * @var float $plusfactor
     */
    public $plusfactor = 0;

    /**
     * Aggregation coeficient used for weighted averages or extra credit
     * @var float $aggregationcoef
     */
    public $aggregationcoef = 0;

    /**
     * Aggregation coeficient used for weighted averages only
     * @var float $aggregationcoef2
     */
    public $aggregationcoef2 = 0;

    /**
     * Sorting order of the columns.
     * @var int $sortorder
     */
    public $sortorder = 0;

    /**
     * Display type of the grades (Real, Percentage, Letter, or default).
     * @var int $display
     */
    public $display = GRADE_DISPLAY_TYPE_DEFAULT;

    /**
     * The number of digits after the decimal point symbol. Applies only to REAL and PERCENTAGE grade display types.
     * @var int $decimals
     */
    public $decimals = null;

    /**
     * Grade item lock flag. Empty if not locked, locked if any value present, usually date when item was locked. Locking prevents updating.
     * @var int $locked
     */
    public $locked = 0;

    /**
     * Date after which the grade will be locked. Empty means no automatic locking.
     * @var int $locktime
     */
    public $locktime = 0;

    /**
     * If set, the whole column will be recalculated, then this flag will be switched off.
     * @var bool $needsupdate
     */
    public $needsupdate = 1;

    /**
     * If set, the grade item's weight has been overridden by a user and should not be automatically adjusted.
     */
    public $weightoverride = 0;

    /**
     * Cached dependson array
     * @var array An array of cached grade item dependencies.
     */
    public $dependson_cache = null;

    /**
     * Constructor. Optionally (and by default) attempts to fetch corresponding row from the database
     *
     * @param array $params An array with required parameters for this grade object.
     * @param bool $fetch Whether to fetch corresponding row from the database or not,
     *        optional fields might not be defined if false used
     */
    public function __construct($params = null, $fetch = true) {
        global $CFG;
        // Set grademax from $CFG->gradepointdefault .
        self::set_properties($this, array('grademax' => $CFG->gradepointdefault));
        parent::__construct($params, $fetch);
    }

    /**
     * In addition to update() as defined in grade_object, handle the grade_outcome and grade_scale objects.
     * Force regrading if necessary, rounds the float numbers using php function,
     * the reason is we need to compare the db value with computed number to skip regrading if possible.
     *
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return bool success
     */
    public function update($source=null) {
        // reset caches
        $this->dependson_cache = null;

        // Retrieve scale and infer grademax/min from it if needed
        $this->load_scale();

        // make sure there is not 0 in outcomeid
        if (empty($this->outcomeid)) {
            $this->outcomeid = null;
        }

        if ($this->qualifies_for_regrading()) {
            $this->force_regrading();
        }

        $this->timemodified = time();

        $this->grademin        = grade_floatval($this->grademin);
        $this->grademax        = grade_floatval($this->grademax);
        $this->multfactor      = grade_floatval($this->multfactor);
        $this->plusfactor      = grade_floatval($this->plusfactor);
        $this->aggregationcoef = grade_floatval($this->aggregationcoef);
        $this->aggregationcoef2 = grade_floatval($this->aggregationcoef2);

        $result = parent::update($source);

        if ($result) {
            $event = \core\event\grade_item_updated::create_from_grade_item($this);
            $event->trigger();
        }

        return $result;
    }

    /**
     * Compares the values held by this object with those of the matching record in DB, and returns
     * whether or not these differences are sufficient to justify an update of all parent objects.
     * This assumes that this object has an id number and a matching record in DB. If not, it will return false.
     *
     * @return bool
     */
    public function qualifies_for_regrading() {
        if (empty($this->id)) {
            return false;
        }

        $db_item = new grade_item(array('id' => $this->id));

        $calculationdiff = $db_item->calculation != $this->calculation;
        $categorydiff    = $db_item->categoryid  != $this->categoryid;
        $gradetypediff   = $db_item->gradetype   != $this->gradetype;
        $scaleiddiff     = $db_item->scaleid     != $this->scaleid;
        $outcomeiddiff   = $db_item->outcomeid   != $this->outcomeid;
        $locktimediff    = $db_item->locktime    != $this->locktime;
        $grademindiff    = grade_floats_different($db_item->grademin,        $this->grademin);
        $grademaxdiff    = grade_floats_different($db_item->grademax,        $this->grademax);
        $multfactordiff  = grade_floats_different($db_item->multfactor,      $this->multfactor);
        $plusfactordiff  = grade_floats_different($db_item->plusfactor,      $this->plusfactor);
        $acoefdiff       = grade_floats_different($db_item->aggregationcoef, $this->aggregationcoef);
        $acoefdiff2      = grade_floats_different($db_item->aggregationcoef2, $this->aggregationcoef2);
        $weightoverride  = grade_floats_different($db_item->weightoverride, $this->weightoverride);

        $needsupdatediff = !$db_item->needsupdate &&  $this->needsupdate;    // force regrading only if setting the flag first time
        $lockeddiff      = !empty($db_item->locked) && empty($this->locked); // force regrading only when unlocking

        return ($calculationdiff || $categorydiff || $gradetypediff || $grademaxdiff || $grademindiff || $scaleiddiff
             || $outcomeiddiff || $multfactordiff || $plusfactordiff || $needsupdatediff
             || $lockeddiff || $acoefdiff || $acoefdiff2 || $weightoverride || $locktimediff);
    }

    /**
     * Finds and returns a grade_item instance based on params.
     *
     * @static
     * @param array $params associative arrays varname=>value
     * @return grade_item|bool Returns a grade_item instance or false if none found
     */
    public static function fetch($params) {
        return grade_object::fetch_helper('grade_items', 'grade_item', $params);
    }

    /**
     * Check to see if there are any existing grades for this grade_item.
     *
     * @return boolean - true if there are valid grades for this grade_item.
     */
    public function has_grades() {
        global $DB;

        $count = $DB->count_records_select('grade_grades',
                                           'itemid = :gradeitemid AND finalgrade IS NOT NULL',
                                           array('gradeitemid' => $this->id));
        return $count > 0;
    }

    /**
     * Check to see if there are existing overridden grades for this grade_item.
     *
     * @return boolean - true if there are overridden grades for this grade_item.
     */
    public function has_overridden_grades() {
        global $DB;

        $count = $DB->count_records_select('grade_grades',
                                           'itemid = :gradeitemid AND finalgrade IS NOT NULL AND overridden > 0',
                                           array('gradeitemid' => $this->id));
        return $count > 0;
    }

    /**
     * Finds and returns all grade_item instances based on params.
     *
     * @static
     * @param array $params associative arrays varname=>value
     * @return array array of grade_item instances or false if none found.
     */
    public static function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_items', 'grade_item', $params);
    }

    /**
     * Delete all grades and force_regrading of parent category.
     *
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return bool success
     */
    public function delete($source=null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $this->delete_all_grades($source);
        $success = parent::delete($source);
        $transaction->allow_commit();

        if ($success) {
            $event = \core\event\grade_item_deleted::create_from_grade_item($this);
            $event->trigger();
        }

        return $success;
    }

    /**
     * Delete all grades
     *
     * @param string $source from where was the object deleted (mod/forum, manual, etc.)
     * @return bool
     */
    public function delete_all_grades($source=null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        if (!$this->is_course_item()) {
            $this->force_regrading();
        }

        if ($grades = grade_grade::fetch_all(array('itemid'=>$this->id))) {
            foreach ($grades as $grade) {
                $grade->delete($source);
            }
        }

        // Delete all the historical files.
        // We only support feedback files for modules atm.
        if ($this->is_external_item()) {
            $fs = new file_storage();
            $fs->delete_area_files($this->get_context()->id, GRADE_FILE_COMPONENT, GRADE_HISTORY_FEEDBACK_FILEAREA);
        }

        $transaction->allow_commit();

        return true;
    }

    /**
     * Duplicate grade item.
     *
     * @return grade_item The duplicate grade item
     */
    public function duplicate() {
        // Convert current object to array.
        $copy = (array) $this;

        if (empty($copy["id"])) {
            throw new moodle_exception('invalidgradeitemid');
        }

        // Remove fields that will be either unique or automatically filled.
        $removekeys = array();
        $removekeys[] = 'id';
        $removekeys[] = 'idnumber';
        $removekeys[] = 'timecreated';
        $removekeys[] = 'sortorder';
        foreach ($removekeys as $key) {
            unset($copy[$key]);
        }

        // Addendum to name.
        $copy["itemname"] = get_string('duplicatedgradeitem', 'grades', $copy["itemname"]);

        // Create new grade item.
        $gradeitem = new grade_item($copy);

        // Insert grade item into database.
        $gradeitem->insert();

        return $gradeitem;
    }

    /**
     * In addition to perform parent::insert(), calls force_regrading() method too.
     *
     * @param string $source From where was the object inserted (mod/forum, manual, etc.)
     * @return int PK ID if successful, false otherwise
     */
    public function insert($source=null) {
        global $CFG, $DB;

        if (empty($this->courseid)) {
            print_error('cannotinsertgrade');
        }

        // load scale if needed
        $this->load_scale();

        // add parent category if needed
        if (empty($this->categoryid) and !$this->is_course_item() and !$this->is_category_item()) {
            $course_category = grade_category::fetch_course_category($this->courseid);
            $this->categoryid = $course_category->id;

        }

        // always place the new items at the end, move them after insert if needed
        $last_sortorder = $DB->get_field_select('grade_items', 'MAX(sortorder)', "courseid = ?", array($this->courseid));
        if (!empty($last_sortorder)) {
            $this->sortorder = $last_sortorder + 1;
        } else {
            $this->sortorder = 1;
        }

        // add proper item numbers to manual items
        if ($this->itemtype == 'manual') {
            if (empty($this->itemnumber)) {
                $this->itemnumber = 0;
            }
        }

        // make sure there is not 0 in outcomeid
        if (empty($this->outcomeid)) {
            $this->outcomeid = null;
        }

        $this->timecreated = $this->timemodified = time();

        if (parent::insert($source)) {
            // force regrading of items if needed
            $this->force_regrading();

            $event = \core\event\grade_item_created::create_from_grade_item($this);
            $event->trigger();

            return $this->id;

        } else {
            debugging("Could not insert this grade_item in the database!");
            return false;
        }
    }

    /**
     * Set idnumber of grade item, updates also course_modules table
     *
     * @param string $idnumber (without magic quotes)
     * @return bool success
     */
    public function add_idnumber($idnumber) {
        global $DB;
        if (!empty($this->idnumber)) {
            return false;
        }

        if ($this->itemtype == 'mod' and !$this->is_outcome_item()) {
            if ($this->itemnumber == 0) {
                // for activity modules, itemnumber 0 is synced with the course_modules
                if (!$cm = get_coursemodule_from_instance($this->itemmodule, $this->iteminstance, $this->courseid)) {
                    return false;
                }
                if (!empty($cm->idnumber)) {
                    return false;
                }
                $DB->set_field('course_modules', 'idnumber', $idnumber, array('id' => $cm->id));
                $this->idnumber = $idnumber;
                return $this->update();
            } else {
                $this->idnumber = $idnumber;
                return $this->update();
            }

        } else {
            $this->idnumber = $idnumber;
            return $this->update();
        }
    }

    /**
     * Returns the locked state of this grade_item (if the grade_item is locked OR no specific
     * $userid is given) or the locked state of a specific grade within this item if a specific
     * $userid is given and the grade_item is unlocked.
     *
     * @param int $userid The user's ID
     * @return bool Locked state
     */
    public function is_locked($userid=NULL) {
        global $CFG;

        // Override for any grade items belonging to activities which are in the process of being deleted.
        require_once($CFG->dirroot . '/course/lib.php');
        if (course_module_instance_pending_deletion($this->courseid, $this->itemmodule, $this->iteminstance)) {
            return true;
        }

        if (!empty($this->locked)) {
            return true;
        }

        if (!empty($userid)) {
            if ($grade = grade_grade::fetch(array('itemid'=>$this->id, 'userid'=>$userid))) {
                $grade->grade_item =& $this; // prevent db fetching of cached grade_item
                return $grade->is_locked();
            }
        }

        return false;
    }

    /**
     * Locks or unlocks this grade_item and (optionally) all its associated final grades.
     *
     * @param int $lockedstate 0, 1 or a timestamp int(10) after which date the item will be locked.
     * @param bool $cascade Lock/unlock child objects too
     * @param bool $refresh Refresh grades when unlocking
     * @return bool True if grade_item all grades updated, false if at least one update fails
     */
    public function set_locked($lockedstate, $cascade=false, $refresh=true) {
        if ($lockedstate) {
        /// setting lock
            if ($this->needsupdate) {
                return false; // can not lock grade without first having final grade
            }

            $this->locked = time();
            $this->update();

            if ($cascade) {
                $grades = $this->get_final();
                foreach($grades as $g) {
                    $grade = new grade_grade($g, false);
                    $grade->grade_item =& $this;
                    $grade->set_locked(1, null, false);
                }
            }

            return true;

        } else {
        /// removing lock
            if (!empty($this->locked) and $this->locktime < time()) {
                //we have to reset locktime or else it would lock up again
                $this->locktime = 0;
            }

            $this->locked = 0;
            $this->update();

            if ($cascade) {
                if ($grades = grade_grade::fetch_all(array('itemid'=>$this->id))) {
                    foreach($grades as $grade) {
                        $grade->grade_item =& $this;
                        $grade->set_locked(0, null, false);
                    }
                }
            }

            if ($refresh) {
                //refresh when unlocking
                $this->refresh_grades();
            }

            return true;
        }
    }

    /**
     * Lock the grade if needed. Make sure this is called only when final grades are valid
     */
    public function check_locktime() {
        if (!empty($this->locked)) {
            return; // already locked
        }

        if ($this->locktime and $this->locktime < time()) {
            $this->locked = time();
            $this->update('locktime');
        }
    }

    /**
     * Set the locktime for this grade item.
     *
     * @param int $locktime timestamp for lock to activate
     * @return void
     */
    public function set_locktime($locktime) {
        $this->locktime = $locktime;
        $this->update();
    }

    /**
     * Set the locktime for this grade item.
     *
     * @return int $locktime timestamp for lock to activate
     */
    public function get_locktime() {
        return $this->locktime;
    }

    /**
     * Set the hidden status of grade_item and all grades.
     *
     * 0 mean always visible, 1 means always hidden and a number > 1 is a timestamp to hide until
     *
     * @param int $hidden new hidden status
     * @param bool $cascade apply to child objects too
     */
    public function set_hidden($hidden, $cascade=false) {
        parent::set_hidden($hidden, $cascade);

        if ($cascade) {
            if ($grades = grade_grade::fetch_all(array('itemid'=>$this->id))) {
                foreach($grades as $grade) {
                    $grade->grade_item =& $this;
                    $grade->set_hidden($hidden, $cascade);
                }
            }
        }

        //if marking item visible make sure category is visible MDL-21367
        if( !$hidden ) {
            $category_array = grade_category::fetch_all(array('id'=>$this->categoryid));
            if ($category_array && array_key_exists($this->categoryid, $category_array)) {
                $category = $category_array[$this->categoryid];
                //call set_hidden on the category regardless of whether it is hidden as its parent might be hidden
                $category->set_hidden($hidden, false);
            }
        }
    }

    /**
     * Returns the number of grades that are hidden
     *
     * @param string $groupsql SQL to limit the query by group
     * @param array $params SQL params for $groupsql
     * @param string $groupwheresql Where conditions for $groupsql
     * @return int The number of hidden grades
     */
    public function has_hidden_grades($groupsql="", array $params=null, $groupwheresql="") {
        global $DB;
        $params = (array)$params;
        $params['itemid'] = $this->id;

        return $DB->get_field_sql("SELECT COUNT(*) FROM {grade_grades} g LEFT JOIN "
                            ."{user} u ON g.userid = u.id $groupsql WHERE itemid = :itemid AND hidden = 1 $groupwheresql", $params);
    }

    /**
     * Mark regrading as finished successfully. This will also be called when subsequent regrading will not change any grades.
     * Situations such as an error being found will still result in the regrading being finished.
     */
    public function regrading_finished() {
        global $DB;
        $this->needsupdate = 0;
        //do not use $this->update() because we do not want this logged in grade_item_history
        $DB->set_field('grade_items', 'needsupdate', 0, array('id' => $this->id));
    }

    /**
     * Performs the necessary calculations on the grades_final referenced by this grade_item.
     * Also resets the needsupdate flag once successfully performed.
     *
     * This function must be used ONLY from lib/gradeslib.php/grade_regrade_final_grades(),
     * because the regrading must be done in correct order!!
     *
     * @param int $userid Supply a user ID to limit the regrading to a single user
     * @return bool true if ok, error string otherwise
     */
    public function regrade_final_grades($userid=null) {
        global $CFG, $DB;

        // locked grade items already have correct final grades
        if ($this->is_locked()) {
            return true;
        }

        // calculation produces final value using formula from other final values
        if ($this->is_calculated()) {
            if ($this->compute($userid)) {
                return true;
            } else {
                return "Could not calculate grades for grade item"; // TODO: improve and localize
            }

        // noncalculated outcomes already have final values - raw grades not used
        } else if ($this->is_outcome_item()) {
            return true;

        // aggregate the category grade
        } else if ($this->is_category_item() or $this->is_course_item()) {
            // aggregate category grade item
            $category = $this->load_item_category();
            $category->grade_item =& $this;
            if ($category->generate_grades($userid)) {
                return true;
            } else {
                return "Could not aggregate final grades for category:".$this->id; // TODO: improve and localize
            }

        } else if ($this->is_manual_item()) {
            // manual items track only final grades, no raw grades
            return true;

        } else if (!$this->is_raw_used()) {
            // hmm - raw grades are not used- nothing to regrade
            return true;
        }

        // normal grade item - just new final grades
        $result = true;
        $grade_inst = new grade_grade();
        $fields = implode(',', $grade_inst->required_fields);
        if ($userid) {
            $params = array($this->id, $userid);
            $rs = $DB->get_recordset_select('grade_grades', "itemid=? AND userid=?", $params, '', $fields);
        } else {
            $rs = $DB->get_recordset('grade_grades', array('itemid' => $this->id), '', $fields);
        }
        if ($rs) {
            foreach ($rs as $grade_record) {
                $grade = new grade_grade($grade_record, false);

                if (!empty($grade_record->locked) or !empty($grade_record->overridden)) {
                    // this grade is locked - final grade must be ok
                    continue;
                }

                $grade->finalgrade = $this->adjust_raw_grade($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);

                if (grade_floats_different($grade_record->finalgrade, $grade->finalgrade)) {
                    $success = $grade->update('system');

                    // If successful trigger a user_graded event.
                    if ($success) {
                        $grade->load_grade_item();
                        \core\event\user_graded::create_from_grade($grade, \core\event\base::USER_OTHER)->trigger();
                    } else {
                        $result = "Internal error updating final grade";
                    }
                }
            }
            $rs->close();
        }

        return $result;
    }

    /**
     * Given a float grade value or integer grade scale, applies a number of adjustment based on
     * grade_item variables and returns the result.
     *
     * @param float $rawgrade The raw grade value
     * @param float $rawmin original rawmin
     * @param float $rawmax original rawmax
     * @return mixed
     */
    public function adjust_raw_grade($rawgrade, $rawmin, $rawmax) {
        if (is_null($rawgrade)) {
            return null;
        }

        if ($this->gradetype == GRADE_TYPE_VALUE) { // Dealing with numerical grade

            if ($this->grademax < $this->grademin) {
                return null;
            }

            if ($this->grademax == $this->grademin) {
                return $this->grademax; // no range
            }

            // Standardise score to the new grade range
            // NOTE: skip if the activity provides a manual rescaling option.
            $manuallyrescale = (component_callback_exists('mod_' . $this->itemmodule, 'rescale_activity_grades') !== false);
            if (!$manuallyrescale && ($rawmin != $this->grademin or $rawmax != $this->grademax)) {
                $rawgrade = grade_grade::standardise_score($rawgrade, $rawmin, $rawmax, $this->grademin, $this->grademax);
            }

            // Apply other grade_item factors
            $rawgrade *= $this->multfactor;
            $rawgrade += $this->plusfactor;

            return $this->bounded_grade($rawgrade);

        } else if ($this->gradetype == GRADE_TYPE_SCALE) { // Dealing with a scale value
            if (empty($this->scale)) {
                $this->load_scale();
            }

            if ($this->grademax < 0) {
                return null; // scale not present - no grade
            }

            if ($this->grademax == 0) {
                return $this->grademax; // only one option
            }

            // Convert scale if needed
            // NOTE: skip if the activity provides a manual rescaling option.
            $manuallyrescale = (component_callback_exists('mod_' . $this->itemmodule, 'rescale_activity_grades') !== false);
            if (!$manuallyrescale && ($rawmin != $this->grademin or $rawmax != $this->grademax)) {
                // This should never happen because scales are locked if they are in use.
                $rawgrade = grade_grade::standardise_score($rawgrade, $rawmin, $rawmax, $this->grademin, $this->grademax);
            }

            return $this->bounded_grade($rawgrade);


        } else if ($this->gradetype == GRADE_TYPE_TEXT or $this->gradetype == GRADE_TYPE_NONE) { // no value
            // somebody changed the grading type when grades already existed
            return null;

        } else {
            debugging("Unknown grade type");
            return null;
        }
    }

    /**
     * Update the rawgrademax and rawgrademin for all grade_grades records for this item.
     * Scale every rawgrade to maintain the percentage. This function should be called
     * after the gradeitem has been updated to the new min and max values.
     *
     * @param float $oldgrademin The previous grade min value
     * @param float $oldgrademax The previous grade max value
     * @param float $newgrademin The new grade min value
     * @param float $newgrademax The new grade max value
     * @param string $source from where was the object inserted (mod/forum, manual, etc.)
     * @return bool True on success
     */
    public function rescale_grades_keep_percentage($oldgrademin, $oldgrademax, $newgrademin, $newgrademax, $source = null) {
        global $DB;

        if (empty($this->id)) {
            return false;
        }

        if ($oldgrademax <= $oldgrademin) {
            // Grades cannot be scaled.
            return false;
        }
        $scale = ($newgrademax - $newgrademin) / ($oldgrademax - $oldgrademin);
        if (($newgrademax - $newgrademin) <= 1) {
            // We would lose too much precision, lets bail.
            return false;
        }

        $rs = $DB->get_recordset('grade_grades', array('itemid' => $this->id));

        foreach ($rs as $graderecord) {
            // For each record, create an object to work on.
            $grade = new grade_grade($graderecord, false);
            // Set this object in the item so it doesn't re-fetch it.
            $grade->grade_item = $this;

            if (!$this->is_category_item() || ($this->is_category_item() && $grade->is_overridden())) {
                // Updating the raw grade automatically updates the min/max.
                if ($this->is_raw_used()) {
                    $rawgrade = (($grade->rawgrade - $oldgrademin) * $scale) + $newgrademin;
                    $this->update_raw_grade(false, $rawgrade, $source, false, FORMAT_MOODLE, null, null, null, $grade);
                } else {
                    $finalgrade = (($grade->finalgrade - $oldgrademin) * $scale) + $newgrademin;
                    $this->update_final_grade($grade->userid, $finalgrade, $source);
                }
            }
        }
        $rs->close();

        // Mark this item for regrading.
        $this->force_regrading();

        return true;
    }

    /**
     * Sets this grade_item's needsupdate to true. Also marks the course item as needing update.
     *
     * @return void
     */
    public function force_regrading() {
        global $DB;
        $this->needsupdate = 1;
        //mark this item and course item only - categories and calculated items are always regraded
        $wheresql = "(itemtype='course' OR id=?) AND courseid=?";
        $params   = array($this->id, $this->courseid);
        $DB->set_field_select('grade_items', 'needsupdate', 1, $wheresql, $params);
    }

    /**
     * Instantiates a grade_scale object from the DB if this item's scaleid variable is set
     *
     * @return grade_scale Returns a grade_scale object or null if no scale used
     */
    public function load_scale() {
        if ($this->gradetype != GRADE_TYPE_SCALE) {
            $this->scaleid = null;
        }

        if (!empty($this->scaleid)) {
            //do not load scale if already present
            if (empty($this->scale->id) or $this->scale->id != $this->scaleid) {
                $this->scale = grade_scale::fetch(array('id'=>$this->scaleid));
                if (!$this->scale) {
                    debugging('Incorrect scale id: '.$this->scaleid);
                    $this->scale = null;
                    return null;
                }
                $this->scale->load_items();
            }

            // Until scales are uniformly set to min=0 max=count(scaleitems)-1 throughout Moodle, we
            // stay with the current min=1 max=count(scaleitems)
            $this->grademax = count($this->scale->scale_items);
            $this->grademin = 1;

        } else {
            $this->scale = null;
        }

        return $this->scale;
    }

    /**
     * Instantiates a grade_outcome object from the DB if this item's outcomeid variable is set
     *
     * @return grade_outcome This grade item's associated grade_outcome or null
     */
    public function load_outcome() {
        if (!empty($this->outcomeid)) {
            $this->outcome = grade_outcome::fetch(array('id'=>$this->outcomeid));
        }
        return $this->outcome;
    }

    /**
     * Returns the grade_category object this grade_item belongs to (referenced by categoryid)
     * or category attached to category item.
     *
     * @return grade_category|bool Returns a grade_category object if applicable or false if this is a course item
     */
    public function get_parent_category() {
        if ($this->is_category_item() or $this->is_course_item()) {
            return $this->get_item_category();

        } else {
            return grade_category::fetch(array('id'=>$this->categoryid));
        }
    }

    /**
     * Calls upon the get_parent_category method to retrieve the grade_category object
     * from the DB and assigns it to $this->parent_category. It also returns the object.
     *
     * @return grade_category This grade item's parent grade_category.
     */
    public function load_parent_category() {
        if (empty($this->parent_category->id)) {
            $this->parent_category = $this->get_parent_category();
        }
        return $this->parent_category;
    }

    /**
     * Returns the grade_category for a grade category grade item
     *
     * @return grade_category|bool Returns a grade_category instance if applicable or false otherwise
     */
    public function get_item_category() {
        if (!$this->is_course_item() and !$this->is_category_item()) {
            return false;
        }
        return grade_category::fetch(array('id'=>$this->iteminstance));
    }

    /**
     * Calls upon the get_item_category method to retrieve the grade_category object
     * from the DB and assigns it to $this->item_category. It also returns the object.
     *
     * @return grade_category
     */
    public function load_item_category() {
        if (empty($this->item_category->id)) {
            $this->item_category = $this->get_item_category();
        }
        return $this->item_category;
    }

    /**
     * Is the grade item associated with category?
     *
     * @return bool
     */
    public function is_category_item() {
        return ($this->itemtype == 'category');
    }

    /**
     * Is the grade item associated with course?
     *
     * @return bool
     */
    public function is_course_item() {
        return ($this->itemtype == 'course');
    }

    /**
     * Is this a manually graded item?
     *
     * @return bool
     */
    public function is_manual_item() {
        return ($this->itemtype == 'manual');
    }

    /**
     * Is this an outcome item?
     *
     * @return bool
     */
    public function is_outcome_item() {
        return !empty($this->outcomeid);
    }

    /**
     * Is the grade item external - associated with module, plugin or something else?
     *
     * @return bool
     */
    public function is_external_item() {
        return ($this->itemtype == 'mod');
    }

    /**
     * Is the grade item overridable
     *
     * @return bool
     */
    public function is_overridable_item() {
        if ($this->is_course_item() or $this->is_category_item()) {
            $overridable = (bool) get_config('moodle', 'grade_overridecat');
        } else {
            $overridable = false;
        }

        return !$this->is_outcome_item() and ($this->is_external_item() or $this->is_calculated() or $overridable);
    }

    /**
     * Is the grade item feedback overridable
     *
     * @return bool
     */
    public function is_overridable_item_feedback() {
        return !$this->is_outcome_item() and $this->is_external_item();
    }

    /**
     * Returns true if grade items uses raw grades
     *
     * @return bool
     */
    public function is_raw_used() {
        return ($this->is_external_item() and !$this->is_calculated() and !$this->is_outcome_item());
    }

    /**
     * Returns true if the grade item is an aggreggated type grade.
     *
     * @since  Moodle 2.8.7, 2.9.1
     * @return bool
     */
    public function is_aggregate_item() {
        return ($this->is_category_item() || $this->is_course_item());
    }

    /**
     * Returns the grade item associated with the course
     *
     * @param int $courseid
     * @return grade_item Course level grade item object
     */
    public static function fetch_course_item($courseid) {
        if ($course_item = grade_item::fetch(array('courseid'=>$courseid, 'itemtype'=>'course'))) {
            return $course_item;
        }

        // first get category - it creates the associated grade item
        $course_category = grade_category::fetch_course_category($courseid);
        return $course_category->get_grade_item();
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
     * Checks if grade calculated. Returns this object's calculation.
     *
     * @return bool true if grade item calculated.
     */
    public function is_calculated() {
        if (empty($this->calculation)) {
            return false;
        }

        /*
         * The main reason why we use the ##gixxx## instead of [[idnumber]] is speed of depends_on(),
         * we would have to fetch all course grade items to find out the ids.
         * Also if user changes the idnumber the formula does not need to be updated.
         */

        // first detect if we need to change calculation formula from [[idnumber]] to ##giXXX## (after backup, etc.)
        if (!$this->calculation_normalized and strpos($this->calculation, '[[') !== false) {
            $this->set_calculation($this->calculation);
        }

        return !empty($this->calculation);
    }

    /**
     * Returns calculation string if grade calculated.
     *
     * @return string Returns the grade item's calculation if calculation is used, null if not
     */
    public function get_calculation() {
        if ($this->is_calculated()) {
            return grade_item::denormalize_formula($this->calculation, $this->courseid);

        } else {
            return NULL;
        }
    }

    /**
     * Sets this item's calculation (creates it) if not yet set, or
     * updates it if already set (in the DB). If no calculation is given,
     * the calculation is removed.
     *
     * @param string $formula string representation of formula used for calculation
     * @return bool success
     */
    public function set_calculation($formula) {
        $this->calculation = grade_item::normalize_formula($formula, $this->courseid);
        $this->calculation_normalized = true;
        return $this->update();
    }

    /**
     * Denormalizes the calculation formula to [idnumber] form
     *
     * @param string $formula A string representation of the formula
     * @param int $courseid The course ID
     * @return string The denormalized formula as a string
     */
    public static function denormalize_formula($formula, $courseid) {
        if (empty($formula)) {
            return '';
        }

        // denormalize formula - convert ##giXX## to [[idnumber]]
        if (preg_match_all('/##gi(\d+)##/', $formula, $matches)) {
            foreach ($matches[1] as $id) {
                if ($grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$courseid))) {
                    if (!empty($grade_item->idnumber)) {
                        $formula = str_replace('##gi'.$grade_item->id.'##', '[['.$grade_item->idnumber.']]', $formula);
                    }
                }
            }
        }

        return $formula;

    }

    /**
     * Normalizes the calculation formula to [#giXX#] form
     *
     * @param string $formula The formula
     * @param int $courseid The course ID
     * @return string The normalized formula as a string
     */
    public static function normalize_formula($formula, $courseid) {
        $formula = trim($formula);

        if (empty($formula)) {
            return NULL;

        }

        // normalize formula - we want grade item ids ##giXXX## instead of [[idnumber]]
        if ($grade_items = grade_item::fetch_all(array('courseid'=>$courseid))) {
            foreach ($grade_items as $grade_item) {
                $formula = str_replace('[['.$grade_item->idnumber.']]', '##gi'.$grade_item->id.'##', $formula);
            }
        }

        return $formula;
    }

    /**
     * Returns the final values for this grade item (as imported by module or other source).
     *
     * @param int $userid Optional: to retrieve a single user's final grade
     * @return array|grade_grade An array of all grade_grade instances for this grade_item, or a single grade_grade instance.
     */
    public function get_final($userid=NULL) {
        global $DB;
        if ($userid) {
            if ($user = $DB->get_record('grade_grades', array('itemid' => $this->id, 'userid' => $userid))) {
                return $user;
            }

        } else {
            if ($grades = $DB->get_records('grade_grades', array('itemid' => $this->id))) {
                //TODO: speed up with better SQL (MDL-31380)
                $result = array();
                foreach ($grades as $grade) {
                    $result[$grade->userid] = $grade;
                }
                return $result;
            } else {
                return array();
            }
        }
    }

    /**
     * Get (or create if not exist yet) grade for this user
     *
     * @param int $userid The user ID
     * @param bool $create If true and the user has no grade for this grade item a new grade_grade instance will be inserted
     * @return grade_grade The grade_grade instance for the user for this grade item
     */
    public function get_grade($userid, $create=true) {
        if (empty($this->id)) {
            debugging('Can not use before insert');
            return false;
        }

        $grade = new grade_grade(array('userid'=>$userid, 'itemid'=>$this->id));
        if (empty($grade->id) and $create) {
            $grade->insert();
        }

        return $grade;
    }

    /**
     * Returns the sortorder of this grade_item. This method is also available in
     * grade_category, for cases where the object type is not know.
     *
     * @return int Sort order
     */
    public function get_sortorder() {
        return $this->sortorder;
    }

    /**
     * Returns the idnumber of this grade_item. This method is also available in
     * grade_category, for cases where the object type is not know.
     *
     * @return string The grade item idnumber
     */
    public function get_idnumber() {
        return $this->idnumber;
    }

    /**
     * Returns this grade_item. This method is also available in
     * grade_category, for cases where the object type is not know.
     *
     * @return grade_item
     */
    public function get_grade_item() {
        return $this;
    }

    /**
     * Sets the sortorder of this grade_item. This method is also available in
     * grade_category, for cases where the object type is not know.
     *
     * @param int $sortorder
     */
    public function set_sortorder($sortorder) {
        if ($this->sortorder == $sortorder) {
            return;
        }
        $this->sortorder = $sortorder;
        $this->update();
    }

    /**
     * Update this grade item's sortorder so that it will appear after $sortorder
     *
     * @param int $sortorder The sort order to place this grade item after
     */
    public function move_after_sortorder($sortorder) {
        global $CFG, $DB;

        //make some room first
        $params = array($sortorder, $this->courseid);
        $sql = "UPDATE {grade_items}
                   SET sortorder = sortorder + 1
                 WHERE sortorder > ? AND courseid = ?";
        $DB->execute($sql, $params);

        $this->set_sortorder($sortorder + 1);
    }

    /**
     * Detect duplicate grade item's sortorder and re-sort them.
     * Note: Duplicate sortorder will be introduced while duplicating activities or
     * merging two courses.
     *
     * @param int $courseid id of the course for which grade_items sortorder need to be fixed.
     */
    public static function fix_duplicate_sortorder($courseid) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $sql = "SELECT DISTINCT g1.id, g1.courseid, g1.sortorder
                    FROM {grade_items} g1
                    JOIN {grade_items} g2 ON g1.courseid = g2.courseid
                WHERE g1.sortorder = g2.sortorder AND g1.id != g2.id AND g1.courseid = :courseid
                ORDER BY g1.sortorder DESC, g1.id DESC";

        // Get all duplicates in course highest sort order, and higest id first so that we can make space at the
        // bottom higher end of the sort orders and work down by id.
        $rs = $DB->get_recordset_sql($sql, array('courseid' => $courseid));

        foreach($rs as $duplicate) {
            $DB->execute("UPDATE {grade_items}
                            SET sortorder = sortorder + 1
                          WHERE courseid = :courseid AND
                          (sortorder > :sortorder OR (sortorder = :sortorder2 AND id > :id))",
                array('courseid' => $duplicate->courseid,
                    'sortorder' => $duplicate->sortorder,
                    'sortorder2' => $duplicate->sortorder,
                    'id' => $duplicate->id));
        }
        $rs->close();
        $transaction->allow_commit();
    }

    /**
     * Returns the most descriptive field for this object.
     *
     * Determines what type of grade item it is then returns the appropriate string
     *
     * @param bool $fulltotal If the item is a category total, returns $categoryname."total" instead of "Category total" or "Course total"
     * @return string name
     */
    public function get_name($fulltotal=false) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        if (strval($this->itemname) !== '') {
            // MDL-10557

            // Make it obvious to users if the course module to which this grade item relates, is currently being removed.
            $deletionpending = course_module_instance_pending_deletion($this->courseid, $this->itemmodule, $this->iteminstance);
            $deletionnotice = get_string('gradesmoduledeletionprefix', 'grades');

            $options = ['context' => context_course::instance($this->courseid)];
            return $deletionpending ?
                format_string($deletionnotice . ' ' . $this->itemname, true, $options) :
                format_string($this->itemname, true, $options);

        } else if ($this->is_course_item()) {
            return get_string('coursetotal', 'grades');

        } else if ($this->is_category_item()) {
            if ($fulltotal) {
                $category = $this->load_parent_category();
                $a = new stdClass();
                $a->category = $category->get_name();
                return get_string('categorytotalfull', 'grades', $a);
            } else {
            return get_string('categorytotal', 'grades');
            }

        } else {
            return get_string('grade');
        }
    }

    /**
     * A grade item can return a more detailed description which will be added to the header of the column/row in some reports.
     *
     * @return string description
     */
    public function get_description() {
        if ($this->is_course_item() || $this->is_category_item()) {
            $categoryitem = $this->load_item_category();
            return $categoryitem->get_description();
        }
        return '';
    }

    /**
     * Sets this item's categoryid. A generic method shared by objects that have a parent id of some kind.
     *
     * @param int $parentid The ID of the new parent
     * @param bool $updateaggregationfields Whether or not to convert the aggregation fields when switching between category.
     *                          Set this to false when the aggregation fields have been updated in prevision of the new
     *                          category, typically when the item is freshly created.
     * @return bool True if success
     */
    public function set_parent($parentid, $updateaggregationfields = true) {
        if ($this->is_course_item() or $this->is_category_item()) {
            print_error('cannotsetparentforcatoritem');
        }

        if ($this->categoryid == $parentid) {
            return true;
        }

        // find parent and check course id
        if (!$parent_category = grade_category::fetch(array('id'=>$parentid, 'courseid'=>$this->courseid))) {
            return false;
        }

        $currentparent = $this->load_parent_category();

        if ($updateaggregationfields) {
            $this->set_aggregation_fields_for_aggregation($currentparent->aggregation, $parent_category->aggregation);
        }

        $this->force_regrading();

        // set new parent
        $this->categoryid = $parent_category->id;
        $this->parent_category =& $parent_category;

        return $this->update();
    }

    /**
     * Update the aggregation fields when the aggregation changed.
     *
     * This method should always be called when the aggregation has changed, but also when
     * the item was moved to another category, even it if uses the same aggregation method.
     *
     * Some values such as the weight only make sense within a category, once moved the
     * values should be reset to let the user adapt them accordingly.
     *
     * Note that this method does not save the grade item.
     * {@link grade_item::update()} has to be called manually after using this method.
     *
     * @param  int $from Aggregation method constant value.
     * @param  int $to   Aggregation method constant value.
     * @return boolean   True when at least one field was changed, false otherwise
     */
    public function set_aggregation_fields_for_aggregation($from, $to) {
        $defaults = grade_category::get_default_aggregation_coefficient_values($to);

        $origaggregationcoef = $this->aggregationcoef;
        $origaggregationcoef2 = $this->aggregationcoef2;
        $origweighoverride = $this->weightoverride;

        if ($from == GRADE_AGGREGATE_SUM && $to == GRADE_AGGREGATE_SUM && $this->weightoverride) {
            // Do nothing. We are switching from SUM to SUM and the weight is overriden,
            // a teacher would not expect any change in this situation.

        } else if ($from == GRADE_AGGREGATE_WEIGHTED_MEAN && $to == GRADE_AGGREGATE_WEIGHTED_MEAN) {
            // Do nothing. The weights can be kept in this case.

        } else if (in_array($from, array(GRADE_AGGREGATE_SUM,  GRADE_AGGREGATE_EXTRACREDIT_MEAN, GRADE_AGGREGATE_WEIGHTED_MEAN2))
                && in_array($to, array(GRADE_AGGREGATE_SUM,  GRADE_AGGREGATE_EXTRACREDIT_MEAN, GRADE_AGGREGATE_WEIGHTED_MEAN2))) {

            // Reset all but the the extra credit field.
            $this->aggregationcoef2 = $defaults['aggregationcoef2'];
            $this->weightoverride = $defaults['weightoverride'];

            if ($to != GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                // Normalise extra credit, except for 'Mean with extra credit' which supports higher values than 1.
                $this->aggregationcoef = min(1, $this->aggregationcoef);
            }
        } else {
            // Reset all.
            $this->aggregationcoef = $defaults['aggregationcoef'];
            $this->aggregationcoef2 = $defaults['aggregationcoef2'];
            $this->weightoverride = $defaults['weightoverride'];
        }

        $acoefdiff       = grade_floats_different($origaggregationcoef, $this->aggregationcoef);
        $acoefdiff2      = grade_floats_different($origaggregationcoef2, $this->aggregationcoef2);
        $weightoverride  = grade_floats_different($origweighoverride, $this->weightoverride);

        return $acoefdiff || $acoefdiff2 || $weightoverride;
    }

    /**
     * Makes sure value is a valid grade value.
     *
     * @param float $gradevalue
     * @return mixed float or int fixed grade value
     */
    public function bounded_grade($gradevalue) {
        global $CFG;

        if (is_null($gradevalue)) {
            return null;
        }

        if ($this->gradetype == GRADE_TYPE_SCALE) {
            // no >100% grades hack for scale grades!
            // 1.5 is rounded to 2 ;-)
            return (int)bounded_number($this->grademin, round($gradevalue+0.00001), $this->grademax);
        }

        $grademax = $this->grademax;

        // NOTE: if you change this value you must manually reset the needsupdate flag in all grade items
        $maxcoef = isset($CFG->gradeoverhundredprocentmax) ? $CFG->gradeoverhundredprocentmax : 10; // 1000% max by default

        if (!empty($CFG->unlimitedgrades)) {
            // NOTE: if you change this value you must manually reset the needsupdate flag in all grade items
            $grademax = $grademax * $maxcoef;
        } else if ($this->is_category_item() or $this->is_course_item()) {
            $category = $this->load_item_category();
            if ($category->aggregation >= 100) {
                // grade >100% hack
                $grademax = $grademax * $maxcoef;
            }
        }

        return (float)bounded_number($this->grademin, $gradevalue, $grademax);
    }

    /**
     * Finds out on which other items does this depend directly when doing calculation or category aggregation
     *
     * @param bool $reset_cache
     * @return array of grade_item IDs this one depends on
     */
    public function depends_on($reset_cache=false) {
        global $CFG, $DB;

        if ($reset_cache) {
            $this->dependson_cache = null;
        } else if (isset($this->dependson_cache)) {
            return $this->dependson_cache;
        }

        if ($this->is_locked()) {
            // locked items do not need to be regraded
            $this->dependson_cache = array();
            return $this->dependson_cache;
        }

        if ($this->is_calculated()) {
            if (preg_match_all('/##gi(\d+)##/', $this->calculation, $matches)) {
                $this->dependson_cache = array_unique($matches[1]); // remove duplicates
                return $this->dependson_cache;
            } else {
                $this->dependson_cache = array();
                return $this->dependson_cache;
            }

        } else if ($grade_category = $this->load_item_category()) {
            $params = array();

            //only items with numeric or scale values can be aggregated
            if ($this->gradetype != GRADE_TYPE_VALUE and $this->gradetype != GRADE_TYPE_SCALE) {
                $this->dependson_cache = array();
                return $this->dependson_cache;
            }

            $grade_category->apply_forced_settings();

            if (empty($CFG->enableoutcomes) or $grade_category->aggregateoutcomes) {
                $outcomes_sql = "";
            } else {
                $outcomes_sql = "AND gi.outcomeid IS NULL";
            }

            if (empty($CFG->grade_includescalesinaggregation)) {
                $gtypes = "gi.gradetype = ?";
                $params[] = GRADE_TYPE_VALUE;
            } else {
                $gtypes = "(gi.gradetype = ? OR gi.gradetype = ?)";
                $params[] = GRADE_TYPE_VALUE;
                $params[] = GRADE_TYPE_SCALE;
            }

            $params[] = $grade_category->id;
            $params[] = $this->courseid;
            $params[] = $grade_category->id;
            $params[] = $this->courseid;
            if (empty($CFG->grade_includescalesinaggregation)) {
                $params[] = GRADE_TYPE_VALUE;
            } else {
                $params[] = GRADE_TYPE_VALUE;
                $params[] = GRADE_TYPE_SCALE;
            }
            $sql = "SELECT gi.id
                      FROM {grade_items} gi
                     WHERE $gtypes
                           AND gi.categoryid = ?
                           AND gi.courseid = ?
                           $outcomes_sql
                    UNION

                    SELECT gi.id
                      FROM {grade_items} gi, {grade_categories} gc
                     WHERE (gi.itemtype = 'category' OR gi.itemtype = 'course') AND gi.iteminstance=gc.id
                           AND gc.parent = ?
                           AND gi.courseid = ?
                           AND $gtypes
                           $outcomes_sql";

            if ($children = $DB->get_records_sql($sql, $params)) {
                $this->dependson_cache = array_keys($children);
                return $this->dependson_cache;
            } else {
                $this->dependson_cache = array();
                return $this->dependson_cache;
            }

        } else {
            $this->dependson_cache = array();
            return $this->dependson_cache;
        }
    }

    /**
     * Refetch grades from modules, plugins.
     *
     * @param int $userid optional, limit the refetch to a single user
     * @return bool Returns true on success or if there is nothing to do
     */
    public function refresh_grades($userid=0) {
        global $DB;
        if ($this->itemtype == 'mod') {
            if ($this->is_outcome_item()) {
                //nothing to do
                return true;
            }

            if (!$activity = $DB->get_record($this->itemmodule, array('id' => $this->iteminstance))) {
                debugging("Can not find $this->itemmodule activity with id $this->iteminstance");
                return false;
            }

            if (!$cm = get_coursemodule_from_instance($this->itemmodule, $activity->id, $this->courseid)) {
                debugging('Can not find course module');
                return false;
            }

            $activity->modname    = $this->itemmodule;
            $activity->cmidnumber = $cm->idnumber;

            return grade_update_mod_grades($activity, $userid);
        }

        return true;
    }

    /**
     * Updates final grade value for given user, this is a only way to update final
     * grades from gradebook and import because it logs the change in history table
     * and deals with overridden flag. This flag is set to prevent later overriding
     * from raw grades submitted from modules.
     *
     * @param int $userid The graded user
     * @param float|false $finalgrade The float value of final grade, false means do not change
     * @param string $source The modification source
     * @param string $feedback Optional teacher feedback
     * @param int $feedbackformat A format like FORMAT_PLAIN or FORMAT_HTML
     * @param int $usermodified The ID of the user making the modification
     * @param int $timemodified Optional parameter to set the time modified, if not present current time.
     * @return bool success
     */
    public function update_final_grade($userid, $finalgrade = false,
                                       $source = null, $feedback = false,
                                       $feedbackformat = FORMAT_MOODLE,
                                       $usermodified = null, $timemodified = null) {
        global $USER, $CFG;

        $result = true;

        // no grading used or locked
        if ($this->gradetype == GRADE_TYPE_NONE or $this->is_locked()) {
            return false;
        }

        $grade = new grade_grade(array('itemid'=>$this->id, 'userid'=>$userid));
        $grade->grade_item =& $this; // prevent db fetching of this grade_item

        if (empty($usermodified)) {
            $grade->usermodified = $USER->id;
        } else {
            $grade->usermodified = $usermodified;
        }

        if ($grade->is_locked()) {
            // do not update locked grades at all
            return false;
        }

        $locktime = $grade->get_locktime();
        if ($locktime and $locktime < time()) {
            // do not update grades that should be already locked, force regrade instead
            $this->force_regrading();
            return false;
        }

        $oldgrade = new stdClass();
        $oldgrade->finalgrade     = $grade->finalgrade;
        $oldgrade->overridden     = $grade->overridden;
        $oldgrade->feedback       = $grade->feedback;
        $oldgrade->feedbackformat = $grade->feedbackformat;
        $oldgrade->rawgrademin    = $grade->rawgrademin;
        $oldgrade->rawgrademax    = $grade->rawgrademax;

        // MDL-31713 rawgramemin and max must be up to date so conditional access %'s works properly.
        $grade->rawgrademin = $this->grademin;
        $grade->rawgrademax = $this->grademax;
        $grade->rawscaleid  = $this->scaleid;

        // changed grade?
        if ($finalgrade !== false) {
            if ($this->is_overridable_item()) {
                $grade->overridden = time();
            }

            $grade->finalgrade = $this->bounded_grade($finalgrade);
        }

        // do we have comment from teacher?
        if ($feedback !== false) {
            if ($this->is_overridable_item_feedback()) {
                // external items (modules, plugins) may have own feedback
                $grade->overridden = time();
            }

            $grade->feedback       = $feedback;
            $grade->feedbackformat = $feedbackformat;
        }

        $gradechanged = false;
        if (empty($grade->id)) {
            $grade->timecreated = null;   // Hack alert - date submitted - no submission yet.
            $grade->timemodified = $timemodified ?? time(); // Hack alert - date graded.
            $result = (bool)$grade->insert($source);

            // If the grade insert was successful and the final grade was not null then trigger a user_graded event.
            if ($result && !is_null($grade->finalgrade)) {
                \core\event\user_graded::create_from_grade($grade)->trigger();
            }
            $gradechanged = true;
        } else {
            // Existing grade_grades.

            if (grade_floats_different($grade->finalgrade, $oldgrade->finalgrade)
                    or grade_floats_different($grade->rawgrademin, $oldgrade->rawgrademin)
                    or grade_floats_different($grade->rawgrademax, $oldgrade->rawgrademax)
                    or ($oldgrade->overridden == 0 and $grade->overridden > 0)) {
                $gradechanged = true;
            }

            if ($grade->feedback === $oldgrade->feedback and $grade->feedbackformat == $oldgrade->feedbackformat and
                    $gradechanged === false) {
                // No grade nor feedback changed.
                return $result;
            }

            $grade->timemodified = $timemodified ?? time(); // Hack alert - date graded.
            $result = $grade->update($source);

            // If the grade update was successful and the actual grade has changed then trigger a user_graded event.
            if ($result && grade_floats_different($grade->finalgrade, $oldgrade->finalgrade)) {
                \core\event\user_graded::create_from_grade($grade)->trigger();
            }
        }

        if (!$result) {
            // Something went wrong - better force final grade recalculation.
            $this->force_regrading();
            return $result;
        }

        // If we are not updating grades we don't need to recalculate the whole course.
        if (!$gradechanged) {
            return $result;
        }

        if ($this->is_course_item() and !$this->needsupdate) {
            if (grade_regrade_final_grades($this->courseid, $userid, $this) !== true) {
                $this->force_regrading();
            }

        } else if (!$this->needsupdate) {

            $course_item = grade_item::fetch_course_item($this->courseid);
            if (!$course_item->needsupdate) {
                if (grade_regrade_final_grades($this->courseid, $userid, $this) !== true) {
                    $this->force_regrading();
                }
            } else {
                $this->force_regrading();
            }
        }

        return $result;
    }


    /**
     * Updates raw grade value for given user, this is a only way to update raw
     * grades from external source (modules, etc.),
     * because it logs the change in history table and deals with final grade recalculation.
     *
     * @param int $userid the graded user
     * @param mixed $rawgrade float value of raw grade - false means do not change
     * @param string $source modification source
     * @param string $feedback optional teacher feedback
     * @param int $feedbackformat A format like FORMAT_PLAIN or FORMAT_HTML
     * @param int $usermodified the ID of the user who did the grading
     * @param int $dategraded A timestamp of when the student's work was graded
     * @param int $datesubmitted A timestamp of when the student's work was submitted
     * @param grade_grade $grade A grade object, useful for bulk upgrades
     * @param array $feedbackfiles An array identifying the location of files we want to copy to the gradebook feedback area.
     *        Example -
     *        [
     *            'contextid' => 1,
     *            'component' => 'mod_xyz',
     *            'filearea' => 'mod_xyz_feedback',
     *            'itemid' => 2
     *        ];
     * @return bool success
     */
    public function update_raw_grade($userid, $rawgrade = false, $source = null, $feedback = false,
            $feedbackformat = FORMAT_MOODLE, $usermodified = null, $dategraded = null, $datesubmitted=null,
            $grade = null, array $feedbackfiles = []) {
        global $USER;

        $result = true;

        // calculated grades can not be updated; course and category can not be updated  because they are aggregated
        if (!$this->is_raw_used() or $this->gradetype == GRADE_TYPE_NONE or $this->is_locked()) {
            return false;
        }

        if (is_null($grade)) {
            //fetch from db
            $grade = new grade_grade(array('itemid'=>$this->id, 'userid'=>$userid));
        }
        $grade->grade_item =& $this; // prevent db fetching of this grade_item

        if (empty($usermodified)) {
            $grade->usermodified = $USER->id;
        } else {
            $grade->usermodified = $usermodified;
        }

        if ($grade->is_locked()) {
            // do not update locked grades at all
            return false;
        }

        $locktime = $grade->get_locktime();
        if ($locktime and $locktime < time()) {
            // do not update grades that should be already locked and force regrade
            $this->force_regrading();
            return false;
        }

        $oldgrade = new stdClass();
        $oldgrade->finalgrade     = $grade->finalgrade;
        $oldgrade->rawgrade       = $grade->rawgrade;
        $oldgrade->rawgrademin    = $grade->rawgrademin;
        $oldgrade->rawgrademax    = $grade->rawgrademax;
        $oldgrade->rawscaleid     = $grade->rawscaleid;
        $oldgrade->feedback       = $grade->feedback;
        $oldgrade->feedbackformat = $grade->feedbackformat;

        // use new min and max
        $grade->rawgrade    = $grade->rawgrade;
        $grade->rawgrademin = $this->grademin;
        $grade->rawgrademax = $this->grademax;
        $grade->rawscaleid  = $this->scaleid;

        // change raw grade?
        if ($rawgrade !== false) {
            $grade->rawgrade = $rawgrade;
        }

        // empty feedback means no feedback at all
        if ($feedback === '') {
            $feedback = null;
        }

        // do we have comment from teacher?
        if ($feedback !== false and !$grade->is_overridden()) {
            $grade->feedback       = $feedback;
            $grade->feedbackformat = $feedbackformat;
            $grade->feedbackfiles  = $feedbackfiles;
        }

        // update final grade if possible
        if (!$grade->is_locked() and !$grade->is_overridden()) {
            $grade->finalgrade = $this->adjust_raw_grade($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);
        }

        // TODO: hack alert - create new fields for these in 2.0
        $oldgrade->timecreated  = $grade->timecreated;
        $oldgrade->timemodified = $grade->timemodified;

        $grade->timecreated = $datesubmitted;

        if ($grade->is_overridden()) {
            // keep original graded date - update_final_grade() sets this for overridden grades

        } else if (is_null($grade->rawgrade) and is_null($grade->feedback)) {
            // no grade and feedback means no grading yet
            $grade->timemodified = null;

        } else if (!empty($dategraded)) {
            // fine - module sends info when graded (yay!)
            $grade->timemodified = $dategraded;

        } else if (grade_floats_different($grade->finalgrade, $oldgrade->finalgrade)
                   or $grade->feedback !== $oldgrade->feedback) {
            // guess - if either grade or feedback changed set new graded date
            $grade->timemodified = time();

        } else {
            //keep original graded date
        }
        // end of hack alert

        $gradechanged = false;
        if (empty($grade->id)) {
            $result = (bool)$grade->insert($source);

            // If the grade insert was successful and the final grade was not null then trigger a user_graded event.
            if ($result && !is_null($grade->finalgrade)) {
                \core\event\user_graded::create_from_grade($grade)->trigger();
            }
            $gradechanged = true;
        } else {
            // Existing grade_grades.

            if (grade_floats_different($grade->finalgrade,  $oldgrade->finalgrade)
                    or grade_floats_different($grade->rawgrade,    $oldgrade->rawgrade)
                    or grade_floats_different($grade->rawgrademin, $oldgrade->rawgrademin)
                    or grade_floats_different($grade->rawgrademax, $oldgrade->rawgrademax)
                    or $grade->rawscaleid != $oldgrade->rawscaleid) {
                $gradechanged = true;
            }

            // The timecreated and timemodified checking is part of the hack above.
            if ($gradechanged === false and
                    $grade->feedback === $oldgrade->feedback and
                    $grade->feedbackformat == $oldgrade->feedbackformat and
                    $grade->timecreated == $oldgrade->timecreated and
                    $grade->timemodified == $oldgrade->timemodified) {
                // No changes.
                return $result;
            }
            $result = $grade->update($source);

            // If the grade update was successful and the actual grade has changed then trigger a user_graded event.
            if ($result && grade_floats_different($grade->finalgrade, $oldgrade->finalgrade)) {
                \core\event\user_graded::create_from_grade($grade)->trigger();
            }
        }

        if (!$result) {
            // Something went wrong - better force final grade recalculation.
            $this->force_regrading();
            return $result;
        }

        // If we are not updating grades we don't need to recalculate the whole course.
        if (!$gradechanged) {
            return $result;
        }

        if (!$this->needsupdate) {
            $course_item = grade_item::fetch_course_item($this->courseid);
            if (!$course_item->needsupdate) {
                if (grade_regrade_final_grades($this->courseid, $userid, $this) !== true) {
                    $this->force_regrading();
                }
            }
        }

        return $result;
    }

    /**
     * Calculates final grade values using the formula in the calculation property.
     * The parameters are taken from final grades of grade items in current course only.
     *
     * @param int $userid Supply a user ID to limit the calculations to the grades of a single user
     * @return bool false if error
     */
    public function compute($userid=null) {
        global $CFG, $DB;

        if (!$this->is_calculated()) {
            return false;
        }

        require_once($CFG->libdir.'/mathslib.php');

        if ($this->is_locked()) {
            return true; // no need to recalculate locked items
        }

        // Precreate grades - we need them to exist
        if ($userid) {
            $missing = array();
            if (!$DB->record_exists('grade_grades', array('itemid'=>$this->id, 'userid'=>$userid))) {
                $m = new stdClass();
                $m->userid = $userid;
                $missing[] = $m;
            }
        } else {
            // Find any users who have grades for some but not all grade items in this course
            $params = array('gicourseid' => $this->courseid, 'ggitemid' => $this->id);
            $sql = "SELECT gg.userid
                      FROM {grade_grades} gg
                           JOIN {grade_items} gi
                           ON (gi.id = gg.itemid AND gi.courseid = :gicourseid)
                     GROUP BY gg.userid
                     HAVING SUM(CASE WHEN gg.itemid = :ggitemid THEN 1 ELSE 0 END) = 0";
            $missing = $DB->get_records_sql($sql, $params);
        }

        if ($missing) {
            foreach ($missing as $m) {
                $grade = new grade_grade(array('itemid'=>$this->id, 'userid'=>$m->userid), false);
                $grade->grade_item =& $this;
                $grade->insert('system');
            }
        }

        // get used items
        $useditems = $this->depends_on();

        // prepare formula and init maths library
        $formula = preg_replace('/##(gi\d+)##/', '\1', $this->calculation);
        if (strpos($formula, '[[') !== false) {
            // missing item
            return false;
        }
        $this->formula = new calc_formula($formula);

        // where to look for final grades?
        // this itemid is added so that we use only one query for source and final grades
        $gis = array_merge($useditems, array($this->id));
        list($usql, $params) = $DB->get_in_or_equal($gis);

        if ($userid) {
            $usersql = "AND g.userid=?";
            $params[] = $userid;
        } else {
            $usersql = "";
        }

        $grade_inst = new grade_grade();
        $fields = 'g.'.implode(',g.', $grade_inst->required_fields);

        $params[] = $this->courseid;
        $sql = "SELECT $fields
                  FROM {grade_grades} g, {grade_items} gi
                 WHERE gi.id = g.itemid AND gi.id $usql $usersql AND gi.courseid=?
                 ORDER BY g.userid";

        $return = true;

        // group the grades by userid and use formula on the group
        $rs = $DB->get_recordset_sql($sql, $params);
        if ($rs->valid()) {
            $prevuser = 0;
            $grade_records   = array();
            $oldgrade    = null;
            foreach ($rs as $used) {
                if ($used->userid != $prevuser) {
                    if (!$this->use_formula($prevuser, $grade_records, $useditems, $oldgrade)) {
                        $return = false;
                    }
                    $prevuser = $used->userid;
                    $grade_records   = array();
                    $oldgrade    = null;
                }
                if ($used->itemid == $this->id) {
                    $oldgrade = $used;
                }
                $grade_records['gi'.$used->itemid] = $used->finalgrade;
            }
            if (!$this->use_formula($prevuser, $grade_records, $useditems, $oldgrade)) {
                $return = false;
            }
        }
        $rs->close();

        return $return;
    }

    /**
     * Internal function that does the final grade calculation
     *
     * @param int $userid The user ID
     * @param array $params An array of grade items of the form {'gi'.$itemid]} => $finalgrade
     * @param array $useditems An array of grade item IDs that this grade item depends on plus its own ID
     * @param grade_grade $oldgrade A grade_grade instance containing the old values from the database
     * @return bool False if an error occurred
     */
    public function use_formula($userid, $params, $useditems, $oldgrade) {
        if (empty($userid)) {
            return true;
        }

        // add missing final grade values
        // not graded (null) is counted as 0 - the spreadsheet way
        $allinputsnull = true;
        foreach($useditems as $gi) {
            if (!array_key_exists('gi'.$gi, $params) || is_null($params['gi'.$gi])) {
                $params['gi'.$gi] = 0;
            } else {
                $params['gi'.$gi] = (float)$params['gi'.$gi];
                if ($gi != $this->id) {
                    $allinputsnull = false;
                }
            }
        }

        // can not use own final grade during calculation
        unset($params['gi'.$this->id]);

        // Check to see if the gradebook is frozen. This allows grades to not be altered at all until a user verifies that they
        // wish to update the grades.
        $gradebookcalculationsfreeze = get_config('core', 'gradebook_calculations_freeze_' . $this->courseid);

        $rawminandmaxchanged = false;
        // insert final grade - will be needed later anyway
        if ($oldgrade) {
            // Only run through this code if the gradebook isn't frozen.
            if ($gradebookcalculationsfreeze && (int)$gradebookcalculationsfreeze <= 20150627) {
                // Do nothing.
            } else {
                // The grade_grade for a calculated item should have the raw grade maximum and minimum set to the
                // grade_item grade maximum and minimum respectively.
                if ($oldgrade->rawgrademax != $this->grademax || $oldgrade->rawgrademin != $this->grademin) {
                    $rawminandmaxchanged = true;
                    $oldgrade->rawgrademax = $this->grademax;
                    $oldgrade->rawgrademin = $this->grademin;
                }
            }
            $oldfinalgrade = $oldgrade->finalgrade;
            $grade = new grade_grade($oldgrade, false); // fetching from db is not needed
            $grade->grade_item =& $this;

        } else {
            $grade = new grade_grade(array('itemid'=>$this->id, 'userid'=>$userid), false);
            $grade->grade_item =& $this;
            $rawminandmaxchanged = false;
            if ($gradebookcalculationsfreeze && (int)$gradebookcalculationsfreeze <= 20150627) {
                // Do nothing.
            } else {
                // The grade_grade for a calculated item should have the raw grade maximum and minimum set to the
                // grade_item grade maximum and minimum respectively.
                $rawminandmaxchanged = true;
                $grade->rawgrademax = $this->grademax;
                $grade->rawgrademin = $this->grademin;
            }
            $grade->insert('system');
            $oldfinalgrade = null;
        }

        // no need to recalculate locked or overridden grades
        if ($grade->is_locked() or $grade->is_overridden()) {
            return true;
        }

        if ($allinputsnull) {
            $grade->finalgrade = null;
            $result = true;

        } else {

            // do the calculation
            $this->formula->set_params($params);
            $result = $this->formula->evaluate();

            if ($result === false) {
                $grade->finalgrade = null;

            } else {
                // normalize
                $grade->finalgrade = $this->bounded_grade($result);
            }
        }

        // Only run through this code if the gradebook isn't frozen.
        if ($gradebookcalculationsfreeze && (int)$gradebookcalculationsfreeze <= 20150627) {
            // Update in db if changed.
            if (grade_floats_different($grade->finalgrade, $oldfinalgrade)) {
                $grade->timemodified = time();
                $success = $grade->update('compute');

                // If successful trigger a user_graded event.
                if ($success) {
                    \core\event\user_graded::create_from_grade($grade)->trigger();
                }
            }
        } else {
            // Update in db if changed.
            if (grade_floats_different($grade->finalgrade, $oldfinalgrade) || $rawminandmaxchanged) {
                $grade->timemodified = time();
                $success = $grade->update('compute');

                // If successful trigger a user_graded event.
                if ($success) {
                    \core\event\user_graded::create_from_grade($grade)->trigger();
                }
            }
        }

        if ($result !== false) {
            //lock grade if needed
        }

        if ($result === false) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Validate the formula.
     *
     * @param string $formulastr
     * @return bool true if calculation possible, false otherwise
     */
    public function validate_formula($formulastr) {
        global $CFG, $DB;
        require_once($CFG->libdir.'/mathslib.php');

        $formulastr = grade_item::normalize_formula($formulastr, $this->courseid);

        if (empty($formulastr)) {
            return true;
        }

        if (strpos($formulastr, '=') !== 0) {
            return get_string('errorcalculationnoequal', 'grades');
        }

        // get used items
        if (preg_match_all('/##gi(\d+)##/', $formulastr, $matches)) {
            $useditems = array_unique($matches[1]); // remove duplicates
        } else {
            $useditems = array();
        }

        // MDL-11902
        // unset the value if formula is trying to reference to itself
        // but array keys does not match itemid
        if (!empty($this->id)) {
            $useditems = array_diff($useditems, array($this->id));
            //unset($useditems[$this->id]);
        }

        // prepare formula and init maths library
        $formula = preg_replace('/##(gi\d+)##/', '\1', $formulastr);
        $formula = new calc_formula($formula);


        if (empty($useditems)) {
            $grade_items = array();

        } else {
            list($usql, $params) = $DB->get_in_or_equal($useditems);
            $params[] = $this->courseid;
            $sql = "SELECT gi.*
                      FROM {grade_items} gi
                     WHERE gi.id $usql and gi.courseid=?"; // from the same course only!

            if (!$grade_items = $DB->get_records_sql($sql, $params)) {
                $grade_items = array();
            }
        }

        $params = array();
        foreach ($useditems as $itemid) {
            // make sure all grade items exist in this course
            if (!array_key_exists($itemid, $grade_items)) {
                return false;
            }
            // use max grade when testing formula, this should be ok in 99.9%
            // division by 0 is one of possible problems
            $params['gi'.$grade_items[$itemid]->id] = $grade_items[$itemid]->grademax;
        }

        // do the calculation
        $formula->set_params($params);
        $result = $formula->evaluate();

        // false as result indicates some problem
        if ($result === false) {
            // TODO: add more error hints
            return get_string('errorcalculationunknown', 'grades');
        } else {
            return true;
        }
    }

    /**
     * Returns the value of the display type
     *
     * It can be set at 3 levels: grade_item, course setting and site. The lowest level overrides the higher ones.
     *
     * @return int Display type
     */
    public function get_displaytype() {
        global $CFG;

        if ($this->display == GRADE_DISPLAY_TYPE_DEFAULT) {
            return grade_get_setting($this->courseid, 'displaytype', $CFG->grade_displaytype);

        } else {
            return $this->display;
        }
    }

    /**
     * Returns the value of the decimals field
     *
     * It can be set at 3 levels: grade_item, course setting and site. The lowest level overrides the higher ones.
     *
     * @return int Decimals (0 - 5)
     */
    public function get_decimals() {
        global $CFG;

        if (is_null($this->decimals)) {
            return grade_get_setting($this->courseid, 'decimalpoints', $CFG->grade_decimalpoints);

        } else {
            return $this->decimals;
        }
    }

    /**
     * Returns a string representing the range of grademin - grademax for this grade item.
     *
     * @param int $rangesdisplaytype
     * @param int $rangesdecimalpoints
     * @return string
     */
    function get_formatted_range($rangesdisplaytype=null, $rangesdecimalpoints=null) {

        global $USER;

        // Determine which display type to use for this average
        if (isset($USER->gradeediting) && array_key_exists($this->courseid, $USER->gradeediting) && $USER->gradeediting[$this->courseid]) {
            $displaytype = GRADE_DISPLAY_TYPE_REAL;

        } else if ($rangesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // no ==0 here, please resave report and user prefs
            $displaytype = $this->get_displaytype();

        } else {
            $displaytype = $rangesdisplaytype;
        }

        // Override grade_item setting if a display preference (not default) was set for the averages
        if ($rangesdecimalpoints == GRADE_REPORT_PREFERENCE_INHERIT) {
            $decimalpoints = $this->get_decimals();

        } else {
            $decimalpoints = $rangesdecimalpoints;
        }

        if ($displaytype == GRADE_DISPLAY_TYPE_PERCENTAGE) {
            $grademin = "0 %";
            $grademax = "100 %";

        } else {
            $grademin = grade_format_gradevalue($this->grademin, $this, true, $displaytype, $decimalpoints);
            $grademax = grade_format_gradevalue($this->grademax, $this, true, $displaytype, $decimalpoints);
        }

        return $grademin.'&ndash;'. $grademax;
    }

    /**
     * Queries parent categories recursively to find the aggregationcoef type that applies to this grade item.
     *
     * @return string|false Returns the coefficient string of false is no coefficient is being used
     */
    public function get_coefstring() {
        $parent_category = $this->load_parent_category();
        if ($this->is_category_item()) {
            $parent_category = $parent_category->load_parent_category();
        }

        if ($parent_category->is_aggregationcoef_used()) {
            return $parent_category->get_coefstring();
        } else {
            return false;
        }
    }

    /**
     * Returns whether the grade item can control the visibility of the grades
     *
     * @return bool
     */
    public function can_control_visibility() {
        if (core_component::get_plugin_directory($this->itemtype, $this->itemmodule)) {
            return !plugin_supports($this->itemtype, $this->itemmodule, FEATURE_CONTROLS_GRADE_VISIBILITY, false);
        }
        return parent::can_control_visibility();
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
            \availability_grade\callbacks::grade_item_changed($this->courseid);
        }
    }

    /**
     * Helper function to get the accurate context for this grade column.
     *
     * @return context
     */
    public function get_context() {
        if ($this->itemtype == 'mod') {
            $modinfo = get_fast_modinfo($this->courseid);
            // Sometimes the course module cache is out of date and needs to be rebuilt.
            if (!isset($modinfo->instances[$this->itemmodule][$this->iteminstance])) {
                rebuild_course_cache($this->courseid, true);
                $modinfo = get_fast_modinfo($this->courseid);
            }
            // Even with a rebuilt cache the module does not exist. This means the
            // database is in an invalid state - we will log an error and return
            // the course context but the calling code should be updated.
            if (!isset($modinfo->instances[$this->itemmodule][$this->iteminstance])) {
                mtrace(get_string('moduleinstancedoesnotexist', 'error'));
                $context = \context_course::instance($this->courseid);
            } else {
                $cm = $modinfo->instances[$this->itemmodule][$this->iteminstance];
                $context = \context_module::instance($cm->id);
            }
        } else {
            $context = \context_course::instance($this->courseid);
        }
        return $context;
    }
}
