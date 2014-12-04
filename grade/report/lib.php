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
 * File containing the grade_report class
 *
 * @package   core_grades
 * @copyright 2007 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/gradelib.php');

/**
 * An abstract class containing variables and methods used by all or most reports.
 * @copyright 2007 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_report {
    /**
     * The courseid.
     * @var int $courseid
     */
    public $courseid;

    /**
     * The course.
     * @var object $course
     */
    public $course;

    /** Grade plugin return tracking object.
     * @var object $gpr
     */
    public $gpr;

    /**
     * The context.
     * @var int $context
     */
    public $context;

    /**
     * The grade_tree object.
     * @var object $gtree
     */
    public $gtree;

    /**
     * User preferences related to this report.
     * @var array $prefs
     */
    public $prefs = array();

    /**
     * The roles for this report.
     * @var string $gradebookroles
     */
    public $gradebookroles;

    /**
     * base url for sorting by first/last name.
     * @var string $baseurl
     */
    public $baseurl;

    /**
     * base url for paging.
     * @var string $pbarurl
     */
    public $pbarurl;

    /**
     * Current page (for paging).
     * @var int $page
     */
    public $page;

    /**
     * Array of cached language strings (using get_string() all the time takes a long time!).
     * @var array $lang_strings
     */
    public $lang_strings = array();

    // GROUP VARIABLES (including SQL)

    /**
     * The current group being displayed.
     * @var int $currentgroup
     */
    public $currentgroup;

    /**
     * The current groupname being displayed.
     * @var string $currentgroupname
     */
    public $currentgroupname;

    /**
     * Current course group mode
     * @var int $groupmode
     */
    public $groupmode;

    /**
     * A HTML select element used to select the current group.
     * @var string $group_selector
     */
    public $group_selector;

    /**
     * An SQL fragment used to add linking information to the group tables.
     * @var string $groupsql
     */
    protected $groupsql;

    /**
     * An SQL constraint to append to the queries used by this object to build the report.
     * @var string $groupwheresql
     */
    protected $groupwheresql;

    /**
     * The ordered params for $groupwheresql
     * @var array $groupwheresql_params
     */
    protected $groupwheresql_params = array();

    // USER VARIABLES (including SQL).

    /**
     * An SQL constraint to append to the queries used by this object to build the report.
     * @var string $userwheresql
     */
    protected $userwheresql;

    /**
     * The ordered params for $userwheresql
     * @var array $userwheresql_params
     */
    protected $userwheresql_params = array();

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $page The current page being viewed (when report is paged)
     */
    public function __construct($courseid, $gpr, $context, $page=null) {
        global $CFG, $COURSE, $DB;

        if (empty($CFG->gradebookroles)) {
            print_error('norolesdefined', 'grades');
        }

        $this->courseid  = $courseid;
        if ($this->courseid == $COURSE->id) {
            $this->course = $COURSE;
        } else {
            $this->course = $DB->get_record('course', array('id' => $this->courseid));
        }

        $this->gpr       = $gpr;
        $this->context   = $context;
        $this->page      = $page;

        // roles to be displayed in the gradebook
        $this->gradebookroles = $CFG->gradebookroles;

        // Set up link to preferences page
        $this->preferences_page = $CFG->wwwroot.'/grade/report/grader/preferences.php?id='.$courseid;

        // init gtree in child class
    }

    /**
     * Given the name of a user preference (without grade_report_ prefix), locally saves then returns
     * the value of that preference. If the preference has already been fetched before,
     * the saved value is returned. If the preference is not set at the User level, the $CFG equivalent
     * is given (site default).
     * Can be called statically, but then doesn't benefit from caching
     * @param string $pref The name of the preference (do not include the grade_report_ prefix)
     * @param int $objectid An optional itemid or categoryid to check for a more fine-grained preference
     * @return mixed The value of the preference
     */
    public function get_pref($pref, $objectid=null) {
        global $CFG;
        $fullprefname = 'grade_report_' . $pref;
        $shortprefname = 'grade_' . $pref;

        $retval = null;

        if (!isset($this) OR get_class($this) != 'grade_report') {
            if (!empty($objectid)) {
                $retval = get_user_preferences($fullprefname . $objectid, self::get_pref($pref));
            } else if (isset($CFG->$fullprefname)) {
                $retval = get_user_preferences($fullprefname, $CFG->$fullprefname);
            } else if (isset($CFG->$shortprefname)) {
                $retval = get_user_preferences($fullprefname, $CFG->$shortprefname);
            } else {
                $retval = null;
            }
        } else {
            if (empty($this->prefs[$pref.$objectid])) {

                if (!empty($objectid)) {
                    $retval = get_user_preferences($fullprefname . $objectid);
                    if (empty($retval)) {
                        // No item pref found, we are returning the global preference
                        $retval = $this->get_pref($pref);
                        $objectid = null;
                    }
                } else {
                    $retval = get_user_preferences($fullprefname, $CFG->$fullprefname);
                }
                $this->prefs[$pref.$objectid] = $retval;
            } else {
                $retval = $this->prefs[$pref.$objectid];
            }
        }

        return $retval;
    }

    /**
     * Uses set_user_preferences() to update the value of a user preference. If 'default' is given as the value,
     * the preference will be removed in favour of a higher-level preference.
     * @param string $pref The name of the preference.
     * @param mixed $pref_value The value of the preference.
     * @param int $itemid An optional itemid to which the preference will be assigned
     * @return bool Success or failure.
     */
    public function set_pref($pref, $pref_value='default', $itemid=null) {
        $fullprefname = 'grade_report_' . $pref;
        if ($pref_value == 'default') {
            return unset_user_preference($fullprefname.$itemid);
        } else {
            return set_user_preference($fullprefname.$itemid, $pref_value);
        }
    }

    /**
     * Handles form data sent by this report for this report. Abstract method to implement in all children.
     * @abstract
     * @param array $data
     * @return mixed True or array of errors
     */
    abstract public function process_data($data);

    /**
     * Processes a single action against a category, grade_item or grade.
     * @param string $target Sortorder
     * @param string $action Which action to take (edit, delete etc...)
     * @return
     */
    abstract public function process_action($target, $action);

    /**
     * First checks the cached language strings, then returns match if found, or uses get_string()
     * to get it from the DB, caches it then returns it.
     * @param string $strcode
     * @param string $section Optional language section
     * @return string
     */
    public function get_lang_string($strcode, $section=null) {
        if (empty($this->lang_strings[$strcode])) {
            $this->lang_strings[$strcode] = get_string($strcode, $section);
        }
        return $this->lang_strings[$strcode];
    }

    /**
     * Fetches and returns a count of all the users that will be shown on this page.
     * @param boolean $groups include groups limit
     * @param boolean $users include users limit - default false, used for searching purposes
     * @return int Count of users
     */
    public function get_numusers($groups = true, $users = false) {
        global $CFG, $DB;
        $userwheresql = "";
        $groupsql      = "";
        $groupwheresql = "";

        // Limit to users with a gradeable role.
        list($gradebookrolessql, $gradebookrolesparams) = $DB->get_in_or_equal(explode(',', $this->gradebookroles), SQL_PARAMS_NAMED, 'grbr0');

        // Limit to users with an active enrollment.
        list($enrolledsql, $enrolledparams) = get_enrolled_sql($this->context);

        // We want to query both the current context and parent contexts.
        list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($this->context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');

        $params = array_merge($gradebookrolesparams, $enrolledparams, $relatedctxparams);

        if ($users) {
            $userwheresql = $this->userwheresql;
            $params       = array_merge($params, $this->userwheresql_params);
        }

        if ($groups) {
            $groupsql      = $this->groupsql;
            $groupwheresql = $this->groupwheresql;
            $params        = array_merge($params, $this->groupwheresql_params);
        }

        $sql = "SELECT DISTINCT u.id
                       FROM {user} u
                       JOIN ($enrolledsql) je
                            ON je.id = u.id
                       JOIN {role_assignments} ra
                            ON u.id = ra.userid
                       $groupsql
                      WHERE ra.roleid $gradebookrolessql
                            AND u.deleted = 0
                            $userwheresql
                            $groupwheresql
                            AND ra.contextid $relatedctxsql";
        $selectedusers = $DB->get_records_sql($sql, $params);

        $count = 0;
        // Check if user's enrolment is active and should be displayed.
        if (!empty($selectedusers)) {
            $coursecontext = $this->context->get_course_context(true);

            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
            $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $coursecontext);

            if ($showonlyactiveenrol) {
                $useractiveenrolments = get_enrolled_users($coursecontext, '', 0, 'u.id',  null, 0, 0, true);
            }

            foreach ($selectedusers as $id => $value) {
                if (!$showonlyactiveenrol || ($showonlyactiveenrol && array_key_exists($id, $useractiveenrolments))) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Sets up this object's group variables, mainly to restrict the selection of users to display.
     */
    protected function setup_groups() {
        // find out current groups mode
        if ($this->groupmode = groups_get_course_groupmode($this->course)) {
            $this->currentgroup = groups_get_course_group($this->course, true);
            $this->group_selector = groups_print_course_menu($this->course, $this->pbarurl, true);

            if ($this->groupmode == SEPARATEGROUPS and !$this->currentgroup and !has_capability('moodle/site:accessallgroups', $this->context)) {
                $this->currentgroup = -2; // means can not access any groups at all
            }

            if ($this->currentgroup) {
                $group = groups_get_group($this->currentgroup);
                $this->currentgroupname     = $group->name;
                $this->groupsql             = " JOIN {groups_members} gm ON gm.userid = u.id ";
                $this->groupwheresql        = " AND gm.groupid = :gr_grpid ";
                $this->groupwheresql_params = array('gr_grpid'=>$this->currentgroup);
            }
        }
    }

    /**
     * Sets up this report's user criteria to restrict the selection of users to display.
     */
    public function setup_users() {
        global $SESSION, $DB;

        $this->userwheresql = "";
        $this->userwheresql_params = array();
        if (isset($SESSION->gradereport['filterfirstname']) && !empty($SESSION->gradereport['filterfirstname'])) {
            $this->userwheresql .= ' AND '.$DB->sql_like('u.firstname', ':firstname', false, false);
            $this->userwheresql_params['firstname'] = $SESSION->gradereport['filterfirstname'].'%';
        }
        if (isset($SESSION->gradereport['filtersurname']) && !empty($SESSION->gradereport['filtersurname'])) {
            $this->userwheresql .= ' AND '.$DB->sql_like('u.lastname', ':lastname', false, false);
            $this->userwheresql_params['lastname'] = $SESSION->gradereport['filtersurname'].'%';
        }
    }

    /**
     * Returns an arrow icon inside an <a> tag, for the purpose of sorting a column.
     * @param string $direction
     * @param moodle_url $sortlink
     */
    protected function get_sort_arrow($direction='move', $sortlink=null) {
        global $OUTPUT;
        $pix = array('up' => 't/sort_desc', 'down' => 't/sort_asc', 'move' => 't/sort');
        $matrix = array('up' => 'desc', 'down' => 'asc', 'move' => 'desc');
        $strsort = $this->get_lang_string('sort' . $matrix[$direction]);

        $arrow = $OUTPUT->pix_icon($pix[$direction], $strsort, '', array('class' => 'sorticon'));
        return html_writer::link($sortlink, $arrow, array('title'=>$strsort));
    }

    /**
     * Optionally blank out course/category totals if they contain any hidden items
     * @param string $courseid the course id
     * @param string $course_item an instance of grade_item
     * @param string $finalgrade the grade for the course_item
     * @return array[] containing values for 'grade', 'grademax', 'grademin', 'aggregationstatus' and 'aggregationweight'
     */
    protected function blank_hidden_total_and_adjust_bounds($courseid, $course_item, $finalgrade) {
        global $CFG, $DB;
        static $hiding_affected = null;//array of items in this course affected by hiding

        // If we're dealing with multiple users we need to know when we've moved on to a new user.
        static $previous_userid = null;

        // If we're dealing with multiple courses we need to know when we've moved on to a new course.
        static $previous_courseid = null;

        $coursegradegrade = grade_grade::fetch(array('userid'=>$this->user->id, 'itemid'=>$course_item->id));
        $grademin = $course_item->grademin;
        $grademax = $course_item->grademax;
        if ($coursegradegrade) {
            $grademin = $coursegradegrade->rawgrademin;
            $grademax = $coursegradegrade->rawgrademax;
        } else {
            $coursegradegrade = new grade_grade(array('userid'=>$this->user->id, 'itemid'=>$course_item->id), false);
        }
        $hint = $coursegradegrade->get_aggregation_hint();
        $aggregationstatus = $hint['status'];
        $aggregationweight = $hint['weight'];

        if (!is_array($this->showtotalsifcontainhidden)) {
            debugging('showtotalsifcontainhidden should be an array', DEBUG_DEVELOPER);
            $this->showtotalsifcontainhidden = array($courseid => $this->showtotalsifcontainhidden);
        }

        if ($this->showtotalsifcontainhidden[$courseid] == GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN) {
            return array('grade' => $finalgrade,
                         'grademin' => $grademin,
                         'grademax' => $grademax,
                         'aggregationstatus' => $aggregationstatus,
                         'aggregationweight' => $aggregationweight);
        }

        // If we've moved on to another course or user, reload the grades.
        if ($previous_userid != $this->user->id || $previous_courseid != $courseid) {
            $hiding_affected = null;
            $previous_userid = $this->user->id;
            $previous_courseid = $courseid;
        }

        if (!$hiding_affected) {
            $items = grade_item::fetch_all(array('courseid'=>$courseid));
            $grades = array();
            $sql = "SELECT g.*
                      FROM {grade_grades} g
                      JOIN {grade_items} gi ON gi.id = g.itemid
                     WHERE g.userid = {$this->user->id} AND gi.courseid = {$courseid}";
            if ($gradesrecords = $DB->get_records_sql($sql)) {
                foreach ($gradesrecords as $grade) {
                    $grades[$grade->itemid] = new grade_grade($grade, false);
                }
                unset($gradesrecords);
            }
            foreach ($items as $itemid => $unused) {
                if (!isset($grades[$itemid])) {
                    $grade_grade = new grade_grade();
                    $grade_grade->userid = $this->user->id;
                    $grade_grade->itemid = $items[$itemid]->id;
                    $grades[$itemid] = $grade_grade;
                }
                $grades[$itemid]->grade_item =& $items[$itemid];
            }
            $hiding_affected = grade_grade::get_hiding_affected($grades, $items);
        }

        //if the item definitely depends on a hidden item
        if (array_key_exists($course_item->id, $hiding_affected['altered']) ||
                array_key_exists($course_item->id, $hiding_affected['alteredgrademin']) ||
                array_key_exists($course_item->id, $hiding_affected['alteredgrademax']) ||
                array_key_exists($course_item->id, $hiding_affected['alteredaggregationstatus']) ||
                array_key_exists($course_item->id, $hiding_affected['alteredaggregationweight'])) {
            if (!$this->showtotalsifcontainhidden[$courseid] && array_key_exists($course_item->id, $hiding_affected['altered'])) {
                // Hide the grade, but only when it has changed.
                $finalgrade = null;
            } else {
                //use reprocessed marks that exclude hidden items
                if (array_key_exists($course_item->id, $hiding_affected['altered'])) {
                    $finalgrade = $hiding_affected['altered'][$course_item->id];
                }
                if (array_key_exists($course_item->id, $hiding_affected['alteredgrademin'])) {
                    $grademin = $hiding_affected['alteredgrademin'][$course_item->id];
                }
                if (array_key_exists($course_item->id, $hiding_affected['alteredgrademax'])) {
                    $grademax = $hiding_affected['alteredgrademax'][$course_item->id];
                }
                if (array_key_exists($course_item->id, $hiding_affected['alteredaggregationstatus'])) {
                    $aggregationstatus = $hiding_affected['alteredaggregationstatus'][$course_item->id];
                }
                if (array_key_exists($course_item->id, $hiding_affected['alteredaggregationweight'])) {
                    $aggregationweight = $hiding_affected['alteredaggregationweight'][$course_item->id];
                }

                if (!$this->showtotalsifcontainhidden[$courseid]) {
                    // If the course total is hidden we must hide the weight otherwise
                    // it can be used to compute the course total.
                    $aggregationstatus = 'unknown';
                    $aggregationweight = null;
                }
            }
        } else if (!empty($hiding_affected['unknown'][$course_item->id])) {
            //not sure whether or not this item depends on a hidden item
            if (!$this->showtotalsifcontainhidden[$courseid]) {
                //hide the grade
                $finalgrade = null;
            } else {
                //use reprocessed marks that exclude hidden items
                $finalgrade = $hiding_affected['unknown'][$course_item->id];

                if (array_key_exists($course_item->id, $hiding_affected['alteredgrademin'])) {
                    $grademin = $hiding_affected['alteredgrademin'][$course_item->id];
                }
                if (array_key_exists($course_item->id, $hiding_affected['alteredgrademax'])) {
                    $grademax = $hiding_affected['alteredgrademax'][$course_item->id];
                }
                if (array_key_exists($course_item->id, $hiding_affected['alteredaggregationstatus'])) {
                    $aggregationstatus = $hiding_affected['alteredaggregationstatus'][$course_item->id];
                }
                if (array_key_exists($course_item->id, $hiding_affected['alteredaggregationweight'])) {
                    $aggregationweight = $hiding_affected['alteredaggregationweight'][$course_item->id];
                }
            }
        }

        return array('grade' => $finalgrade, 'grademin' => $grademin, 'grademax' => $grademax, 'aggregationstatus'=>$aggregationstatus, 'aggregationweight'=>$aggregationweight);
    }

    /**
     * Optionally blank out course/category totals if they contain any hidden items
     * @deprecated since Moodle 2.8 - Call blank_hidden_total_and_adjust_bounds instead.
     * @param string $courseid the course id
     * @param string $course_item an instance of grade_item
     * @param string $finalgrade the grade for the course_item
     * @return string The new final grade
     */
    protected function blank_hidden_total($courseid, $course_item, $finalgrade) {
        // Note it is flawed to call this function directly because
        // the aggregated grade does not make sense without the updated min and max information.

        debugging('grade_report::blank_hidden_total() is deprecated.
                   Call grade_report::blank_hidden_total_and_adjust_bounds instead.', DEBUG_DEVELOPER);
        $result = $this->blank_hidden_total_and_adjust_bounds($courseid, $course_item, $finalgrade);
        return $result['grade'];
    }
}

