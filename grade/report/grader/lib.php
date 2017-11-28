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
     * Length at which feedback will be truncated (to the nearest word) and an ellipsis be added.
     * TODO replace this by a report preference
     * @var int $feedback_trunc_length
     */
    protected $feedback_trunc_length = 50;

    /**
     * Allow category grade overriding
     * @var bool $overridecat
     */
    protected $overridecat;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $page The current page being viewed (when report is paged)
     * @param int $sortitemid The id of the grade_item by which to sort the table
     */
    public function __construct($courseid, $gpr, $context, $page=null, $sortitemid=null) {
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
        $this->setup_sortitemid();

        $this->overridecat = (bool)get_config('moodle', 'grade_overridecat');
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
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

            // skip, not a grade nor feedback
            if (strpos($varname, 'grade') === 0) {
                $datatype = 'grade';
            } else if (strpos($varname, 'feedback') === 0) {
                $datatype = 'feedback';
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

                    } else if ($datatype === 'feedback') {
                        // If quick grading is on, feedback needs to be compared without line breaks.
                        if ($this->get_pref('quickgrading')) {
                            $oldvalue->feedback = preg_replace("/\r\n|\r|\n/", "", $oldvalue->feedback);
                        }
                        if (($oldvalue->feedback === $postedvalue) or ($oldvalue->feedback === null and empty($postedvalue))) {
                            continue;
                        }
                    }

                    if (!$gradeitem = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$this->courseid))) {
                        print_error('invalidgradeitemid');
                    }

                    // Pre-process grade
                    if ($datatype == 'grade') {
                        $feedback = false;
                        $feedbackformat = false;
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
                            $userfields = 'id, ' . get_all_user_name_fields(true);
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

                    } else if ($datatype == 'feedback') {
                        $finalgrade = false;
                        $trimmed = trim($postedvalue);
                        if (empty($trimmed)) {
                             $feedback = null;
                        } else {
                             $feedback = $postedvalue;
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

                    $gradeitem->update_final_grade($userid, $finalgrade, 'gradebook', $feedback, FORMAT_MOODLE);

                    // We can update feedback without reloading the grade item as it doesn't affect grade calculations
                    if ($datatype === 'feedback') {
                        $this->grades[$userid][$itemid]->feedback = $feedback;
                    }
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
     */
    private function setup_sortitemid() {

        global $SESSION;

        if (!isset($SESSION->gradeuserreport)) {
            $SESSION->gradeuserreport = new stdClass();
        }

        if ($this->sortitemid) {
            if (!isset($SESSION->gradeuserreport->sort)) {
                if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                } else {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                }
            } else {
                // this is the first sort, i.e. by last name
                if (!isset($SESSION->gradeuserreport->sortitemid)) {
                    if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    }
                } else if ($SESSION->gradeuserreport->sortitemid == $this->sortitemid) {
                    // same as last sort
                    if ($SESSION->gradeuserreport->sort == 'ASC') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    }
                } else {
                    if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    }
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
    }

    /**
     * pulls out the userids of the users to be display, and sorts them
     */
    public function load_users() {
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
        $userfields = user_picture::fields('u', get_extra_user_fields($this->context));

        // We want to query both the current context and parent contexts.
        list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($this->context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');

        // If the user has clicked one of the sort asc/desc arrows.
        if (is_numeric($this->sortitemid)) {
            $params = array_merge(array('gitemid' => $this->sortitemid), $gradebookrolesparams, $this->userwheresql_params,
                    $this->groupwheresql_params, $enrolledparams, $relatedctxparams);

            $sortjoin = "LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = $this->sortitemid";
            $sort = "g.finalgrade $this->sortorder, u.idnumber, u.lastname, u.firstname, u.email";
        } else {
            $sortjoin = '';
            switch($this->sortitemid) {
                case 'lastname':
                    $sort = "u.lastname $this->sortorder, u.firstname $this->sortorder, u.idnumber, u.email";
                    break;
                case 'firstname':
                    $sort = "u.firstname $this->sortorder, u.lastname $this->sortorder, u.idnumber, u.email";
                    break;
                case 'email':
                    $sort = "u.email $this->sortorder, u.firstname, u.lastname, u.idnumber";
                    break;
                case 'idnumber':
                default:
                    $sort = "u.idnumber $this->sortorder, u.firstname, u.lastname, u.email";
                    break;
            }

            $params = array_merge($gradebookrolesparams, $this->userwheresql_params, $this->groupwheresql_params, $enrolledparams, $relatedctxparams);
        }

        $sql = "SELECT $userfields
                  FROM {user} u
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
        $studentsperpage = $this->get_students_per_page();
        $this->users = $DB->get_records_sql($sql, $params, $studentsperpage * $this->page, $studentsperpage);

        if (empty($this->users)) {
            $this->userselect = '';
            $this->users = array();
            $this->userselect_params = array();
        } else {
            list($usql, $uparams) = $DB->get_in_or_equal(array_keys($this->users), SQL_PARAMS_NAMED, 'usid0');
            $this->userselect = "AND g.userid $usql";
            $this->userselect_params = $uparams;

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
        $params = array_merge(array('courseid'=>$this->courseid), $this->userselect_params);
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
     * Gets html toggle
     * @deprecated since Moodle 2.4 as it appears not to be used any more.
     */
    public function get_toggles_html() {
        throw new coding_exception('get_toggles_html() can not be used any more');
    }

    /**
     * Prints html toggle
     * @deprecated since 2.4 as it appears not to be used any more.
     * @param unknown $type
     */
    public function print_toggle($type) {
        throw new coding_exception('print_toggle() can not be used any more');
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
        global $CFG, $USER, $OUTPUT;

        $rows = array();

        $showuserimage = $this->get_pref('showuserimage');
        // FIXME: MDL-52678 This get_capability_info is hacky and we should have an API for inserting grade row links instead.
        $canseeuserreport = false;
        $canseesingleview = false;
        if (get_capability_info('gradereport/'.$CFG->grade_profilereport.':view')) {
            $canseeuserreport = has_capability('gradereport/'.$CFG->grade_profilereport.':view', $this->context);
        }
        if (get_capability_info('gradereport/singleview:view')) {
            $canseesingleview = has_all_capabilities(array('gradereport/singleview:view', 'moodle/grade:viewall',
            'moodle/grade:edit'), $this->context);
        }
        $hasuserreportcell = $canseeuserreport || $canseesingleview;
        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);

        $strfeedback  = $this->get_lang_string("feedback");
        $strgrade     = $this->get_lang_string('grade');

        $extrafields = get_extra_user_fields($this->context);

        $arrows = $this->get_sort_arrows($extrafields);

        $colspan = 1 + $hasuserreportcell + count($extrafields);

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
        $studentheader->attributes['class'] = 'header';
        $studentheader->scope = 'col';
        $studentheader->header = true;
        $studentheader->id = 'studentheader';
        if ($hasuserreportcell) {
            $studentheader->colspan = 2;
        }
        $studentheader->text = $arrows['studentname'];

        $headerrow->cells[] = $studentheader;

        foreach ($extrafields as $field) {
            $fieldheader = new html_table_cell();
            $fieldheader->attributes['class'] = 'header userfield user' . $field;
            $fieldheader->scope = 'col';
            $fieldheader->header = true;
            $fieldheader->text = $arrows[$field];

            $headerrow->cells[] = $fieldheader;
        }

        $rows[] = $headerrow;

        $rows = $this->get_left_icons_row($rows, $colspan);

        $suspendedstring = null;
        foreach ($this->users as $userid => $user) {
            $userrow = new html_table_row();
            $userrow->id = 'fixed_user_'.$userid;

            $usercell = new html_table_cell();
            $usercell->attributes['class'] = 'header user';

            $usercell->header = true;
            $usercell->scope = 'row';

            if ($showuserimage) {
                $usercell->text = $OUTPUT->user_picture($user, array('visibletoscreenreaders' => false));
            }

            $fullname = fullname($user, $viewfullnames);
            $usercell->text .= html_writer::link(new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $this->course->id)), $fullname, array(
                'class' => 'username',
            ));

            if (!empty($user->suspendedenrolment)) {
                $usercell->attributes['class'] .= ' usersuspended';

                //may be lots of suspended users so only get the string once
                if (empty($suspendedstring)) {
                    $suspendedstring = get_string('userenrolmentsuspended', 'grades');
                }
                $icon = $OUTPUT->pix_icon('i/enrolmentsuspended', $suspendedstring);
                $usercell->text .= html_writer::tag('span', $icon, array('class'=>'usersuspendedicon'));
            }

            $userrow->cells[] = $usercell;

            $userreportcell = new html_table_cell();
            $userreportcell->attributes['class'] = 'userreport';
            $userreportcell->header = false;
            if ($canseeuserreport) {
                $a = new stdClass();
                $a->user = $fullname;
                $strgradesforuser = get_string('gradesforuser', 'grades', $a);
                $url = new moodle_url('/grade/report/'.$CFG->grade_profilereport.'/index.php', array('userid' => $user->id, 'id' => $this->course->id));
                $userreportcell->text .= $OUTPUT->action_icon($url, new pix_icon('t/grades', $strgradesforuser));
            }

            if ($canseesingleview) {
                $url = new moodle_url('/grade/report/singleview/index.php', array('id' => $this->course->id, 'itemid' => $user->id, 'item' => 'user'));
                $singleview = $OUTPUT->action_icon($url, new pix_icon('t/editstring', get_string('singleview', 'grades', $fullname)));
                $userreportcell->text .= $singleview;
            }

            if ($userreportcell->text) {
                $userrow->cells[] = $userreportcell;
            }

            foreach ($extrafields as $field) {
                $fieldcell = new html_table_cell();
                $fieldcell->attributes['class'] = 'userfield user' . $field;
                $fieldcell->header = false;
                $fieldcell->text = $user->{$field};
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
    public function get_right_rows($displayaverages) {
        global $CFG, $USER, $OUTPUT, $DB, $PAGE;

        $rows = array();
        $this->rowcount = 0;
        $numrows = count($this->gtree->get_levels());
        $numusers = count($this->users);
        $gradetabindex = 1;
        $columnstounset = array();
        $strgrade = $this->get_lang_string('grade');
        $strfeedback  = $this->get_lang_string("feedback");
        $arrows = $this->get_sort_arrows();

        $jsarguments = array(
            'cfg'       => array('ajaxenabled'=>false),
            'items'     => array(),
            'users'     => array(),
            'feedback'  => array(),
            'grades'    => array()
        );
        $jsscales = array();

        // Get preferences once.
        $showactivityicons = $this->get_pref('showactivityicons');
        $quickgrading = $this->get_pref('quickgrading');
        $showquickfeedback = $this->get_pref('showquickfeedback');
        $enableajax = $this->get_pref('enableajax');
        $showanalysisicon = $this->get_pref('showanalysisicon');

        // Get strings which are re-used inside the loop.
        $strftimedatetimeshort = get_string('strftimedatetimeshort');
        $strexcludedgrades = get_string('excluded', 'grades');
        $strerror = get_string('error');

        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);

        foreach ($this->gtree->get_levels() as $key => $row) {
            $headingrow = new html_table_row();
            $headingrow->attributes['class'] = 'heading_name_row';

            foreach ($row as $columnkey => $element) {
                $sortlink = clone($this->baseurl);
                if (isset($element['object']->id)) {
                    $sortlink->param('sortitemid', $element['object']->id);
                }

                $eid    = $element['eid'];
                $object = $element['object'];
                $type   = $element['type'];
                $categorystate = @$element['categorystate'];

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

                // Element is a filler
                if ($type == 'filler' or $type == 'fillerfirst' or $type == 'fillerlast') {
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
                        $categorycell->text = $this->get_course_header($element);
                        $categorycell->header = true;
                        $categorycell->scope = 'col';

                        // Print icons.
                        if ($USER->gradeediting[$this->courseid]) {
                            $categorycell->text .= $this->get_icons($element);
                        }

                        $headingrow->cells[] = $categorycell;
                    }
                } else {
                    // Element is a grade_item
                    if ($element['object']->id == $this->sortitemid) {
                        if ($this->sortorder == 'ASC') {
                            $arrow = $this->get_sort_arrow('up', $sortlink);
                        } else {
                            $arrow = $this->get_sort_arrow('down', $sortlink);
                        }
                    } else {
                        $arrow = $this->get_sort_arrow('move', $sortlink);
                    }

                    $headerlink = $this->gtree->get_element_header($element, true, $showactivityicons, false, false, true);

                    $itemcell = new html_table_cell();
                    $itemcell->attributes['class'] = $type . ' ' . $catlevel . ' highlightable'. ' i'. $element['object']->id;
                    $itemcell->attributes['data-itemid'] = $element['object']->id;

                    if ($element['object']->is_hidden()) {
                        $itemcell->attributes['class'] .= ' dimmed_text';
                    }

                    $singleview = '';

                    // FIXME: MDL-52678 This is extremely hacky we should have an API for inserting grade column links.
                    if (get_capability_info('gradereport/singleview:view')) {
                        if (has_all_capabilities(array('gradereport/singleview:view', 'moodle/grade:viewall',
                            'moodle/grade:edit'), $this->context)) {

                            $url = new moodle_url('/grade/report/singleview/index.php', array(
                                'id' => $this->course->id,
                                'item' => 'grade',
                                'itemid' => $element['object']->id));
                            $singleview = $OUTPUT->action_icon(
                                $url,
                                new pix_icon('t/editstring', get_string('singleview', 'grades', $element['object']->get_name()))
                            );
                        }
                    }

                    $itemcell->colspan = $colspan;
                    $itemcell->text = $headerlink . $arrow . $singleview;
                    $itemcell->header = true;
                    $itemcell->scope = 'col';

                    $headingrow->cells[] = $itemcell;
                }
            }
            $rows[] = $headingrow;
        }

        $rows = $this->get_right_icons_row($rows);

        // Preload scale objects for items with a scaleid and initialize tab indices
        $scaleslist = array();
        $tabindices = array();

        foreach ($this->gtree->get_items() as $itemid => $item) {
            $scale = null;
            if (!empty($item->scaleid)) {
                $scaleslist[] = $item->scaleid;
                $jsarguments['items'][$itemid] = array('id'=>$itemid, 'name'=>$item->get_name(true), 'type'=>'scale', 'scale'=>$item->scaleid, 'decimals'=>$item->get_decimals());
            } else {
                $jsarguments['items'][$itemid] = array('id'=>$itemid, 'name'=>$item->get_name(true), 'type'=>'value', 'scale'=>false, 'decimals'=>$item->get_decimals());
            }
            $tabindices[$item->id]['grade'] = $gradetabindex;
            $tabindices[$item->id]['feedback'] = $gradetabindex + $numusers;
            $gradetabindex += $numusers * 2;
        }
        $scalesarray = array();

        if (!empty($scaleslist)) {
            $scalesarray = $DB->get_records_list('scale', 'id', $scaleslist);
        }
        $jsscales = $scalesarray;

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
                $altered = array();
                $unknown = array();
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
            $jsarguments['users'][$userid] = $fullname;

            foreach ($this->gtree->items as $itemid => $unused) {
                $item =& $this->gtree->items[$itemid];
                $grade = $this->grades[$userid][$item->id];

                $itemcell = new html_table_cell();

                $itemcell->id = 'u'.$userid.'i'.$itemid;
                $itemcell->attributes['data-itemid'] = $itemid;

                // Get the decimal points preference for this item
                $decimalpoints = $item->get_decimals();

                if (array_key_exists($itemid, $unknown)) {
                    $gradeval = null;
                } else if (array_key_exists($itemid, $altered)) {
                    $gradeval = $altered[$itemid];
                } else {
                    $gradeval = $grade->finalgrade;
                }
                if (!empty($grade->finalgrade)) {
                    $gradevalforjs = null;
                    if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                        $gradevalforjs = (int)$gradeval;
                    } else {
                        $gradevalforjs = format_float($gradeval, $decimalpoints);
                    }
                    $jsarguments['grades'][] = array('user'=>$userid, 'item'=>$itemid, 'grade'=>$gradevalforjs);
                }

                // MDL-11274
                // Hide grades in the grader report if the current grader doesn't have 'moodle/grade:viewhidden'
                if (!$this->canviewhidden and $grade->is_hidden()) {
                    if (!empty($CFG->grade_hiddenasdate) and $grade->get_datesubmitted() and !$item->is_category_item() and !$item->is_course_item()) {
                        // the problem here is that we do not have the time when grade value was modified, 'timemodified' is general modification date for grade_grades records
                        $itemcell->text = "<span class='datesubmitted'>" .
                                userdate($grade->get_datesubmitted(), $strftimedatetimeshort) . "</span>";
                    } else {
                        $itemcell->text = '-';
                    }
                    $itemrow->cells[] = $itemcell;
                    continue;
                }

                // emulate grade element
                $eid = $this->gtree->get_grade_eid($grade);
                $element = array('eid'=>$eid, 'object'=>$grade, 'type'=>'grade');

                $itemcell->attributes['class'] .= ' grade i'.$itemid;
                if ($item->is_category_item()) {
                    $itemcell->attributes['class'] .= ' cat';
                }
                if ($item->is_course_item()) {
                    $itemcell->attributes['class'] .= ' course';
                }
                if ($grade->is_overridden()) {
                    $itemcell->attributes['class'] .= ' overridden';
                    $itemcell->attributes['aria-label'] = get_string('overriddengrade', 'gradereport_grader');
                }

                if (!empty($grade->feedback)) {
                    $feedback = wordwrap(trim(format_string($grade->feedback, $grade->feedbackformat)), 34, '<br>');
                    $itemcell->attributes['data-feedback'] = $feedback;
                    $jsarguments['feedback'][] = array('user'=>$userid, 'item'=>$itemid, 'content' => $feedback);
                }

                if ($grade->is_excluded()) {
                    // Adding white spaces before and after to prevent a screenreader from
                    // thinking that the words are attached to the next/previous <span> or text.
                    $itemcell->text .= " <span class='excludedfloater'>" . $strexcludedgrades . "</span> ";
                }

                // Do not show any icons if no grade (no record in DB to match)
                if (!$item->needsupdate and $USER->gradeediting[$this->courseid]) {
                    $itemcell->text .= $this->get_icons($element);
                }

                $hidden = '';
                if ($grade->is_hidden()) {
                    $hidden = ' dimmed_text ';
                }

                $gradepass = ' gradefail ';
                if ($grade->is_passed($item)) {
                    $gradepass = ' gradepass ';
                } else if (is_null($grade->is_passed($item))) {
                    $gradepass = '';
                }

                // if in editing mode, we need to print either a text box
                // or a drop down (for scales)
                // grades in item of type grade category or course are not directly editable
                if ($item->needsupdate) {
                    $itemcell->text .= "<span class='gradingerror{$hidden}'>" . $strerror . "</span>";

                } else if ($USER->gradeediting[$this->courseid]) {

                    if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                        $itemcell->attributes['class'] .= ' grade_type_scale';
                    } else if ($item->gradetype == GRADE_TYPE_VALUE) {
                        $itemcell->attributes['class'] .= ' grade_type_value';
                    } else if ($item->gradetype == GRADE_TYPE_TEXT) {
                        $itemcell->attributes['class'] .= ' grade_type_text';
                    }

                    if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                        $scale = $scalesarray[$item->scaleid];
                        $gradeval = (int)$gradeval; // scales use only integers
                        $scales = explode(",", $scale->scale);
                        // reindex because scale is off 1

                        // MDL-12104 some previous scales might have taken up part of the array
                        // so this needs to be reset
                        $scaleopt = array();
                        $i = 0;
                        foreach ($scales as $scaleoption) {
                            $i++;
                            $scaleopt[$i] = $scaleoption;
                        }

                        if ($quickgrading and $grade->is_editable()) {
                            $oldval = empty($gradeval) ? -1 : $gradeval;
                            if (empty($item->outcomeid)) {
                                $nogradestr = $this->get_lang_string('nograde');
                            } else {
                                $nogradestr = $this->get_lang_string('nooutcome', 'grades');
                            }
                            $attributes = array('tabindex' => $tabindices[$item->id]['grade'], 'id'=>'grade_'.$userid.'_'.$item->id);
                            $gradelabel = $fullname . ' ' . $item->get_name(true);
                            $itemcell->text .= html_writer::label(
                                get_string('useractivitygrade', 'gradereport_grader', $gradelabel), $attributes['id'], false,
                                    array('class' => 'accesshide'));
                            $itemcell->text .= html_writer::select($scaleopt, 'grade['.$userid.']['.$item->id.']', $gradeval, array(-1=>$nogradestr), $attributes);
                        } else if (!empty($scale)) {
                            $scales = explode(",", $scale->scale);

                            // invalid grade if gradeval < 1
                            if ($gradeval < 1) {
                                $itemcell->text .= "<span class='gradevalue{$hidden}{$gradepass}'>-</span>";
                            } else {
                                $gradeval = $grade->grade_item->bounded_grade($gradeval); //just in case somebody changes scale
                                $itemcell->text .= "<span class='gradevalue{$hidden}{$gradepass}'>{$scales[$gradeval - 1]}</span>";
                            }
                        }

                    } else if ($item->gradetype != GRADE_TYPE_TEXT) { // Value type
                        if ($quickgrading and $grade->is_editable()) {
                            $value = format_float($gradeval, $decimalpoints);
                            $gradelabel = $fullname . ' ' . $item->get_name(true);
                            $itemcell->text .= '<label class="accesshide" for="grade_'.$userid.'_'.$item->id.'">'
                                          .get_string('useractivitygrade', 'gradereport_grader', $gradelabel).'</label>';
                            $itemcell->text .= '<input size="6" tabindex="' . $tabindices[$item->id]['grade']
                                          . '" type="text" class="text" title="'. $strgrade .'" name="grade['
                                          .$userid.'][' .$item->id.']" id="grade_'.$userid.'_'.$item->id.'" value="'.$value.'" />';
                        } else {
                            $itemcell->text .= "<span class='gradevalue{$hidden}{$gradepass}'>" .
                                    format_float($gradeval, $decimalpoints) . "</span>";
                        }
                    }

                    // If quickfeedback is on, print an input element
                    if ($showquickfeedback and $grade->is_editable()) {
                        $feedbacklabel = $fullname . ' ' . $item->get_name(true);
                        $itemcell->text .= '<label class="accesshide" for="feedback_'.$userid.'_'.$item->id.'">'
                                      .get_string('useractivityfeedback', 'gradereport_grader', $feedbacklabel).'</label>';
                        $itemcell->text .= '<input class="quickfeedback" tabindex="' . $tabindices[$item->id]['feedback'].'" id="feedback_'.$userid.'_'.$item->id
                                      . '" size="6" title="' . $strfeedback . '" type="text" name="feedback['.$userid.']['.$item->id.']" value="' . s($grade->feedback) . '" />';
                    }

                } else { // Not editing
                    $gradedisplaytype = $item->get_displaytype();

                    if ($item->scaleid && !empty($scalesarray[$item->scaleid])) {
                        $itemcell->attributes['class'] .= ' grade_type_scale';
                    } else if ($item->gradetype == GRADE_TYPE_VALUE) {
                        $itemcell->attributes['class'] .= ' grade_type_value';
                    } else if ($item->gradetype == GRADE_TYPE_TEXT) {
                        $itemcell->attributes['class'] .= ' grade_type_text';
                    }

                    // Only allow edting if the grade is editable (not locked, not in a unoverridable category, etc).
                    if ($enableajax && $grade->is_editable()) {
                        // If a grade item is type text, and we don't have show quick feedback on, it can't be edited.
                        if ($item->gradetype != GRADE_TYPE_TEXT || $showquickfeedback) {
                            $itemcell->attributes['class'] .= ' clickable';
                        }
                    }

                    if ($item->needsupdate) {
                        $itemcell->text .= "<span class='gradingerror{$hidden}{$gradepass}'>" . $error . "</span>";
                    } else {
                        // The max and min for an aggregation may be different to the grade_item.
                        if (!is_null($gradeval)) {
                            $item->grademax = $grade->get_grade_max();
                            $item->grademin = $grade->get_grade_min();
                        }

                        $itemcell->text .= "<span class='gradevalue{$hidden}{$gradepass}'>" .
                                grade_format_gradevalue($gradeval, $item, true, $gradedisplaytype, null) . "</span>";
                        if ($showanalysisicon) {
                            $itemcell->text .= $this->gtree->get_grade_analysis_icon($grade);
                        }
                    }
                }

                // Enable keyboard navigation if the grade is editable (not locked, not in a unoverridable category, etc).
                if ($enableajax && $grade->is_editable()) {
                    // If a grade item is type text, and we don't have show quick feedback on, it can't be edited.
                    if ($item->gradetype != GRADE_TYPE_TEXT || $showquickfeedback) {
                        $itemcell->attributes['class'] .= ' gbnavigable';
                    }
                }

                if (!empty($this->gradeserror[$item->id][$userid])) {
                    $itemcell->text .= $this->gradeserror[$item->id][$userid];
                }

                $itemrow->cells[] = $itemcell;
            }
            $rows[] = $itemrow;
        }

        if ($enableajax) {
            $jsarguments['cfg']['ajaxenabled'] = true;
            $jsarguments['cfg']['scales'] = array();
            foreach ($jsscales as $scale) {
                // Trim the scale values, as they may have a space that is ommitted from values later.
                $jsarguments['cfg']['scales'][$scale->id] = array_map('trim', explode(',', $scale->scale));
            }
            $jsarguments['cfg']['feedbacktrunclength'] =  $this->feedback_trunc_length;

            // Student grades and feedback are already at $jsarguments['feedback'] and $jsarguments['grades']
        }
        $jsarguments['cfg']['isediting'] = (bool)$USER->gradeediting[$this->courseid];
        $jsarguments['cfg']['courseid'] = $this->courseid;
        $jsarguments['cfg']['studentsperpage'] = $this->get_students_per_page();
        $jsarguments['cfg']['showquickfeedback'] = (bool) $showquickfeedback;

        $module = array(
            'name'      => 'gradereport_grader',
            'fullpath'  => '/grade/report/grader/module.js',
            'requires'  => array('base', 'dom', 'event', 'event-mouseenter', 'event-key', 'io-queue', 'json-parse', 'overlay')
        );
        $PAGE->requires->js_init_call('M.gradereport_grader.init_report', $jsarguments, false, $module);
        $PAGE->requires->strings_for_js(array('addfeedback', 'feedback', 'grade'), 'grades');
        $PAGE->requires->strings_for_js(array('ajaxchoosescale', 'ajaxclicktoclose', 'ajaxerror', 'ajaxfailedupdate', 'ajaxfieldchanged'), 'gradereport_grader');
        if (!$enableajax && $USER->gradeediting[$this->courseid]) {
            $PAGE->requires->yui_module('moodle-core-formchangechecker',
                    'M.core_formchangechecker.init',
                    array(array(
                        'formid' => 'gradereport_grader'
                    ))
            );
            $PAGE->requires->string_for_js('changesmadereallygoaway', 'moodle');
        }

        $rows = $this->get_right_range_row($rows);
        if ($displayaverages) {
            $rows = $this->get_right_avg_row($rows, true);
            $rows = $this->get_right_avg_row($rows);
        }

        return $rows;
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
        $fulltable->attributes['class'] = 'gradereport-grader-table';
        $fulltable->id = 'user-grades';
        $fulltable->summary = get_string('summarygrader', 'gradereport_grader');

        // Extract rows from each side (left and right) and collate them into one row each
        foreach ($leftrows as $key => $row) {
            $row->cells = array_merge($row->cells, $rightrows[$key]->cells);
            $fulltable->data[] = $row;
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
     */
    public function get_left_icons_row($rows=array(), $colspan=1) {
        global $USER;

        if ($USER->gradeediting[$this->courseid]) {
            $controlsrow = new html_table_row();
            $controlsrow->attributes['class'] = 'controls';
            $controlscell = new html_table_cell();
            $controlscell->attributes['class'] = 'header controls';
            $controlscell->colspan = $colspan;
            $controlscell->text = $this->get_lang_string('controls', 'grades');

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
            $rangecell->text = $this->get_lang_string('range', 'grades');
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
     */
    public function get_right_icons_row($rows=array()) {
        global $USER;
        if ($USER->gradeediting[$this->courseid]) {
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
        global $OUTPUT;

        if ($this->get_pref('showranges')) {
            $rangesdisplaytype   = $this->get_pref('rangesdisplaytype');
            $rangesdecimalpoints = $this->get_pref('rangesdecimalpoints');
            $rangerow = new html_table_row();
            $rangerow->attributes['class'] = 'heading range';

            foreach ($this->gtree->items as $itemid => $unused) {
                $item =& $this->gtree->items[$itemid];
                $itemcell = new html_table_cell();
                $itemcell->attributes['class'] .= ' range i'. $itemid;

                $hidden = '';
                if ($item->is_hidden()) {
                    $hidden = ' dimmed_text ';
                }

                $formattedrange = $item->get_formatted_range($rangesdisplaytype, $rangesdecimalpoints);

                $itemcell->text = $OUTPUT->container($formattedrange, 'rangevalues'.$hidden);
                $rangerow->cells[] = $itemcell;
            }
            $rows[] = $rangerow;
        }
        return $rows;
    }

    /**
     * Builds and return the row of averages for the right part of the grader report.
     * @param array $rows Whether to return only group averages or all averages.
     * @param bool $grouponly Whether to return only group averages or all averages.
     * @return array Array of rows for the right part of the report
     */
    public function get_right_avg_row($rows=array(), $grouponly=false) {
        global $USER, $DB, $OUTPUT, $CFG;

        if (!$this->canviewhidden) {
            // Totals might be affected by hiding, if user can not see hidden grades the aggregations might be altered
            // better not show them at all if user can not see all hidden grades.
            return $rows;
        }

        $averagesdisplaytype   = $this->get_pref('averagesdisplaytype');
        $averagesdecimalpoints = $this->get_pref('averagesdecimalpoints');
        $meanselection         = $this->get_pref('meanselection');
        $shownumberofgrades    = $this->get_pref('shownumberofgrades');

        if ($grouponly) {
            $showaverages = $this->currentgroup && $this->get_pref('showaverages');
            $groupsql = $this->groupsql;
            $groupwheresql = $this->groupwheresql;
            $groupwheresqlparams = $this->groupwheresql_params;
        } else {
            $showaverages = $this->get_pref('showaverages');
            $groupsql = "";
            $groupwheresql = "";
            $groupwheresqlparams = array();
        }

        if ($showaverages) {
            $totalcount = $this->get_numusers($grouponly);

            // Limit to users with a gradeable role.
            list($gradebookrolessql, $gradebookrolesparams) = $DB->get_in_or_equal(explode(',', $this->gradebookroles), SQL_PARAMS_NAMED, 'grbr0');

            // Limit to users with an active enrollment.
            $coursecontext = $this->context->get_course_context(true);
            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
            $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $coursecontext);
            list($enrolledsql, $enrolledparams) = get_enrolled_sql($this->context, '', 0, $showonlyactiveenrol);

            // We want to query both the current context and parent contexts.
            list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($this->context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');

            $params = array_merge(array('courseid' => $this->courseid), $gradebookrolesparams, $enrolledparams, $groupwheresqlparams, $relatedctxparams);

            // Find sums of all grade items in course.
            $sql = "SELECT g.itemid, SUM(g.finalgrade) AS sum
                      FROM {grade_items} gi
                      JOIN {grade_grades} g ON g.itemid = gi.id
                      JOIN {user} u ON u.id = g.userid
                      JOIN ($enrolledsql) je ON je.id = u.id
                      JOIN (
                               SELECT DISTINCT ra.userid
                                 FROM {role_assignments} ra
                                WHERE ra.roleid $gradebookrolessql
                                  AND ra.contextid $relatedctxsql
                           ) rainner ON rainner.userid = u.id
                      $groupsql
                     WHERE gi.courseid = :courseid
                       AND u.deleted = 0
                       AND g.finalgrade IS NOT NULL
                       $groupwheresql
                     GROUP BY g.itemid";
            $sumarray = array();
            if ($sums = $DB->get_records_sql($sql, $params)) {
                foreach ($sums as $itemid => $csum) {
                    $sumarray[$itemid] = $csum->sum;
                }
            }

            // MDL-10875 Empty grades must be evaluated as grademin, NOT always 0
            // This query returns a count of ungraded grades (NULL finalgrade OR no matching record in grade_grades table)
            $sql = "SELECT gi.id, COUNT(DISTINCT u.id) AS count
                      FROM {grade_items} gi
                      CROSS JOIN {user} u
                      JOIN ($enrolledsql) je
                           ON je.id = u.id
                      JOIN {role_assignments} ra
                           ON ra.userid = u.id
                      LEFT OUTER JOIN {grade_grades} g
                           ON (g.itemid = gi.id AND g.userid = u.id AND g.finalgrade IS NOT NULL)
                      $groupsql
                     WHERE gi.courseid = :courseid
                           AND ra.roleid $gradebookrolessql
                           AND ra.contextid $relatedctxsql
                           AND u.deleted = 0
                           AND g.id IS NULL
                           $groupwheresql
                  GROUP BY gi.id";

            $ungradedcounts = $DB->get_records_sql($sql, $params);

            $avgrow = new html_table_row();
            $avgrow->attributes['class'] = 'avg';

            foreach ($this->gtree->items as $itemid => $unused) {
                $item =& $this->gtree->items[$itemid];

                if ($item->needsupdate) {
                    $avgcell = new html_table_cell();
                    $avgcell->attributes['class'] = 'i'. $itemid;
                    $avgcell->text = $OUTPUT->container(get_string('error'), 'gradingerror');
                    $avgrow->cells[] = $avgcell;
                    continue;
                }

                if (!isset($sumarray[$item->id])) {
                    $sumarray[$item->id] = 0;
                }

                if (empty($ungradedcounts[$itemid])) {
                    $ungradedcount = 0;
                } else {
                    $ungradedcount = $ungradedcounts[$itemid]->count;
                }

                if ($meanselection == GRADE_REPORT_MEAN_GRADED) {
                    $meancount = $totalcount - $ungradedcount;
                } else { // Bump up the sum by the number of ungraded items * grademin
                    $sumarray[$item->id] += $ungradedcount * $item->grademin;
                    $meancount = $totalcount;
                }

                // Determine which display type to use for this average
                if ($USER->gradeediting[$this->courseid]) {
                    $displaytype = GRADE_DISPLAY_TYPE_REAL;

                } else if ($averagesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // no ==0 here, please resave the report and user preferences
                    $displaytype = $item->get_displaytype();

                } else {
                    $displaytype = $averagesdisplaytype;
                }

                // Override grade_item setting if a display preference (not inherit) was set for the averages
                if ($averagesdecimalpoints == GRADE_REPORT_PREFERENCE_INHERIT) {
                    $decimalpoints = $item->get_decimals();

                } else {
                    $decimalpoints = $averagesdecimalpoints;
                }

                if (!isset($sumarray[$item->id]) || $meancount == 0) {
                    $avgcell = new html_table_cell();
                    $avgcell->attributes['class'] = 'i'. $itemid;
                    $avgcell->text = '-';
                    $avgrow->cells[] = $avgcell;

                } else {
                    $sum = $sumarray[$item->id];
                    $avgradeval = $sum/$meancount;
                    $gradehtml = grade_format_gradevalue($avgradeval, $item, true, $displaytype, $decimalpoints);

                    $numberofgrades = '';
                    if ($shownumberofgrades) {
                        $numberofgrades = " ($meancount)";
                    }

                    $avgcell = new html_table_cell();
                    $avgcell->attributes['class'] = 'i'. $itemid;
                    $avgcell->text = $gradehtml.$numberofgrades;
                    $avgrow->cells[] = $avgcell;
                }
            }
            $rows[] = $avgrow;
        }
        return $rows;
    }

    /**
     * Given element category, create a collapsible icon and
     * course header.
     *
     * @param array $element
     * @return string HTML
     */
    protected function get_course_header($element) {
        global $OUTPUT;

        $icon = '';
        // If object is a category, display expand/contract icon.
        if ($element['type'] == 'category') {
            // Load language strings.
            $strswitchminus = $this->get_lang_string('aggregatesonly', 'grades');
            $strswitchplus  = $this->get_lang_string('gradesonly', 'grades');
            $strswitchwhole = $this->get_lang_string('fullmode', 'grades');

            $url = new moodle_url($this->gpr->get_return_url(null, array('target' => $element['eid'], 'sesskey' => sesskey())));

            if (in_array($element['object']->id, $this->collapsed['aggregatesonly'])) {
                $url->param('action', 'switch_plus');
                $icon = $OUTPUT->action_icon($url, new pix_icon('t/switch_plus', $strswitchplus), null, null);
                $showing = get_string('showingaggregatesonly', 'grades');
            } else if (in_array($element['object']->id, $this->collapsed['gradesonly'])) {
                $url->param('action', 'switch_whole');
                $icon = $OUTPUT->action_icon($url, new pix_icon('t/switch_whole', $strswitchwhole), null, null);
                $showing = get_string('showinggradesonly', 'grades');
            } else {
                $url->param('action', 'switch_minus');
                $icon = $OUTPUT->action_icon($url, new pix_icon('t/switch_minus', $strswitchminus), null, null);
                $showing = get_string('showingfullmode', 'grades');
            }
        }

        $name = $element['object']->get_name();
        $courseheaderid = 'courseheader_' . clean_param($name, PARAM_ALPHANUMEXT);
        $courseheader = html_writer::tag('span', $name, array('id' => $courseheaderid,
                'title' => $name, 'class' => 'gradeitemheader'));
        $courseheader .= html_writer::label($showing, $courseheaderid, false, array('class' => 'accesshide'));
        $courseheader .= $icon;

        return $courseheader;
    }

    /**
     * Given a grade_category, grade_item or grade_grade, this function
     * figures out the state of the object and builds then returns a div
     * with the icons needed for the grader report.
     *
     * @param array $element
     * @return string HTML
     */
    protected function get_icons($element) {
        global $CFG, $USER, $OUTPUT;

        if (!$USER->gradeediting[$this->courseid]) {
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
            if ($this->get_pref('showcalculations')) {
                $editcalculationicon = $this->gtree->get_calculation_icon($element, $this->gpr);
            }

            if ($this->get_pref('showeyecons')) {
                $showhideicon = $this->gtree->get_hiding_icon($element, $this->gpr);
            }

            if ($this->get_pref('showlocks')) {
                $lockunlockicon = $this->gtree->get_locking_icon($element, $this->gpr);
            }

        }

        $gradeanalysisicon   = '';
        if ($this->get_pref('showanalysisicon') && $element['type'] == 'grade') {
            $gradeanalysisicon .= $this->gtree->get_grade_analysis_icon($element['object']);
        }

        return $OUTPUT->container($editicon.$editcalculationicon.$showhideicon.$lockunlockicon.$gradeanalysisicon, 'grade_icons');
    }

    /**
     * Given a category element returns collapsing +/- icon if available
     *
     * @deprecated since Moodle 2.9 MDL-46662 - please do not use this function any more.
     */
    protected function get_collapsing_icon($element) {
        throw new coding_exception('get_collapsing_icon() can not be used any more, please use get_course_header() instead.');
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
        if (empty($collapsed)) {
            $collapsed = array('aggregatesonly' => array(), 'gradesonly' => array());
        }
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
            return json_decode($collapsed, true);
        }

        // Try looking for old location of user setting that used to store all courses in one serialized user preference.
        if (($oldcollapsedpref = get_user_preferences('grade_report_grader_collapsed_categories')) !== null) {
            if ($collapsedall = unserialize_array($oldcollapsedpref)) {
                // We found the old-style preference, filter out only categories that belong to this course and update the prefs.
                $collapsed = static::filter_collapsed_categories($courseid, $collapsedall);
                if (!empty($collapsed['aggregatesonly']) || !empty($collapsed['gradesonly'])) {
                    static::set_collapsed_preferences($courseid, $collapsed);
                    $collapsedall['aggregatesonly'] = array_diff($collapsedall['aggregatesonly'], $collapsed['aggregatesonly']);
                    $collapsedall['gradesonly'] = array_diff($collapsedall['gradesonly'], $collapsed['gradesonly']);
                    if (!empty($collapsedall['aggregatesonly']) || !empty($collapsedall['gradesonly'])) {
                        set_user_preference('grade_report_grader_collapsed_categories', serialize($collapsedall));
                    } else {
                        unset_user_preference('grade_report_grader_collapsed_categories');
                    }
                }
            } else {
                // We found the old-style preference, but it is unreadable, discard it.
                unset_user_preference('grade_report_grader_collapsed_categories');
            }
        } else {
            $collapsed = array('aggregatesonly' => array(), 'gradesonly' => array());
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
    public function get_sort_arrows(array $extrafields = array()) {
        global $OUTPUT;
        $arrows = array();

        $strsortasc   = $this->get_lang_string('sortasc', 'grades');
        $strsortdesc  = $this->get_lang_string('sortdesc', 'grades');
        $strfirstname = $this->get_lang_string('firstname');
        $strlastname  = $this->get_lang_string('lastname');
        $iconasc = $OUTPUT->pix_icon('t/sort_asc', $strsortasc, '', array('class' => 'iconsmall sorticon'));
        $icondesc = $OUTPUT->pix_icon('t/sort_desc', $strsortdesc, '', array('class' => 'iconsmall sorticon'));

        $firstlink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid'=>'firstname')), $strfirstname);
        $lastlink = html_writer::link(new moodle_url($this->baseurl, array('sortitemid'=>'lastname')), $strlastname);

        $arrows['studentname'] = $lastlink;

        if ($this->sortitemid === 'lastname') {
            if ($this->sortorder == 'ASC') {
                $arrows['studentname'] .= $iconasc;
            } else {
                $arrows['studentname'] .= $icondesc;
            }
        }

        $arrows['studentname'] .= ' ' . $firstlink;

        if ($this->sortitemid === 'firstname') {
            if ($this->sortorder == 'ASC') {
                $arrows['studentname'] .= $iconasc;
            } else {
                $arrows['studentname'] .= $icondesc;
            }
        }

        foreach ($extrafields as $field) {
            $fieldlink = html_writer::link(new moodle_url($this->baseurl,
                    array('sortitemid'=>$field)), get_user_field_name($field));
            $arrows[$field] = $fieldlink;

            if ($field == $this->sortitemid) {
                if ($this->sortorder == 'ASC') {
                    $arrows[$field] .= $iconasc;
                } else {
                    $arrows[$field] .= $icondesc;
                }
            }
        }

        return $arrows;
    }

    /**
     * Returns the maximum number of students to be displayed on each page
     *
     * @return int The maximum number of students to display per page
     */
    public function get_students_per_page() {
        return $this->get_pref('studentsperpage');
    }
}

