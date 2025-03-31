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
 * Definition of the grader report class
 *
 * @package   gradereport_grader
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

use core_grades\penalty_manager;

/**
 * Class providing an API for the grader report building and displaying.
 * @uses grade_report
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_report_grader extends grade_report {
    /**
     * The final grades.
     * @var array $grades
     */
    public $grades;

    /**
     * Contains all the grades for the course - even the ones not displayed in the grade tree.
     *
     * @var array $allgrades
     */
    private $allgrades;

    /**
     * Contains all grade items expect GRADE_TYPE_NONE.
     *
     * @var array $allgradeitems
     */
    private $allgradeitems;

    /**
     * Array of errors for bulk grades updating.
     * @var array $gradeserror
     */
    public $gradeserror = array();

    // SQL-RELATED

    /**
     * The id of the grade_item by which this report will be sorted.
     * @var int $sortitemid
     */
    public $sortitemid;

    /**
     * Sortorder used in the SQL selections.
     * @var int $sortorder
     */
    public $sortorder;

    /**
     * An SQL fragment affecting the search for users.
     * @var string $userselect
     */
    public $userselect;

    /**
     * The bound params for $userselect
     * @var array $userselectparams
     */
    public $userselectparams = array();

    /**
     * List of collapsed categories from user preference
     * @var array $collapsed
     */
    public $collapsed;

    /**
     * A count of the rows, used for css classes.
     * @var int $rowcount
     */
    public $rowcount = 0;

    /**
     * Capability check caching
     * @var boolean $canviewhidden
     */
    public $canviewhidden;

    /**
     * @var int Maximum number of students that can be shown on one page
     * @deprecated Since Moodle 4.5 MDL-84245. Use grade_report_grader::get_max_students_per_page() instead.
     */
    #[\core\attribute\deprecated('grade_report_grader::get_max_students_per_page()', since: '4.5', mdl: 'MDL-84245')]
    public const MAX_STUDENTS_PER_PAGE = 5000;

    /**
     * @var int The maximum number of grades that can be shown on one page.
     *
     * More than this causes issues for the browser due to the size of the page.
     */
    public const MAX_GRADES_PER_PAGE = 200000;

    /** @var int[] List of available options on the pagination dropdown */
    public const PAGINATION_OPTIONS = [20, 100];

    /**
     * Allow category grade overriding
     * @var bool $overridecat
     */
    protected $overridecat;

    /** @var array of objects, or empty array if no records were found. */
    protected $users = [];

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $page The current page being viewed (when report is paged)
     * @param int $sortitemid The id of the grade_item by which to sort the table
     * @param string $sort Sorting direction
     */
    public function __construct($courseid, $gpr, $context, $page=null, $sortitemid=null, string $sort = '') {
        global $CFG;
        parent::__construct($courseid, $gpr, $context, $page);

        $this->canviewhidden = has_capability('moodle/grade:viewhidden', context_course::instance($this->course->id));

        // load collapsed settings for this report
        $this->collapsed = static::get_collapsed_preferences($this->course->id);

        if (empty($CFG->enableoutcomes)) {
            $nooutcomes = false;
        } else {
            $nooutcomes = get_user_preferences('grade_report_shownooutcomes');
        }

        // if user report preference set or site report setting set use it, otherwise use course or site setting
        $switch = $this->get_pref('aggregationposition');
        if ($switch == '') {
            $switch = grade_get_setting($this->courseid, 'aggregationposition', $CFG->grade_aggregationposition);
        }

        // Grab the grade_tree for this course
        $this->gtree = new grade_tree($this->courseid, true, $switch, $this->collapsed, $nooutcomes);

        $this->sortitemid = $sortitemid;

        // base url for sorting by first/last name

        $this->baseurl = new moodle_url('index.php', array('id' => $this->courseid));

        $studentsperpage = $this->get_students_per_page();
        if (!empty($this->page) && !empty($studentsperpage)) {
            $this->baseurl->params(array('perpage' => $studentsperpage, 'page' => $this->page));
        }

        $this->pbarurl = new moodle_url('/grade/report/grader/index.php', array('id' => $this->courseid));

        $this->setup_groups();
        $this->setup_users();
        $this->setup_sortitemid($sort);

        $this->overridecat = (bool)get_config('moodle', 'grade_overridecat');
    }

    /**
     * Processes the data sent by the form (grades).
     * Caller is responsible for all access control checks
     * @param array $data form submission (with magic quotes)
     * @return array empty array if success, array of warnings if something fails.
     */
    public function process_data($data) {
        global $DB;
        $warnings = array();

        $separategroups = false;
        $mygroups       = array();
        if ($this->groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $this->context)) {
            $separategroups = true;
            $mygroups = groups_get_user_groups($this->course->id);
            $mygroups = $mygroups[0]; // ignore groupings
            // reorder the groups fro better perf below
            $current = array_search($this->currentgroup, $mygroups);
            if ($current !== false) {
                unset($mygroups[$current]);
                array_unshift($mygroups, $this->currentgroup);
            }
        }
        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);

        // always initialize all arrays
        $queue = array();

        $this->load_users();
        $this->load_final_grades();

        // Were any changes made?
        $changedgrades = false;
        $timepageload = clean_param($data->timepageload, PARAM_INT);

        foreach ($data as $varname => $students) {

            $needsupdate = false;

            // Skip, not a grade.
            if (strpos($varname, 'grade') === 0) {
                $datatype = 'grade';
            } else {
                continue;
            }

            foreach ($students as $userid => $items) {
                $userid = clean_param($userid, PARAM_INT);
                foreach ($items as $itemid => $postedvalue) {
                    $itemid = clean_param($itemid, PARAM_INT);

                    // Was change requested?
                    $oldvalue = $this->grades[$userid][$itemid];
                    if ($datatype === 'grade') {
                        // If there was no grade and there still isn't
                        if (is_null($oldvalue->finalgrade) && $postedvalue == -1) {
                            // -1 means no grade
                            continue;
                        }

                        // If the grade item uses a custom scale
                        if (!empty($oldvalue->grade_item->scaleid)) {

                            if ((int)$oldvalue->finalgrade === (int)$postedvalue) {
                                continue;
                            }
                        } else {
                            // The grade item uses a numeric scale

                            // Format the finalgrade from the DB so that it matches the grade from the client
                            if ($postedvalue === format_float($oldvalue->finalgrade, $oldvalue->grade_item->get_decimals())) {
                                continue;
                            }
                        }

                        $changedgrades = true;
                    }

                    if (!$gradeitem = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$this->courseid))) {
                        throw new \moodle_exception('invalidgradeitemid');
                    }

                    // Pre-process grade
                    if ($datatype == 'grade') {
                        if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                            if ($postedvalue == -1) { // -1 means no grade
                                $finalgrade = null;
                            } else {
                                $finalgrade = $postedvalue;
                            }
                        } else {
                            $finalgrade = unformat_float($postedvalue);
                        }

                        $errorstr = '';
                        $skip = false;

                        $dategraded = $oldvalue->get_dategraded();
                        if (!empty($dategraded) && $timepageload < $dategraded) {
                            // Warn if the grade was updated while we were editing this form.
                            $errorstr = 'gradewasmodifiedduringediting';
                            $skip = true;
                        } else if (!is_null($finalgrade)) {
                            // Warn if the grade is out of bounds.
                            $bounded = $gradeitem->bounded_grade($finalgrade);
                            if ($bounded > $finalgrade) {
                                $errorstr = 'lessthanmin';
                            } else if ($bounded < $finalgrade) {
                                $errorstr = 'morethanmax';
                            }
                        }

                        if ($errorstr) {
                            $userfieldsapi = \core_user\fields::for_name();
                            $userfields = 'id, ' . $userfieldsapi->get_sql('', false, '', '', false)->selects;
                            $user = $DB->get_record('user', array('id' => $userid), $userfields);
                            $gradestr = new stdClass();
                            $gradestr->username = fullname($user, $viewfullnames);
                            $gradestr->itemname = $gradeitem->get_name();
                            $warnings[] = get_string($errorstr, 'grades', $gradestr);
                            if ($skip) {
                                // Skipping the update of this grade it failed the tests above.
                                continue;
                            }
                        }
                    }

                    // group access control
                    if ($separategroups) {
                        // note: we can not use $this->currentgroup because it would fail badly
                        //       when having two browser windows each with different group
                        $sharinggroup = false;
                        foreach ($mygroups as $groupid) {
                            if (groups_is_member($groupid, $userid)) {
                                $sharinggroup = true;
                                break;
                            }
                        }
                        if (!$sharinggroup) {
                            // either group membership changed or somebody is hacking grades of other group
                            $warnings[] = get_string('errorsavegrade', 'grades');
                            continue;
                        }
                    }

                    $gradeitem->update_final_grade($userid, $finalgrade, 'gradebook', false,
                        FORMAT_MOODLE, null, null, true);
                }
            }
        }

        if ($changedgrades) {
            // If a final grade was overriden reload grades so dependent grades like course total will be correct
            $this->grades = null;
        }

        return $warnings;
    }


    /**
     * Setting the sort order, this depends on last state
     * all this should be in the new table class that we might need to use
     * for displaying grades.

     * @param string $sort sorting direction
     */
    private function setup_sortitemid(string $sort = '') {

        global $SESSION;

        if (!isset($SESSION->gradeuserreport)) {
            $SESSION->gradeuserreport = new stdClass();
        }

        if ($this->sortitemid) {
            if (!isset($SESSION->gradeuserreport->sort)) {
                $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
            } else if (!$sort) {
                // this is the first sort, i.e. by last name
                if (!isset($SESSION->gradeuserreport->sortitemid)) {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                } else if ($SESSION->gradeuserreport->sortitemid == $this->sortitemid) {
                    // same as last sort
                    if ($SESSION->gradeuserreport->sort == 'ASC') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    }
                } else {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                }
            }
            $SESSION->gradeuserreport->sortitemid = $this->sortitemid;
        } else {
            // not requesting sort, use last setting (for paging)

            if (isset($SESSION->gradeuserreport->sortitemid)) {
                $this->sortitemid = $SESSION->gradeuserreport->sortitemid;
            } else {
                $this->sortitemid = 'lastname';
            }

            if (isset($SESSION->gradeuserreport->sort)) {
                $this->sortorder = $SESSION->gradeuserreport->sort;
            } else {
                $this->sortorder = 'ASC';
            }
        }

        // If explicit sorting direction exists.
        if ($sort) {
            $this->sortorder = $sort;
            $SESSION->gradeuserreport->sort = $sort;
        }
    }

    /**
     * pulls out the userids of the users to be display, and sorts them
     *
     * @param bool $allusers If we are getting the users within the report, we want them all irrespective of paging.
     */
    public function load_users(bool $allusers = false) {
        global $CFG, $DB;

        if (!empty($this->users)) {
            return;
        }
        $this->setup_users();

        // Limit to users with a gradeable role.
        list($gradebookrolessql, $gradebookrolesparams) = $DB->get_in_or_equal(explode(',', $this->gradebookroles), SQL_PARAMS_NAMED, 'grbr0');

        // Check the status of showing only active enrolments.
        $coursecontext = $this->context->get_course_context(true);
        $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
        $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
        $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $coursecontext);

        // Limit to users with an active enrollment.
        list($enrolledsql, $enrolledparams) = get_enrolled_sql($this->context, '', 0, $showonlyactiveenrol);

        // Fields we need from the user table.
        $userfieldsapi = \core_user\fields::for_identity($this->context)->with_userpic();
        $userfieldssql = $userfieldsapi->get_sql('u', true, '', '', false);

        // We want to query both the current context and parent contexts.
        list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($this->context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');

        // If the user has clicked one of the sort asc/desc arrows.
        if (is_numeric($this->sortitemid)) {
            $params = array_merge(array('gitemid' => $this->sortitemid), $gradebookrolesparams, $this->userwheresql_params,
                    $this->groupwheresql_params, $enrolledparams, $relatedctxparams);

            $sortjoin = "LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = $this->sortitemid";

            if ($this->sortorder == 'ASC') {
                $sort = $DB->sql_order_by_null('g.finalgrade');
            } else {
                $sort = $DB->sql_order_by_null('g.finalgrade', SORT_DESC);
            }
            $sort .= ", u.idnumber, u.lastname, u.firstname, u.email";
        } else {
            $sortjoin = '';

            // The default sort will be that provided by the site for users, unless a valid user field is requested,
            // the value of which takes precedence.
            [$sort] = users_order_by_sql('u', null, $this->context, $userfieldssql->mappings);
            if (array_key_exists($this->sortitemid, $userfieldssql->mappings)) {

                // Ensure user sort field doesn't duplicate one of the default sort fields.
                $usersortfield = $userfieldssql->mappings[$this->sortitemid];
                $defaultsortfields = array_diff(explode(', ', $sort), [$usersortfield]);

                $sort = "{$usersortfield} {$this->sortorder}, " . implode(', ', $defaultsortfields);
            }

            $params = array_merge($gradebookrolesparams, $this->userwheresql_params, $this->groupwheresql_params, $enrolledparams, $relatedctxparams);
        }
        $params = array_merge($userfieldssql->params, $params);
        $sql = "SELECT {$userfieldssql->selects}
                  FROM {user} u
                        {$userfieldssql->joins}
                  JOIN ($enrolledsql) je ON je.id = u.id
                       $this->groupsql
                       $sortjoin
                  JOIN (
                           SELECT DISTINCT ra.userid
                             FROM {role_assignments} ra
                            WHERE ra.roleid IN ($this->gradebookroles)
                              AND ra.contextid $relatedctxsql
                       ) rainner ON rainner.userid = u.id
                   AND u.deleted = 0
                   $this->userwheresql
                   $this->groupwheresql
              ORDER BY $sort";
        // We never work with unlimited result. Limit the number of records by $this->get_max_students_per_page() if no other limit
        // is specified.
        $studentsperpage = ($this->get_students_per_page() && !$allusers) ?
            $this->get_students_per_page() : $this->get_max_students_per_page();
        $this->users = $DB->get_records_sql($sql, $params, $studentsperpage * $this->page, $studentsperpage);

        if (empty($this->users)) {
            $this->userselect = '';
            $this->users = array();
            $this->userselectparams = array();
        } else {
            list($usql, $uparams) = $DB->get_in_or_equal(array_keys($this->users), SQL_PARAMS_NAMED, 'usid0');
            $this->userselect = "AND g.userid $usql";
            $this->userselectparams = $uparams;

            // First flag everyone as not suspended.
            foreach ($this->users as $user) {
                $this->users[$user->id]->suspendedenrolment = false;
            }

            // If we want to mix both suspended and not suspended users, let's find out who is suspended.
            if (!$showonlyactiveenrol) {
                $sql = "SELECT ue.userid
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON e.id = ue.enrolid
                         WHERE ue.userid $usql
                               AND ue.status = :uestatus
                               AND e.status = :estatus
                               AND e.courseid = :courseid
                               AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)
                      GROUP BY ue.userid";

                $time = time();
                $params = array_merge($uparams, array('estatus' => ENROL_INSTANCE_ENABLED, 'uestatus' => ENROL_USER_ACTIVE,
                        'courseid' => $coursecontext->instanceid, 'now1' => $time, 'now2' => $time));
                $useractiveenrolments = $DB->get_records_sql($sql, $params);

                foreach ($this->users as $user) {
                    $this->users[$user->id]->suspendedenrolment = !array_key_exists($user->id, $useractiveenrolments);
                }
            }
        }
        return $this->users;
    }

    /**
     * Load all grade items.
     */
    protected function get_allgradeitems() {
        if (!empty($this->allgradeitems)) {
            return $this->allgradeitems;
        }
        $allgradeitems = grade_item::fetch_all(array('courseid' => $this->courseid));
        // But hang on - don't include ones which are set to not show the grade at all.
        $this->allgradeitems = array_filter($allgradeitems, function($item) {
            return $item->gradetype != GRADE_TYPE_NONE;
        });

        return $this->allgradeitems;
    }

    /**
     * Return the maximum number of students we can display per page.
     *
     * This is based on the number of grade items on the course, to limit the overall number of grades displayed on a single page.
     * Trying to display too many grades causes browser issues.
     *
     * @return int
     */
    public function get_max_students_per_page(): int {
        global $CFG;

        if (isset($CFG->maxgradesperpage) && clean_param($CFG->maxgradesperpage, PARAM_INT) > 0) {
            $maxgradesperpage = $CFG->maxgradesperpage;
        } else {
            $maxgradesperpage = self::MAX_GRADES_PER_PAGE;
        }

        return round($maxgradesperpage / count($this->get_allgradeitems()));
    }

    /**
     * we supply the userids in this query, and get all the grades
     * pulls out all the grades, this does not need to worry about paging
     */
    public function load_final_grades() {
        global $CFG, $DB;

        if (!empty($this->grades)) {
            return;
        }

        if (empty($this->users)) {
            return;
        }

        // please note that we must fetch all grade_grades fields if we want to construct grade_grade object from it!
        $params = array_merge(array('courseid'=>$this->courseid), $this->userselectparams);
        $sql = "SELECT g.*
                  FROM {grade_items} gi,
                       {grade_grades} g
                 WHERE g.itemid = gi.id AND gi.courseid = :courseid {$this->userselect}";

        $userids = array_keys($this->users);
        $allgradeitems = $this->get_allgradeitems();

        if ($grades = $DB->get_records_sql($sql, $params)) {
            foreach ($grades as $graderec) {
                $grade = new grade_grade($graderec, false);
                if (!empty($allgradeitems[$graderec->itemid])) {
                    // Note: Filter out grades which have a grade type of GRADE_TYPE_NONE.
                    // Only grades without this type are present in $allgradeitems.
                    $this->allgrades[$graderec->userid][$graderec->itemid] = $grade;
                }
                if (in_array($graderec->userid, $userids) and array_key_exists($graderec->itemid, $this->gtree->get_items())) { // some items may not be present!!
                    $this->grades[$graderec->userid][$graderec->itemid] = $grade;
                    $this->grades[$graderec->userid][$graderec->itemid]->grade_item = $this->gtree->get_item($graderec->itemid); // db caching
                }
            }
        }

        // prefil grades that do not exist yet
        foreach ($userids as $userid) {
            foreach ($this->gtree->get_items() as $itemid => $unused) {
                if (!isset($this->grades[$userid][$itemid])) {
                    $this->grades[$userid][$itemid] = new grade_grade();
                    $this->grades[$userid][$itemid]->itemid = $itemid;
                    $this->grades[$userid][$itemid]->userid = $userid;
                    $this->grades[$userid][$itemid]->grade_item = $this->gtree->get_item($itemid); // db caching

                    $this->allgrades[$userid][$itemid] = $this->grades[$userid][$itemid];
                }
            }
        }

        // Pre fill grades for any remaining items which might be collapsed.
        foreach ($userids as $userid) {
            foreach ($allgradeitems as $itemid => $gradeitem) {
                if (!isset($this->allgrades[$userid][$itemid])) {
                    $this->allgrades[$userid][$itemid] = new grade_grade();
                    $this->allgrades[$userid][$itemid]->itemid = $itemid;
                    $this->allgrades[$userid][$itemid]->userid = $userid;
                    $this->allgrades[$userid][$itemid]->grade_item = $gradeitem;
                }
            }
        }
    }

    /**
     * Builds and returns the rows that will make up the left part of the grader report
     * This consists of student names and icons, links to user reports and id numbers, as well
     * as header cells for these columns. It also includes the fillers required for the
     * categories displayed on the right side of the report.
     * @param boolean $displayaverages whether to display average rows in the table
     * @return array Array of html_table_row objects
     */
    public function get_left_rows($displayaverages) {
        global $CFG, $OUTPUT;

        // Course context to determine how the user details should be displayed.
        $coursecontext = context_course::instance($this->courseid);

        $rows = [];

        $showuserimage = $this->get_pref('showuserimage');
        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);

        $extrafields = \core_user\fields::get_identity_fields($this->context);

        $arrows = $this->get_sort_arrows($extrafields);

        $colspan = 1 + count($extrafields);

        $levels = count($this->gtree->levels) - 1;

        $fillercell = new html_table_cell();
        $fillercell->header = true;
        $fillercell->attributes['scope'] = 'col';
        $fillercell->attributes['class'] = 'cell topleft';
        $fillercell->text = html_writer::span(get_string('participants'), 'accesshide');
        $fillercell->colspan = $colspan;
        $fillercell->rowspan = $levels;
        $row = new html_table_row(array($fillercell));
        $rows[] = $row;

        for ($i = 1; $i < $levels; $i++) {
            $row = new html_table_row();
            $rows[] = $row;
        }

        $headerrow = new html_table_row();
        $headerrow->attributes['class'] = 'heading';

        $studentheader = new html_table_cell();
        // The browser's scrollbar may partly cover (in certain operative systems) the content in the student header
        // when horizontally scrolling through the table contents (most noticeable when in RTL mode).
        // Therefore, add slight padding on the left or right when using RTL mode.
        $studentheader->attributes['class'] = "header ps-3";
        $studentheader->scope = 'col';
        $studentheader->header = true;
        $studentheader->id = 'studentheader';
        $element = ['type' => 'userfield', 'name' => 'fullname'];
        $studentheader->text = $arrows['studentname'] .
            $this->gtree->get_cell_action_menu($element, 'gradeitem', $this->gpr, $this->baseurl);

        $headerrow->cells[] = $studentheader;

        foreach ($extrafields as $field) {
            $fieldheader = new html_table_cell();
            $fieldheader->attributes['class'] = 'userfield user' . $field;
            $fieldheader->attributes['data-col'] = $field;
            $fieldheader->scope = 'col';
            $fieldheader->header = true;

            $collapsecontext = [
                'field' => $field,
                'name' => \core_user\fields::get_display_name($field),
            ];

            $collapsedicon = $OUTPUT->render_from_template('gradereport_grader/collapse/icon', $collapsecontext);
            // Need to wrap the button into a div with our hooking element for user items, gradeitems already have this.
            $collapsedicon = html_writer::div($collapsedicon, 'd-none', ['data-collapse' => 'expandbutton']);

            $element = ['type' => 'userfield', 'name' => $field];
            $fieldheader->text = $arrows[$field] .
                $this->gtree->get_cell_action_menu($element, 'gradeitem', $this->gpr, $this->baseurl) . $collapsedicon;
            $headerrow->cells[] = $fieldheader;
        }

        $rows[] = $headerrow;

        $suspendedstring = null;

        $usercount = 0;
        foreach ($this->users as $userid => $user) {
            $userrow = new html_table_row();
            $userrow->id = 'fixed_user_'.$userid;
            $userrow->attributes['class'] = ($usercount % 2) ? 'userrow even' : 'userrow odd';

            $usercell = new html_table_cell();
            $usercell->attributes['class'] = ($usercount % 2) ? 'header user even' : 'header user odd';
            $usercount++;

            $usercell->header = true;
            $usercell->scope = 'row';

            if ($showuserimage) {
                $usercell->text = $OUTPUT->render(\core_user::get_profile_picture($user, $coursecontext, [
                    'link' => false, 'visibletoscreenreaders' => false
                ]));
            }

            $fullname = fullname($user, $viewfullnames);
            $usercell->text = html_writer::link(
                \core_user::get_profile_url($user, $coursecontext),
                $usercell->text . $fullname,
                ['class' => 'username']
            );

            if (!empty($user->suspendedenrolment)) {
                $usercell->attributes['class'] .= ' usersuspended';

                //may be lots of suspended users so only get the string once
                if (empty($suspendedstring)) {
                    $suspendedstring = get_string('userenrolmentsuspended', 'grades');
                }
                $icon = $OUTPUT->pix_icon('i/enrolmentsuspended', $suspendedstring);
                $usercell->text .= html_writer::tag('span', $icon, array('class'=>'usersuspendedicon'));
            }
            // The browser's scrollbar may partly cover (in certain operative systems) the content in the user cells
            // when horizontally scrolling through the table contents (most noticeable when in RTL mode).
            // Therefore, add slight padding on the left or right when using RTL mode.
            $usercell->attributes['class'] .= ' ps-3';
            $usercell->text .= $this->gtree->get_cell_action_menu(['userid' => $userid], 'user', $this->gpr);

            $userrow->cells[] = $usercell;

            foreach ($extrafields as $field) {
                $fieldcellcontent = s($user->$field);
                if ($field === 'country') {
                    $countries = get_string_manager()->get_list_of_countries();
                    $fieldcellcontent = $countries[$user->$field] ?? $fieldcellcontent;
                }

                $fieldcell = new html_table_cell();
                $fieldcell->attributes['class'] = 'userfield user' . $field;
                $fieldcell->attributes['data-col'] = $field;
                $fieldcell->header = false;
                $fieldcell->text = html_writer::tag('div', $fieldcellcontent, [
                    'data-collapse' => 'content'
                ]);

                $userrow->cells[] = $fieldcell;
            }

            $userrow->attributes['data-uid'] = $userid;
            $rows[] = $userrow;
        }

        $rows = $this->get_left_range_row($rows, $colspan);
        if ($displayaverages) {
            $rows = $this->get_left_avg_row($rows, $colspan, true);
            $rows = $this->get_left_avg_row($rows, $colspan);
        }

        return $rows;
    }

    /**
     * Builds and returns the rows that will make up the right part of the grader report
     * @param boolean $displayaverages whether to display average rows in the table
     * @return array Array of html_table_row objects
     */
    public function get_right_rows(bool $displayaverages): array {
        global $CFG, $USER, $OUTPUT, $DB, $PAGE;

        $rows = [];
        $this->rowcount = 0;
        $strgrade = get_string('gradenoun');
        $this->get_sort_arrows();

        // Get preferences once.
        $quickgrading = $this->get_pref('quickgrading');

        // Get strings which are re-used inside the loop.
        $strftimedatetimeshort = get_string('strftimedatetimeshort');
        $strerror = get_string('error');
        $stroverridengrade = get_string('overridden', 'grades');
        $strfail = get_string('fail', 'grades');
        $strpass = get_string('pass', 'grades');
        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);

        // Preload scale objects for items with a scaleid and initialize tab indices.
        $scaleslist = [];

        foreach ($this->gtree->get_items() as $itemid => $item) {
            if (!empty($item->scaleid)) {
                $scaleslist[] = $item->scaleid;
            }
        }

        $cache = \cache::make_from_params(\cache_store::MODE_REQUEST, 'gradereport_grader', 'scales');
        $scalesarray = $cache->get(get_class($this));
        if (!$scalesarray) {
            $scalesarray = $DB->get_records_list('scale', 'id', $scaleslist);
            // Save to cache.
            $cache->set(get_class($this), $scalesarray);
        }

        foreach ($this->gtree->get_levels() as $row) {
            $headingrow = new html_table_row();
            $headingrow->attributes['class'] = 'heading_name_row';

            foreach ($row as $element) {
                $sortlink = clone($this->baseurl);
                if (isset($element['object']->id)) {
                    $sortlink->param('sortitemid', $element['object']->id);
                }

                $type   = $element['type'];

                if (!empty($element['colspan'])) {
                    $colspan = $element['colspan'];
                } else {
                    $colspan = 1;
                }

                if (!empty($element['depth'])) {
                    $catlevel = 'catlevel'.$element['depth'];
                } else {
                    $catlevel = '';
                }

                // Element is a filler.
                if ($type == 'filler' || $type == 'fillerfirst' || $type == 'fillerlast') {
                    $fillercell = new html_table_cell();
                    $fillercell->attributes['class'] = $type . ' ' . $catlevel;
                    $fillercell->colspan = $colspan;
                    $fillercell->text = '&nbsp;';

                    // This is a filler cell; don't use a <th>, it'll confuse screen readers.
                    $fillercell->header = false;
                    $headingrow->cells[] = $fillercell;
                } else if ($type == 'category') {
                    // Make sure the grade category has a grade total or at least has child grade items.
                    if (grade_tree::can_output_item($element)) {
                        // Element is a category.
                        $categorycell = new html_table_cell();
                        $categorycell->attributes['class'] = 'category ' . $catlevel;
                        $categorycell->colspan = $colspan;
                        $categorycell->header = true;
                        $categorycell->scope = 'col';

                        $statusicons = $this->gtree->set_grade_status_icons($element);
                        if ($statusicons) {
                            $categorycell->attributes['class'] .= ' statusicons';
                        }

                        $context = new stdClass();
                        $context->courseheader = $this->get_course_header($element);
                        $context->actionmenu = $this->gtree->get_cell_action_menu($element, 'gradeitem', $this->gpr);
                        $context->statusicons = $statusicons;
                        $categorycell->text = $OUTPUT->render_from_template('gradereport_grader/categorycell', $context);

                        $headingrow->cells[] = $categorycell;
                    }
                } else {
                    // Element is a grade_item.

                    $arrow = '';
                    if ($element['object']->id == $this->sortitemid) {
                        if ($this->sortorder == 'ASC') {
                            $arrow = $this->get_sort_arrow('down', $sortlink);
                        } else {
                            $arrow = $this->get_sort_arrow('up', $sortlink);
                        }
                    }

                    $collapsecontext = [
                        'field' => $element['object']->id,
                        'name' => $element['object']->get_name(),
                    ];
                    $collapsedicon = '';
                    // We do not want grade category total items to be hidden away as it is controlled by something else.
                    if (!$element['object']->is_aggregate_item()) {
                        $collapsedicon = $OUTPUT->render_from_template('gradereport_grader/collapse/icon', $collapsecontext);
                    }
                    $headerlink = grade_helper::get_element_header($element, true,
                        true, false, false, true);

                    $itemcell = new html_table_cell();
                    $itemcell->attributes['class'] = $type . ' ' . $catlevel .
                        ' highlightable'. ' i'. $element['object']->id;
                    $itemcell->attributes['data-itemid'] = $element['object']->id;

                    $singleview = $this->gtree->get_cell_action_menu($element, 'gradeitem', $this->gpr, $this->baseurl);
                    $statusicons = $this->gtree->set_grade_status_icons($element);
                    if ($statusicons) {
                        $itemcell->attributes['class'] .= ' statusicons';
                    }

                    $itemcell->attributes['class'] .= $this->get_cell_display_class($element['object']);

                    $itemcell->colspan = $colspan;
                    $itemcell->header = true;
                    $itemcell->scope = 'col';

                    $context = new stdClass();
                    $context->headerlink = $headerlink;
                    $context->arrow = $arrow;
                    $context->singleview = $singleview;
                    $context->statusicons = $statusicons;
                    $context->collapsedicon = $collapsedicon;

                    $itemcell->text = $OUTPUT->render_from_template('gradereport_grader/headercell', $context);

                    $headingrow->cells[] = $itemcell;
                }
            }
            $rows[] = $headingrow;
        }

        // Get all the grade items if the user can not view hidden grade items.
        // It is possible that the user is simply viewing the 'Course total' by switching to the 'Aggregates only' view
        // and that this user does not have the ability to view hidden items. In this case we still need to pass all the
        // grade items (in case one has been hidden) as the course total shown needs to be adjusted for this particular
        // user.
        if (!$this->canviewhidden) {
            $allgradeitems = $this->get_allgradeitems();
        }

        foreach ($this->users as $userid => $user) {

            if ($this->canviewhidden) {
                $altered = [];
                $unknown = [];
            } else {
                $usergrades = $this->allgrades[$userid];
                $hidingaffected = grade_grade::get_hiding_affected($usergrades, $allgradeitems);
                $altered = $hidingaffected['altered'];
                $unknown = $hidingaffected['unknowngrades'];
                unset($hidingaffected);
            }

            $itemrow = new html_table_row();
            $itemrow->id = 'user_'.$userid;

            $fullname = fullname($user, $viewfullnames);

            foreach ($this->gtree->items as $itemid => $unused) {
                $item =& $this->gtree->items[$itemid];
                $grade = $this->grades[$userid][$item->id];

                $itemcell = new html_table_cell();

                $itemcell->id = 'u' . $userid . 'i' . $itemid;
                $itemcell->attributes['data-itemid'] = $itemid;
                $itemcell->attributes['class'] = 'gradecell';

                // Get the decimal points preference for this item.
                $decimalpoints = $item->get_decimals();

                if (array_key_exists($itemid, $unknown)) {
                    $gradeval = null;
                } else if (array_key_exists($itemid, $altered)) {
                    $gradeval = $altered[$itemid];
                } else {
                    $gradeval = $grade->finalgrade;
                }

                $context = new stdClass();

                // MDL-11274: Hide grades in the grader report if the current grader
                // doesn't have 'moodle/grade:viewhidden'.
                if (!$this->canviewhidden && $grade->is_hidden()) {
                    if (!empty($CFG->grade_hiddenasdate) && $grade->get_datesubmitted()
                            && !$item->is_category_item() && !$item->is_course_item()) {
                        // The problem here is that we do not have the time when grade value was modified,
                        // 'timemodified' is general modification date for grade_grades records.
                        $context->text = userdate($grade->get_datesubmitted(), $strftimedatetimeshort);
                        $context->extraclasses = 'datesubmitted';
                    } else {
                        $context->text = '-';
                    }
                    $itemcell->text = $OUTPUT->render_from_template('gradereport_grader/cell', $context);
                    $itemrow->cells[] = $itemcell;
                    continue;
                }

                // Emulate grade element.
                $eid = $this->gtree->get_grade_eid($grade);
                $element = ['eid' => $eid, 'object' => $grade, 'type' => 'grade'];

                $itemcell->attributes['class'] .= ' grade i' . $itemid;
                if ($item->is_category_item()) {
                    $itemcell->attributes['class'] .= ' cat';
                }
                if ($item->is_course_item()) {
                    $itemcell->attributes['class'] .= ' course';
                }
                if ($grade->is_overridden()) {
                    $itemcell->attributes['class'] .= ' overridden';
                    $itemcell->attributes['aria-label'] = $stroverridengrade;
                }

                $hidden = '';
                if ($grade->is_hidden()) {
                    $hidden = ' dimmed_text ';
                }
                $gradepass = ' gradefail ';
                $context->gradepassicon = $OUTPUT->pix_icon('i/invalid', $strfail);
                if ($grade->is_passed($item)) {
                    $gradepass = ' gradepass ';
                    $context->gradepassicon = $OUTPUT->pix_icon('i/valid', $strpass);
                } else if (is_null($grade->is_passed($item))) {
                    $gradepass = '';
                    $context->gradepassicon = '';
                }
                $context->statusicons = $this->gtree->set_grade_status_icons($element);

                // If in editing mode, we need to print either a text box or a drop down (for scales)
                // grades in item of type grade category or course are not directly editable.
                if ($item->needsupdate) {
                    $context->text = $strerror;
                    $context->extraclasses = 'gradingerror';
                } else if (!empty($USER->editing)) {
                    if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                        $itemcell->attributes['class'] .= ' grade_type_scale';
                    } else if ($item->gradetype == GRADE_TYPE_VALUE) {
                        $itemcell->attributes['class'] .= ' grade_type_value';
                    } else if ($item->gradetype == GRADE_TYPE_TEXT) {
                        $itemcell->attributes['class'] .= ' grade_type_text';
                    }

                    if ($grade->is_locked()) {
                        $itemcell->attributes['class'] .= ' locked';
                    }

                    if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                        $context->scale = true;

                        $scale = $scalesarray[$item->scaleid];
                        $gradeval = (int)$gradeval; // Scales use only integers.
                        $scales = explode(",", $scale->scale);
                        // Reindex because scale is off 1.

                        // MDL-12104 some previous scales might have taken up part of the array
                        // so this needs to be reset.
                        $scaleopt = [];
                        $i = 0;
                        foreach ($scales as $scaleoption) {
                            $i++;
                            $scaleopt[$i] = $scaleoption;
                        }

                        if ($quickgrading && $grade->is_editable()) {
                            $context->iseditable = true;
                            if (empty($item->outcomeid)) {
                                $nogradestr = get_string('nograde');
                            } else {
                                $nogradestr = get_string('nooutcome', 'grades');
                            }
                            $attributes = [
                                'id' => 'grade_' . $userid . '_' . $item->id
                            ];
                            $gradelabel = $fullname . ' ' . $item->get_name(true);

                            if ($context->statusicons) {
                                $attributes['class'] = 'statusicons';
                            }

                            $context->label = html_writer::label(
                                get_string('useractivitygrade', 'gradereport_grader', $gradelabel),
                                $attributes['id'], false, ['class' => 'accesshide']);
                            $context->select = html_writer::select($scaleopt, 'grade['.$userid.']['.$item->id.']',
                                $gradeval, [-1 => $nogradestr], $attributes);
                        } else if (!empty($scale)) {
                            $scales = explode(",", $scale->scale);

                            $context->extraclasses = 'gradevalue' . $hidden . $gradepass;
                            // Invalid grade if gradeval < 1.
                            if ($gradeval < 1) {
                                $context->text = '-';
                            } else {
                                // Just in case somebody changes scale.
                                $gradeval = $grade->grade_item->bounded_grade($gradeval);
                                $context->text = $scales[$gradeval - 1];
                            }
                        }

                    } else if ($item->gradetype != GRADE_TYPE_TEXT) {
                        // Value type.
                        if ($quickgrading and $grade->is_editable()) {
                            $context->iseditable = true;

                            // Set this input field with type="number" if the decimal separator for current language is set to
                            // a period. Other decimal separators may not be recognised by browsers yet which may cause issues
                            // when entering grades.
                            $decsep = get_string('decsep', 'core_langconfig');
                            $context->isnumeric = $decsep === '.';
                            // If we're rendering this as a number field, set min/max attributes, if applicable.
                            if ($context->isnumeric) {
                                $context->minvalue = $item->grademin ?? null;
                                if (empty($CFG->unlimitedgrades)) {
                                    $context->maxvalue = $item->grademax ?? null;
                                }
                            }

                            $value = format_float($gradeval, $decimalpoints);
                            $gradelabel = $fullname . ' ' . $item->get_name(true);

                            $context->id = 'grade_' . $userid . '_' . $item->id;
                            $context->name = 'grade[' . $userid . '][' . $item->id .']';
                            $context->value = $value;
                            $context->label = get_string('useractivitygrade', 'gradereport_grader', $gradelabel);
                            $context->title = $strgrade;
                            $context->extraclasses = 'form-control';
                            if ($context->statusicons) {
                                $context->extraclasses .= ' statusicons';
                            }
                        } else {
                            $context->extraclasses = 'gradevalue' . $hidden . $gradepass;
                            $context->text = format_float($gradeval, $decimalpoints);
                        }
                    }

                } else {
                    // Not editing.
                    $gradedisplaytype = $item->get_displaytype();

                    // Letter grades, scales and text grades are left aligned.
                    $textgrade = false;
                    $textgrades = [GRADE_DISPLAY_TYPE_LETTER,
                        GRADE_DISPLAY_TYPE_REAL_LETTER,
                        GRADE_DISPLAY_TYPE_LETTER_REAL,
                        GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE,
                        GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER];
                    if (in_array($gradedisplaytype, $textgrades)) {
                        $textgrade = true;
                    }

                    if ($textgrade || ($item->gradetype == GRADE_TYPE_TEXT)) {
                        $itemcell->attributes['class'] .= ' grade_type_text';
                    } else if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                        if ($gradedisplaytype == GRADE_DISPLAY_TYPE_PERCENTAGE) {
                            $itemcell->attributes['class'] .= ' grade_type_value';
                        } else {
                            $itemcell->attributes['class'] .= ' grade_type_scale';
                        }
                    } else {
                        $itemcell->attributes['class'] .= ' grade_type_value';
                    }

                    if ($item->needsupdate) {
                        $context->text = $strerror;
                        $context->extraclasses = 'gradingerror' . $hidden . $gradepass;
                    } else {
                        // The max and min for an aggregation may be different to the grade_item.
                        if (!is_null($gradeval)) {
                            $item->grademax = $grade->get_grade_max();
                            $item->grademin = $grade->get_grade_min();
                        }

                        $context->extraclasses = 'gradevalue ' . $hidden . $gradepass;
                        $context->text = grade_format_gradevalue($gradeval, $item, true,
                            $gradedisplaytype, null);
                        $context->text .= penalty_manager::show_penalty_indicator($grade);
                    }
                }

                if ($item->gradetype == GRADE_TYPE_TEXT && !empty($grade->feedback)) {
                    $context->text = html_writer::span(shorten_text(strip_tags($grade->feedback), 20), '',
                        ['data-action' => 'feedback', 'role' => 'button', 'data-courseid' => $this->courseid]);
                }

                if (!$item->needsupdate && !($item->gradetype == GRADE_TYPE_TEXT && empty($USER->editing))) {
                    $context->actionmenu = $this->gtree->get_cell_action_menu($element, 'gradeitem', $this->gpr);
                }

                $itemcell->text = $OUTPUT->render_from_template('gradereport_grader/cell', $context);

                if (!empty($this->gradeserror[$item->id][$userid])) {
                    $itemcell->text .= $this->gradeserror[$item->id][$userid];
                }

                $itemrow->cells[] = $itemcell;
            }
            $rows[] = $itemrow;
        }

        if (!empty($USER->editing)) {
            $PAGE->requires->js_call_amd('core_form/changechecker',
                'watchFormById', ['gradereport_grader']);
        }

        $rows = $this->get_right_range_row($rows);
        if ($displayaverages && $this->canviewhidden) {
            $showonlyactiveenrol = $this->show_only_active();

            if ($this->currentgroup) {
                $ungradedcounts = $this->ungraded_counts(true, true, $showonlyactiveenrol);
                $rows[] = $this->format_averages($ungradedcounts);
            }

            $ungradedcounts = $this->ungraded_counts(false, true, $showonlyactiveenrol);
            $rows[] = $this->format_averages($ungradedcounts);
        }

        return $rows;
    }

    /**
     * Returns a row of grade items averages
     *
     * @param grade_item $gradeitem Grade item.
     * @param array|null $aggr Average value and meancount information.
     * @param bool|null $shownumberofgrades Whether to show number of grades.
     * @return html_table_cell Formatted average cell.
     */
    protected function format_average_cell(grade_item $gradeitem, ?array $aggr = null, ?bool $shownumberofgrades = null): html_table_cell {
        global $OUTPUT;

        if ($gradeitem->needsupdate) {
            $avgcell = new html_table_cell();
            $avgcell->attributes['class'] = 'i' . $gradeitem->id;
            $avgcell->text = $OUTPUT->container(get_string('error'), 'gradingerror');
        } else {
            $gradetypeclass = '';
            if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                $gradetypeclass = ' grade_type_scale';
            } else if ($gradeitem->gradetype == GRADE_TYPE_VALUE) {
                $gradetypeclass = ' grade_type_value';
            } else if ($gradeitem->gradetype == GRADE_TYPE_TEXT) {
                $gradetypeclass = ' grade_type_text';
            }

            if (empty($aggr['average'])) {
                $avgcell = new html_table_cell();
                $avgcell->attributes['class'] = $gradetypeclass . ' i' . $gradeitem->id;
                $avgcell->attributes['data-itemid'] = $gradeitem->id;
                $avgcell->text = html_writer::div('-', '', ['data-collapse' => 'avgrowcell']);
            } else {
                $numberofgrades = '';
                if ($shownumberofgrades) {
                    $numberofgrades = " (" . $aggr['meancount'] . ")";
                }

                $avgcell = new html_table_cell();
                $avgcell->attributes['class'] = $gradetypeclass . ' i' . $gradeitem->id;
                $avgcell->attributes['data-itemid'] = $gradeitem->id;
                $avgcell->text = html_writer::div($aggr['average'] . $numberofgrades, '', ['data-collapse' => 'avgrowcell']);
            }
        }
        return $avgcell;
    }

    /**
     * Depending on the style of report (fixedstudents vs traditional one-table),
     * arranges the rows of data in one or two tables, and returns the output of
     * these tables in HTML
     * @param boolean $displayaverages whether to display average rows in the table
     * @return string HTML
     */
    public function get_grade_table($displayaverages = false) {
        global $OUTPUT;
        $leftrows = $this->get_left_rows($displayaverages);
        $rightrows = $this->get_right_rows($displayaverages);

        $html = '';

        $fulltable = new html_table();
        $fulltable->attributes['class'] = 'gradereport-grader-table d-none';
        $fulltable->id = 'user-grades';
        $fulltable->caption = get_string('summarygrader', 'gradereport_grader');
        $fulltable->captionhide = true;
        // We don't want the table to be enclosed within in a .table-responsive div as it is heavily customised.
        $fulltable->responsive = false;

        // Extract rows from each side (left and right) and collate them into one row each
        foreach ($leftrows as $key => $row) {
            $row->cells = array_merge($row->cells, $rightrows[$key]->cells);
            $fulltable->data[] = $row;
            unset($leftrows[$key]);
            unset($rightrows[$key]);
        }
        $html .= html_writer::table($fulltable);
        return $OUTPUT->container($html, 'gradeparent');
    }

    /**
     * Builds and return the row of icons for the left side of the report.
     * It only has one cell that says "Controls"
     * @param array $rows The Array of rows for the left part of the report
     * @param int $colspan The number of columns this cell has to span
     * @return array Array of rows for the left part of the report
     * @deprecated since Moodle 4.2 - The row is not shown anymore - we have actions menu.
     * @todo MDL-77307 This will be deleted in Moodle 4.6.
     */
    public function get_left_icons_row($rows=array(), $colspan=1) {
        global $USER;

        debugging('The function get_left_icons_row() is deprecated, please do not use it anymore.',
            DEBUG_DEVELOPER);

        if (!empty($USER->editing)) {
            $controlsrow = new html_table_row();
            $controlsrow->attributes['class'] = 'controls';
            $controlscell = new html_table_cell();
            $controlscell->attributes['class'] = 'header controls';
            $controlscell->header = true;
            $controlscell->colspan = $colspan;
            $controlscell->text = get_string('controls', 'grades');
            $controlsrow->cells[] = $controlscell;

            $rows[] = $controlsrow;
        }
        return $rows;
    }

    /**
     * Builds and return the header for the row of ranges, for the left part of the grader report.
     * @param array $rows The Array of rows for the left part of the report
     * @param int $colspan The number of columns this cell has to span
     * @return array Array of rows for the left part of the report
     */
    public function get_left_range_row($rows=array(), $colspan=1) {
        global $CFG, $USER;

        if ($this->get_pref('showranges')) {
            $rangerow = new html_table_row();
            $rangerow->attributes['class'] = 'range r'.$this->rowcount++;
            $rangecell = new html_table_cell();
            $rangecell->attributes['class'] = 'header range';
            $rangecell->colspan = $colspan;
            $rangecell->header = true;
            $rangecell->scope = 'row';
            $rangecell->text = get_string('range', 'grades');
            $rangerow->cells[] = $rangecell;
            $rows[] = $rangerow;
        }

        return $rows;
    }

    /**
     * Builds and return the headers for the rows of averages, for the left part of the grader report.
     * @param array $rows The Array of rows for the left part of the report
     * @param int $colspan The number of columns this cell has to span
     * @param bool $groupavg If true, returns the row for group averages, otherwise for overall averages
     * @return array Array of rows for the left part of the report
     */
    public function get_left_avg_row($rows=array(), $colspan=1, $groupavg=false) {
        if (!$this->canviewhidden) {
            // totals might be affected by hiding, if user can not see hidden grades the aggregations might be altered
            // better not show them at all if user can not see all hideen grades
            return $rows;
        }

        $showaverages = $this->get_pref('showaverages');
        $showaveragesgroup = $this->currentgroup && $showaverages;
        $straveragegroup = get_string('groupavg', 'grades');

        if ($groupavg) {
            if ($showaveragesgroup) {
                $groupavgrow = new html_table_row();
                $groupavgrow->attributes['class'] = 'groupavg r'.$this->rowcount++;
                $groupavgcell = new html_table_cell();
                $groupavgcell->attributes['class'] = 'header range';
                $groupavgcell->colspan = $colspan;
                $groupavgcell->header = true;
                $groupavgcell->scope = 'row';
                $groupavgcell->text = $straveragegroup;
                $groupavgrow->cells[] = $groupavgcell;
                $rows[] = $groupavgrow;
            }
        } else {
            $straverage = get_string('overallaverage', 'grades');

            if ($showaverages) {
                $avgrow = new html_table_row();
                $avgrow->attributes['class'] = 'avg r'.$this->rowcount++;
                $avgcell = new html_table_cell();
                $avgcell->attributes['class'] = 'header range';
                $avgcell->colspan = $colspan;
                $avgcell->header = true;
                $avgcell->scope = 'row';
                $avgcell->text = $straverage;
                $avgrow->cells[] = $avgcell;
                $rows[] = $avgrow;
            }
        }

        return $rows;
    }

    /**
     * Builds and return the row of icons when editing is on, for the right part of the grader report.
     * @param array $rows The Array of rows for the right part of the report
     * @return array Array of rows for the right part of the report
     * @deprecated since Moodle 4.2 - The row is not shown anymore - we have actions menu.
     * @todo MDL-77307 This will be deleted in Moodle 4.6.
     */
    public function get_right_icons_row($rows=array()) {
        global $USER;
        debugging('The function get_right_icons_row() is deprecated, please do not use it anymore.',
            DEBUG_DEVELOPER);

        if (!empty($USER->editing)) {
            $iconsrow = new html_table_row();
            $iconsrow->attributes['class'] = 'controls';

            foreach ($this->gtree->items as $itemid => $unused) {
                // emulate grade element
                $item = $this->gtree->get_item($itemid);

                $eid = $this->gtree->get_item_eid($item);
                $element = $this->gtree->locate_element($eid);
                $itemcell = new html_table_cell();
                $itemcell->attributes['class'] = 'controls icons i'.$itemid;
                $itemcell->text = $this->get_icons($element);
                $iconsrow->cells[] = $itemcell;
            }
            $rows[] = $iconsrow;
        }
        return $rows;
    }

    /**
     * Builds and return the row of ranges for the right part of the grader report.
     * @param array $rows The Array of rows for the right part of the report
     * @return array Array of rows for the right part of the report
     */
    public function get_right_range_row($rows=array()) {

        if ($this->get_pref('showranges')) {
            $rangesdisplaytype   = $this->get_pref('rangesdisplaytype');
            $rangesdecimalpoints = $this->get_pref('rangesdecimalpoints');
            $rangerow = new html_table_row();
            $rangerow->attributes['class'] = 'heading range';

            foreach ($this->gtree->items as $itemid => $unused) {
                $item =& $this->gtree->items[$itemid];
                $itemcell = new html_table_cell();
                $itemcell->attributes['class'] .= ' range i'. $itemid;
                $itemcell->attributes['class'] .= $this->get_cell_display_class($item);

                $hidden = '';
                if ($item->is_hidden()) {
                    $hidden = ' dimmed_text ';
                }

                $formattedrange = $item->get_formatted_range($rangesdisplaytype, $rangesdecimalpoints);

                $itemcell->attributes['data-itemid'] = $itemid;
                $itemcell->text = html_writer::div($formattedrange, 'rangevalues' . $hidden,
                    ['data-collapse' => 'rangerowcell']);
                $rangerow->cells[] = $itemcell;
            }
            $rows[] = $rangerow;
        }
        return $rows;
    }

    /**
     * @deprecated since Moodle 4.4 - Call calculate_average instead.
     */
    #[\core\attribute\deprecated('grade_report::calculate_average()', since: '4.4', final: true)]
    public function get_right_avg_row() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * Given element category, create a collapsible icon and
     * course header.
     *
     * @param array $element
     * @return string HTML
     */
    protected function get_course_header($element) {
        if (in_array($element['object']->id, $this->collapsed['aggregatesonly'])) {
            $showing = get_string('showingaggregatesonly', 'grades');
        } else if (in_array($element['object']->id, $this->collapsed['gradesonly'])) {
            $showing = get_string('showinggradesonly', 'grades');
        } else {
            $showing = get_string('showingfullmode', 'grades');
        }

        $name = $element['object']->get_name();
        $nameunescaped = $element['object']->get_name(false);
        $describedbyid = uniqid();
        $courseheader = html_writer::tag('span', $name, [
            'title' => $nameunescaped,
            'class' => 'gradeitemheader',
            'aria-describedby' => $describedbyid
        ]);
        $courseheader .= html_writer::div($showing, 'visually-hidden', [
            'id' => $describedbyid
        ]);

        return $courseheader;
    }

    /**
     * Given a grade_category, grade_item or grade_grade, this function
     * figures out the state of the object and builds then returns a div
     * with the icons needed for the grader report.
     *
     * @param array $element
     * @return string HTML
     * @deprecated since Moodle 4.2 - The row is not shown anymore - we have actions menu.
     * @todo MDL-77307 This will be deleted in Moodle 4.6.
     */
    protected function get_icons($element) {
        global $CFG, $USER, $OUTPUT;
        debugging('The function get_icons() is deprecated, please do not use it anymore.',
            DEBUG_DEVELOPER);

        if (empty($USER->editing)) {
            return '<div class="grade_icons" />';
        }

        // Init all icons
        $editicon = '';

        $editable = true;

        if ($element['type'] == 'grade') {
            $item = $element['object']->grade_item;
            if ($item->is_course_item() or $item->is_category_item()) {
                $editable = $this->overridecat;
            }
        }

        if ($element['type'] != 'categoryitem' && $element['type'] != 'courseitem' && $editable) {
            $editicon = $this->gtree->get_edit_icon($element, $this->gpr);
        }

        $editcalculationicon = '';
        $showhideicon        = '';
        $lockunlockicon      = '';

        if (has_capability('moodle/grade:manage', $this->context)) {
            $editcalculationicon = $this->gtree->get_calculation_icon($element, $this->gpr);

            $showhideicon = $this->gtree->get_hiding_icon($element, $this->gpr);

            $lockunlockicon = $this->gtree->get_locking_icon($element, $this->gpr);
        }

        $gradeanalysisicon = '';
        if ($element['type'] == 'grade') {
            $gradeanalysisicon .= $this->gtree->get_grade_analysis_icon($element['object']);
        }

        return $OUTPUT->container($editicon.$editcalculationicon.$showhideicon.$lockunlockicon.$gradeanalysisicon, 'grade_icons');
    }

    /**
     * Processes a single action against a category, grade_item or grade.
     * @param string $target eid ({type}{id}, e.g. c4 for category4)
     * @param string $action Which action to take (edit, delete etc...)
     * @return
     */
    public function process_action($target, $action) {
        return self::do_process_action($target, $action, $this->course->id);
    }

    /**
     * From the list of categories that this user prefers to collapse choose ones that belong to the current course.
     *
     * This function serves two purposes.
     * Mainly it helps migrating from user preference style when all courses were stored in one preference.
     * Also it helps to remove the settings for categories that were removed if the array for one course grows too big.
     *
     * @param int $courseid
     * @param array $collapsed
     * @return array
     */
    protected static function filter_collapsed_categories($courseid, $collapsed) {
        global $DB;
        // Ensure we always have an element for aggregatesonly and another for gradesonly, no matter it's empty.
        $collapsed['aggregatesonly'] = $collapsed['aggregatesonly'] ?? [];
        $collapsed['gradesonly'] = $collapsed['gradesonly'] ?? [];

        if (empty($collapsed['aggregatesonly']) && empty($collapsed['gradesonly'])) {
            return $collapsed;
        }
        $cats = $DB->get_fieldset_select('grade_categories', 'id', 'courseid = ?', array($courseid));
        $collapsed['aggregatesonly'] = array_values(array_intersect($collapsed['aggregatesonly'], $cats));
        $collapsed['gradesonly'] = array_values(array_intersect($collapsed['gradesonly'], $cats));
        return $collapsed;
    }

    /**
     * Returns the list of categories that this user wants to collapse or display aggregatesonly
     *
     * This method also migrates on request from the old format of storing user preferences when they were stored
     * in one preference for all courses causing DB error when trying to insert very big value.
     *
     * @param int $courseid
     * @return array
     */
    protected static function get_collapsed_preferences($courseid) {
        if ($collapsed = get_user_preferences('grade_report_grader_collapsed_categories'.$courseid)) {
            $collapsed = json_decode($collapsed, true);
            // Ensure we always have an element for aggregatesonly and another for gradesonly, no matter it's empty.
            $collapsed['aggregatesonly'] = $collapsed['aggregatesonly'] ?? [];
            $collapsed['gradesonly'] = $collapsed['gradesonly'] ?? [];
            return $collapsed;
        }

        // Try looking for old location of user setting that used to store all courses in one serialized user preference.
        $collapsed = ['aggregatesonly' => [], 'gradesonly' => []]; // Use this if old settings are not found.
        $collapsedall = [];
        $oldprefexists = false;
        if (($oldcollapsedpref = get_user_preferences('grade_report_grader_collapsed_categories')) !== null) {
            $oldprefexists = true;
            if ($collapsedall = unserialize_array($oldcollapsedpref)) {
                // Ensure we always have an element for aggregatesonly and another for gradesonly, no matter it's empty.
                $collapsedall['aggregatesonly'] = $collapsedall['aggregatesonly'] ?? [];
                $collapsedall['gradesonly'] = $collapsedall['gradesonly'] ?? [];
                // We found the old-style preference, filter out only categories that belong to this course and update the prefs.
                $collapsed = static::filter_collapsed_categories($courseid, $collapsedall);
                if (!empty($collapsed['aggregatesonly']) || !empty($collapsed['gradesonly'])) {
                    static::set_collapsed_preferences($courseid, $collapsed);
                    $collapsedall['aggregatesonly'] = array_diff($collapsedall['aggregatesonly'], $collapsed['aggregatesonly']);
                    $collapsedall['gradesonly'] = array_diff($collapsedall['gradesonly'], $collapsed['gradesonly']);
                    if (!empty($collapsedall['aggregatesonly']) || !empty($collapsedall['gradesonly'])) {
                        set_user_preference('grade_report_grader_collapsed_categories', serialize($collapsedall));
                    }
                }
            }
        }

        // Arrived here, if the old pref exists and it doesn't contain
        // more information, it means that the migration of all the
        // data to new, by course, preferences is completed, so
        // the old one can be safely deleted.
        if ($oldprefexists &&
                empty($collapsedall['aggregatesonly']) &&
                empty($collapsedall['gradesonly'])) {
            unset_user_preference('grade_report_grader_collapsed_categories');
        }

        return $collapsed;
    }

    /**
     * Sets the list of categories that user wants to see collapsed in user preferences
     *
     * This method may filter or even trim the list if it does not fit in DB field.
     *
     * @param int $courseid
     * @param array $collapsed
     */
    protected static function set_collapsed_preferences($courseid, $collapsed) {
        global $DB;
        // In an unlikely case that the list of collapsed categories for one course is too big for the user preference size,
        // try to filter the list of categories since array may contain categories that were deleted.
        if (strlen(json_encode($collapsed)) >= 1333) {
            $collapsed = static::filter_collapsed_categories($courseid, $collapsed);
        }

        // If this did not help, "forget" about some of the collapsed categories. Still better than to loose all information.
        while (strlen(json_encode($collapsed)) >= 1333) {
            if (count($collapsed['aggregatesonly'])) {
                array_pop($collapsed['aggregatesonly']);
            }
            if (count($collapsed['gradesonly'])) {
                array_pop($collapsed['gradesonly']);
            }
        }

        if (!empty($collapsed['aggregatesonly']) || !empty($collapsed['gradesonly'])) {
            set_user_preference('grade_report_grader_collapsed_categories'.$courseid, json_encode($collapsed));
        } else {
            unset_user_preference('grade_report_grader_collapsed_categories'.$courseid);
        }
    }

    /**
     * Processes a single action against a category, grade_item or grade.
     * @param string $target eid ({type}{id}, e.g. c4 for category4)
     * @param string $action Which action to take (edit, delete etc...)
     * @param int $courseid affected course.
     * @return
     */
    public static function do_process_action($target, $action, $courseid = null) {
        global $DB;
        // TODO: this code should be in some grade_tree static method
        $targettype = substr($target, 0, 2);
        $targetid = substr($target, 2);
        // TODO: end

        if ($targettype !== 'cg') {
            // The following code only works with categories.
            return true;
        }

        if (!$courseid) {
            debugging('Function grade_report_grader::do_process_action() now requires additional argument courseid',
                DEBUG_DEVELOPER);
            if (!$courseid = $DB->get_field('grade_categories', 'courseid', array('id' => $targetid), IGNORE_MISSING)) {
                return true;
            }
        }

        $collapsed = static::get_collapsed_preferences($courseid);

        switch ($action) {
            case 'switch_minus': // Add category to array of aggregatesonly
                $key = array_search($targetid, $collapsed['gradesonly']);
                if ($key !== false) {
                    unset($collapsed['gradesonly'][$key]);
                }
                if (!in_array($targetid, $collapsed['aggregatesonly'])) {
                    $collapsed['aggregatesonly'][] = $targetid;
                    static::set_collapsed_preferences($courseid, $collapsed);
                }
                break;

            case 'switch_plus': // Remove category from array of aggregatesonly, and add it to array of gradesonly
                $key = array_search($targetid, $collapsed['aggregatesonly']);
                if ($key !== false) {
                    unset($collapsed['aggregatesonly'][$key]);
                }
                if (!in_array($targetid, $collapsed['gradesonly'])) {
                    $collapsed['gradesonly'][] = $targetid;
                }
                static::set_collapsed_preferences($courseid, $collapsed);
                break;
            case 'switch_whole': // Remove the category from the array of collapsed cats
                $key = array_search($targetid, $collapsed['gradesonly']);
                if ($key !== false) {
                    unset($collapsed['gradesonly'][$key]);
                    static::set_collapsed_preferences($courseid, $collapsed);
                }

                $key = array_search($targetid, $collapsed['aggregatesonly']);
                if ($key !== false) {
                    unset($collapsed['aggregatesonly'][$key]);
                    static::set_collapsed_preferences($courseid, $collapsed);
                }
                break;
            default:
                break;
        }

        return true;
    }

    /**
     * Refactored function for generating HTML of sorting links with matching arrows.
     * Returns an array with 'studentname' and 'idnumber' as keys, with HTML ready
     * to inject into a table header cell.
     * @param array $extrafields Array of extra fields being displayed, such as
     *   user idnumber
     * @return array An associative array of HTML sorting links+arrows
     */
    public function get_sort_arrows(array $extrafields = []) {
        global $CFG;
        $arrows = array();
        $sortlink = clone($this->baseurl);

        // Sourced from tablelib.php
        // Check the full name display for sortable fields.
        if (has_capability('moodle/site:viewfullnames', $this->context)) {
            $nameformat = $CFG->alternativefullnameformat;
        } else {
            $nameformat = $CFG->fullnamedisplay;
        }

        if ($nameformat == 'language') {
            $nameformat = get_string('fullnamedisplay');
        }

        $arrows['studentname'] = '';
        $requirednames = order_in_string(\core_user\fields::get_name_fields(), $nameformat);
        if (!empty($requirednames)) {
            foreach ($requirednames as $name) {
                $arrows['studentname'] .= get_string($name);
                if ($this->sortitemid == $name) {
                    $sortlink->param('sortitemid', $name);
                    if ($this->sortorder == 'ASC') {
                        $sorticon = $this->get_sort_arrow('down', $sortlink);
                    } else {
                        $sorticon = $this->get_sort_arrow('up', $sortlink);
                    }
                    $arrows['studentname'] .= $sorticon;
                }
                $arrows['studentname'] .= ' / ';
            }

            $arrows['studentname'] = substr($arrows['studentname'], 0, -3);
        }

        foreach ($extrafields as $field) {
            $attributes = [
                'data-collapse' => 'content'
            ];
            // With additional user profile fields, we can't grab the name via WS, so conditionally add it to rip out of the DOM.
            if (preg_match(\core_user\fields::PROFILE_FIELD_REGEX, $field)) {
                $attributes['data-collapse-name'] = \core_user\fields::get_display_name($field);
            }

            $arrows[$field] = html_writer::span(\core_user\fields::get_display_name($field), '', $attributes);
            if ($field == $this->sortitemid) {
                $sortlink->param('sortitemid', $field);

                if ($this->sortorder == 'ASC') {
                    $sorticon = $this->get_sort_arrow('down', $sortlink);
                } else {
                    $sorticon = $this->get_sort_arrow('up', $sortlink);
                }
                $arrows[$field] .= $sorticon;
            }
        }

        return $arrows;
    }

    /**
     * Returns the maximum number of students to be displayed on each page
     *
     * @return int The maximum number of students to display per page
     */
    public function get_students_per_page(): int {
        // Default to the lowest available option.
        return (int) get_user_preferences('grade_report_studentsperpage', min(static::PAGINATION_OPTIONS));
    }

    /**
     * Returns link to change category view mode.
     *
     * @param moodle_url $url Url to grader report page
     * @param string $title Menu item title
     * @param string $action View mode to change to
     * @param bool $active Whether link is active in dropdown
     *
     * @return string|null
     */
    public function get_category_view_mode_link(moodle_url $url, string $title, string $action, bool $active = false): ?string {
        $urlnew = $url;
        $urlnew->param('action', $action);
        $active = $active ? 'true' : 'false';
        return html_writer::link($urlnew, $title,
            ['class' => 'dropdown-item', 'aria-label' => $title, 'aria-current' => $active, 'role' => 'menuitem']);
    }

    /**
     * Return the link to allow the field to collapse from the users view.
     *
     * @return string Dropdown menu link that'll trigger the collapsing functionality.
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_hide_show_link(): string {
        $link = new moodle_url('#', []);
        return html_writer::link(
            $link->out(false),
            get_string('collapse'),
            ['class' => 'dropdown-item', 'data-hider' => 'hide', 'aria-label' => get_string('collapse'), 'role' => 'menuitem'],
        );
    }

    /**
     * Return the base report link with some default sorting applied.
     *
     * @return string
     * @throws moodle_exception
     */
    public function get_default_sortable(): string {
        $sortlink = new moodle_url('/grade/report/grader/index.php', [
            'id' => $this->courseid,
            'sortitemid' => 'firstname',
            'sort' => 'asc'
        ]);
        $this->gpr->add_url_params($sortlink);
        return $sortlink->out(false);
    }

    /**
     * Return class used for text alignment.
     *
     * @param grade_item $item Can be grade item or grade
     * @return string class name used for text alignment
     */
    public function get_cell_display_class(grade_item $item): string {
        global $USER;

        $gradetypeclass = '';
        if (!empty($USER->editing)) {
            switch ($item->gradetype) {
                case GRADE_TYPE_SCALE:
                    $gradetypeclass = ' grade_type_scale';
                    break;
                case GRADE_TYPE_VALUE:
                    $gradetypeclass = ' grade_type_value';
                    break;
                case GRADE_TYPE_TEXT:
                    $gradetypeclass = ' grade_type_text';
                    break;
            }
        } else {
            $gradedisplaytype = $item->get_displaytype();

            // Letter grades, scales and text grades are left aligned.
            $textgrade = false;
            $textgrades = [GRADE_DISPLAY_TYPE_LETTER,
                GRADE_DISPLAY_TYPE_REAL_LETTER,
                GRADE_DISPLAY_TYPE_LETTER_REAL,
                GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE,
                GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER];
            if (in_array($gradedisplaytype, $textgrades)) {
                $textgrade = true;
            }

            $cache = \cache::make_from_params(\cache_store::MODE_REQUEST, 'gradereport_grader', 'scales');
            $scalesarray = $cache->get(get_class($this));

            if ($textgrade || ($item->gradetype == GRADE_TYPE_TEXT)) {
                $gradetypeclass = ' grade_type_text';
            } else if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                if ($gradedisplaytype == GRADE_DISPLAY_TYPE_PERCENTAGE) {
                    $gradetypeclass = ' grade_type_value';
                } else {
                    $gradetypeclass = ' grade_type_scale';
                }
            } else {
                $gradetypeclass = ' grade_type_value';
            }
        }
        return $gradetypeclass;
    }
}

/**
 * Adds report specific context variable
 *
 * @param context_course $context Course context
 * @param int $courseid Course ID
 * @param array  $element An array representing an element in the grade_tree
 * @param grade_plugin_return $gpr A grade_plugin_return object
 * @param string $mode Not used
 * @param stdClass|null $templatecontext Template context
 * @return stdClass|null
 */
function gradereport_grader_get_report_link(context_course $context, int $courseid,
        array $element, grade_plugin_return $gpr, string $mode, ?stdClass $templatecontext): ?stdClass {

    static $report = null;
    if (!$report) {
        $report = new grade_report_grader($courseid, $gpr, $context);
    }

    if ($mode == 'category') {
        if (!isset($templatecontext)) {
            $templatecontext = new stdClass();
        }

        $categoryid = $element['object']->id;

        // Load language strings.
        $strswitchminus = get_string('aggregatesonly', 'grades');
        $strswitchplus = get_string('gradesonly', 'grades');
        $strswitchwhole = get_string('fullmode', 'grades');

        $url = new moodle_url($gpr->get_return_url(null, ['target' => $element['eid'], 'sesskey' => sesskey()]));

        $gradesonly = false;
        $aggregatesonly = false;
        $fullmode = false;
        if (in_array($categoryid, $report->collapsed['gradesonly'])) {
            $gradesonly = true;
        } else if (in_array($categoryid, $report->collapsed['aggregatesonly'])) {
            $aggregatesonly = true;
        } else {
            $fullmode = true;
        }
        $templatecontext->gradesonlyurl =
            $report->get_category_view_mode_link($url, $strswitchplus, 'switch_plus', $gradesonly);
        $templatecontext->aggregatesonlyurl =
            $report->get_category_view_mode_link($url, $strswitchminus, 'switch_minus', $aggregatesonly);
        $templatecontext->fullmodeurl =
            $report->get_category_view_mode_link($url, $strswitchwhole, 'switch_whole', $fullmode);
        return $templatecontext;
    } else if ($mode == 'gradeitem') {
        if (($element['type'] == 'userfield') && ($element['name'] !== 'fullname')) {
            $templatecontext->columncollapse = $report->get_hide_show_link();
            $templatecontext->dataid = $element['name'];
        }

        // We do not want grade category total items to be hidden away as it is controlled by something else.
        if (isset($element['object']->id) && !$element['object']->is_aggregate_item()) {
            $templatecontext->columncollapse = $report->get_hide_show_link();
        }
        return $templatecontext;
    }
    return null;
}
