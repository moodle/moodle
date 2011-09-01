<?php
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains a library of functions and constants for the
 * BasicLTI module
 *
 * @package lti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/lti/locallib.php');

/**
 * List of features supported in URL module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function lti_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted basiclti record
 **/
function lti_add_instance($formdata) {
    global $DB;
    $formdata->timecreated = time();
    $formdata->timemodified = $formdata->timecreated;
    $formdata->servicesalt = uniqid('', true);
    
    if(!isset($formdata->grade)){
        $formdata->grade = 100;
    }
    
    $id = $DB->insert_record("lti", $formdata);

    if ($formdata->instructorchoiceacceptgrades == 1) {
        $basiclti = $DB->get_record('lti', array('id'=>$id));
        $basiclti->cmidnumber = $formdata->cmidnumber;
        
        lti_grade_item_update($basiclti);
    }

    return $id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function lti_update_instance($formdata) {
    global $DB;

    $formdata->timemodified = time();
    $formdata->id = $formdata->instance;

    if(!isset($formdata->showtitle)){
        $formdata->showtitle = 0;
    }
    
    if(!isset($formdata->showdescription)){
        $formdata->showdescription = 0;
    }
    
    if ($formdata->instructorchoiceacceptgrades == 1) {
        $basicltirec = $DB->get_record("lti", array("id" => $formdata->id));
        $basicltirec->cmidnumber = $formdata->cmidnumber;
        
        lti_grade_item_update($basicltirec);
    } else {
        lti_grade_item_delete($formdata);
    }

    return $DB->update_record("lti", $formdata);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function lti_delete_instance($id) {
    global $DB;

    if (! $basiclti = $DB->get_record("lti", array("id" => $id))) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #
    lti_grade_item_delete($basiclti);

    return $DB->delete_records("lti", array("id" => $basiclti->id));
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @TODO: implement this moodle function (if needed)
 **/
function lti_user_outline($course, $user, $mod, $basiclti) {
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @TODO: implement this moodle function (if needed)
 **/
function lti_user_complete($course, $user, $mod, $basiclti) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in basiclti activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @uses $CFG
 * @return boolean
 * @TODO: implement this moodle function
 **/
function lti_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @uses $CFG
 * @return boolean
 **/
function lti_cron () {
    return true;
}

/**
 * Must return an array of grades for a given instance of this module,
 * indexed by user.  It also returns a maximum allowed grade.
 *
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $basicltiid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 *
 * @TODO: implement this moodle function (if needed)
 **/
function lti_grades($basicltiid) {
    return null;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of basiclti. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $basicltiid ID of an instance of this module
 * @return mixed boolean/array of students
 *
 * @TODO: implement this moodle function
 **/
function lti_get_participants($basicltiid) {
    return false;
}

/**
 * This function returns if a scale is being used by one basiclti
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $basicltiid ID of an instance of this module
 * @return mixed
 *
 * @TODO: implement this moodle function (if needed)
 **/
function lti_scale_used ($basicltiid, $scaleid) {
    $return = false;

    //$rec = get_record("basiclti","id","$basicltiid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of basiclti.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any basiclti
 *
 */
function lti_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('lti', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function lti_install() {
     return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function lti_uninstall() {
    return true;
}

/**
 * Returns available Basic LTI types
 *
 * @return array of basicLTI types
 */
function lti_get_lti_types() {
    global $DB;

    return $DB->get_records('lti_types');
}

/**
 * Returns Basic LTI types configuration
 *
 * @return array of basicLTI types
 */
/*function lti_get_types() {
    $types = array();

    $basicltitypes = lti_get_lti_types();
    if (!empty($basicltitypes)) {
        foreach ($basicltitypes as $basicltitype) {
            $ltitypesconfig = lti_get_type_config($basicltitype->id);

            $modclass = MOD_CLASS_ACTIVITY;
            if (isset($ltitypesconfig['module_class_type'])) {
                if ($ltitypesconfig['module_class_type']=='1') {
                    $modclass = MOD_CLASS_RESOURCE;
                }
            }

            $type = new object();
            $type->modclass = $modclass;
            $type->type = 'lti&amp;type='.urlencode($basicltitype->rawname);
            $type->typestr = $basicltitype->name;
            $types[] = $type;
        }
    }

    return $types;
}*/

//////////////////////////////////////////////////////////////////////////////////////
/// Any other basiclti functions go here.  Each of them must have a name that
/// starts with basiclti_
/// Remember (see note in first lines) that, if this section grows, it's HIGHLY
/// recommended to move all funcions below to a new "localib.php" file.

///**
// *
// */
//function process_outcomes($userid, $course, $basiclti) {
//    global $CFG, $USER;
//
//    if (empty($CFG->enableoutcomes)) {
//        return;
//    }
//
//    require_once($CFG->libdir.'/gradelib.php');
//
//    if (!$formdata = data_submitted() or !confirm_sesskey()) {
//        return;
//    }
//
//    $data = array();
//    $grading_info = grade_get_grades($course->id, 'mod', 'basiclti', $basiclti->id, $userid);
//
//    if (!empty($grading_info->outcomes)) {
//        foreach ($grading_info->outcomes as $n => $old) {
//            $name = 'outcome_'.$n;
//            if (isset($formdata->{$name}[$userid]) and $old->grades[$userid]->grade != $formdata->{$name}[$userid]) {
//                $data[$n] = $formdata->{$name}[$userid];
//            }
//        }
//    }
//    if (count($data) > 0) {
//        grade_update_outcomes('mod/basiclti', $course->id, 'mod', 'basiclti', $basiclti->id, $userid, $data);
//    }
//
//}

/**
 * Top-level function for handling of submissions called by submissions.php
 *
 * This is for handling the teacher interaction with the grading interface
 *
 * @global object
 * @param string $mode Specifies the kind of teacher interaction taking place
 */
function lti_submissions($cm, $course, $basiclti, $mode) {
    ///The main switch is changed to facilitate
    ///1) Batch fast grading
    ///2) Skip to the next one on the popup
    ///3) Save and Skip to the next one on the popup

    //make user global so we can use the id
    global $USER, $OUTPUT, $DB;

    $mailinfo = optional_param('mailinfo', null, PARAM_BOOL);

    if (optional_param('next', null, PARAM_BOOL)) {
        $mode='next';
    }
    if (optional_param('saveandnext', null, PARAM_BOOL)) {
        $mode='saveandnext';
    }

    if (is_null($mailinfo)) {
        if (optional_param('sesskey', null, PARAM_BOOL)) {
            set_user_preference('lti_mailinfo', $mailinfo);
        } else {
            $mailinfo = get_user_preferences('lti_mailinfo', 0);
        }
    } else {
        set_user_preference('lti_mailinfo', $mailinfo);
    }

    switch ($mode) {
        case 'grade':                         // We are in a main window grading
            if ($submission = process_feedback()) {
                lti_display_submissions($cm, $course, $basiclti, get_string('changessaved'));
            } else {
                lti_display_submissions($cm, $course, $basiclti);
            }
            break;

        case 'single':                        // We are in a main window displaying one submission
            if ($submission = process_feedback()) {
                lti_display_submissions($cm, $course, $basiclti, get_string('changessaved'));
            } else {
                display_submission();
            }
            break;

        case 'all':                          // Main window, display everything
            lti_display_submissions($cm, $course, $basiclti);
            break;

        case 'fastgrade':
            /// do the fast grading stuff - this process should work for all 3 subclasses
            $grading    = false;
            $commenting = false;
            $col        = false;
            if (isset($_POST['submissioncomment'])) {
                $col = 'submissioncomment';
                $commenting = true;
            }
            if (isset($_POST['menu'])) {
                $col = 'menu';
                $grading = true;
            }
            if (!$col) {
                //both submissioncomment and grade columns collapsed..
                lti_display_submissions($cm, $course, $basiclti);
                break;
            }

            foreach ($_POST[$col] as $id => $unusedvalue) {

                $id = (int)$id; //clean parameter name

                // Get grade item
                $gradeitem = $DB->get_record('grade_items', array('courseid' => $cm->course, 'iteminstance' => $cm->instance));

                // Get grade
                $gradeentry = $DB->get_record('grade_grades', array('userid' => $id, 'itemid' => $gradeitem->id));

                $grade = $_POST['menu'][$id];
                $feedback = trim($_POST['submissioncomment'][$id]);

                if ((!$gradeentry) && (($grade != '-1') || ($feedback != ''))) {
                    $newsubmission = true;
                } else {
                    $newsubmission = false;
                }

                //for fast grade, we need to check if any changes take place
                $updatedb = false;

                if ($gradeentry) {
                    if ($grading) {
                        $grade = $_POST['menu'][$id];
                        $updatedb = $updatedb || (($gradeentry->rawgrade != $grade) && ($gradeentry->rawgrade != '-1'));
                        if ($grade != '-1') {
                            $gradeentry->rawgrade = $grade;
                            $gradeentry->finalgrade = $grade;
                        } else {
                            $gradeentry->rawgrade = null;
                            $gradeentry->finalgrade = null;
                        }
                    } else {
                        if (!$newsubmission) {
                            unset($gradeentry->rawgrade);  // Don't need to update this.
                        }
                    }

                    if ($commenting) {
                        $commentvalue = trim($_POST['submissioncomment'][$id]);
                        $updatedb = $updatedb || ($gradeentry->feedback != $commentvalue);
                        // Special case
                        if (($gradeentry->feedback == null) && ($commentvalue == "")) {
                            unset($gradeentry->feedback);
                        }
                        $gradeentry->feedback = $commentvalue;
                    } else {
                        unset($gradeentry->feedback);  // Don't need to update this.
                    }

                } else { // No previous grade entry found
                    if ($newsubmission) {
                        if ($grade != '-1') {
                            $gradeentry->rawgrade = $grade;
                            $updatedb = true;
                        }
                        if ($feedback != '') {
                            $gradeentry->feedback = $feedback;
                            $updatedb = true;
                        }
                    }
                }

                $gradeentry->usermodified    = $USER->id;
                if (!$gradeentry->timecreated) {
                    $gradeentry->timecreated = time();
                }
                $gradeentry->timemodified = time();

                //if it is not an update, we don't change the last modified time etc.
                //this will also not write into database if no submissioncomment and grade is entered.
                if ($updatedb) {
                    if ($gradeentry->rawgrade == '-1') {
                        $gradeentry->rawgrade = null;
                    }

                    if ($newsubmission) {
                        if (!isset($gradeentry->feedback)) {
                            $gradeentry->feedback = '';
                        }
                        $gradeentry->itemid = $gradeitem->id;
                        $gradeentry->userid = $id;
                        $sid = $DB->insert_record("grade_grades", $gradeentry);
                        $gradeentry->id = $sid;
                    } else {
                        $DB->update_record("grade_grades", $gradeentry);
                    }

                    //add to log only if updating
                    add_to_log($course->id, 'lti', 'update grades',
                               'submissions.php?id='.$cm->id.'&user='.$USER->id,
                               $USER->id, $cm->id);
                }

            }

            $message = $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');

            lti_display_submissions($cm, $course, $basiclti, $message);
            break;

        case 'saveandnext':
            ///We are in pop up. save the current one and go to the next one.
            //first we save the current changes
            if ($submission = process_feedback()) {
                //print_heading(get_string('changessaved'));
                //$extra_javascript = $this->update_main_listing($submission);
            }

        case 'next':
            /// We are currently in pop up, but we want to skip to next one without saving.
            ///    This turns out to be similar to a single case
            /// The URL used is for the next submission.
            $offset = required_param('offset', PARAM_INT);
            $nextid = required_param('nextid', PARAM_INT);
            $id = required_param('id', PARAM_INT);
            $offset = (int)$offset+1;
            //$this->display_submission($offset+1 , $nextid);
            redirect('submissions.php?id='.$id.'&userid='. $nextid . '&mode=single&offset='.$offset);
            break;

        case 'singlenosave':
            display_submission();
            break;

        default:
            echo "Critical error. Something is seriously wrong!!";
            break;
    }
}

/**
 *  Display all the submissions ready for grading
 *
 * @global object
 * @global object
 * @global object
 * @global object
 * @param string $message
 * @return bool|void
 */
function lti_display_submissions($cm, $course, $basiclti, $message='') {
    global $CFG, $DB, $OUTPUT, $PAGE;
    require_once($CFG->libdir.'/gradelib.php');

    /* first we check to see if the form has just been submitted
     * to request user_preference updates
     */
    $updatepref = optional_param('updatepref', 0, PARAM_INT);

    if (isset($_POST['updatepref'])) {
        $perpage = optional_param('perpage', 10, PARAM_INT);
        $perpage = ($perpage <= 0) ? 10 : $perpage;
        $filter = optional_param('filter', 0, PARAM_INT);
        set_user_preference('lti_perpage', $perpage);
        set_user_preference('lti_quickgrade', optional_param('quickgrade', 0, PARAM_BOOL));
        set_user_preference('lti_filter', $filter);
    }

    /* next we get perpage and quickgrade (allow quick grade) params
     * from database
     */
    $perpage    = get_user_preferences('lti_perpage', 10);
    $quickgrade = get_user_preferences('lti_quickgrade', 0);
    $filter = get_user_preferences('lti_filter', 0);
    $grading_info = grade_get_grades($course->id, 'mod', 'lti', $basiclti->id);

    if (!empty($CFG->enableoutcomes) and !empty($grading_info->outcomes)) {
        $uses_outcomes = true;
    } else {
        $uses_outcomes = false;
    }

    $page    = optional_param('page', 0, PARAM_INT);
    $strsaveallfeedback = get_string('saveallfeedback', 'lti');

    $tabindex = 1; //tabindex for quick grading tabbing; Not working for dropdowns yet
    add_to_log($course->id, 'lti', 'view submission', 'submissions.php?id='.$cm->id, $basiclti->id, $cm->id);

    $PAGE->set_title(format_string($basiclti->name, true));
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    echo '<div class="usersubmissions">';

    //hook to allow plagiarism plugins to update status/print links.
    plagiarism_update_status($course, $cm);

    /// Print quickgrade form around the table
    if ($quickgrade) {
        $formattrs = array();
        $formattrs['action'] = new moodle_url('/mod/lti/submissions.php');
        $formattrs['id'] = 'fastg';
        $formattrs['method'] = 'post';

        echo html_writer::start_tag('form', $formattrs);
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'id',      'value'=> $cm->id));
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'mode',    'value'=> 'fastgrade'));
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'page',    'value'=> $page));
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=> sesskey()));
    }

    $course_context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (has_capability('gradereport/grader:view', $course_context) && has_capability('moodle/grade:viewall', $course_context)) {
        echo '<div class="allcoursegrades"><a href="' . $CFG->wwwroot . '/grade/report/grader/index.php?id=' . $course->id . '">'
            . get_string('seeallcoursegrades', 'grades') . '</a></div>';
    }

    if (!empty($message)) {
        echo $message;   // display messages here if any
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

/// Check to see if groups are being used in this tool

    /// find out current groups mode
    $groupmode = groups_get_activity_groupmode($cm);
    $currentgroup = groups_get_activity_group($cm, true);
    groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/lti/submissions.php?id=' . $cm->id);

    /// Get all ppl that are allowed to submit tools
    list($esql, $params) = get_enrolled_sql($context, 'mod/lti:view', $currentgroup);

    $sql = "SELECT u.id FROM {user} u ".
           "LEFT JOIN ($esql) eu ON eu.id=u.id ".
           "WHERE u.deleted = 0 AND eu.id=u.id ";

    $users = $DB->get_records_sql($sql, $params);
    if (!empty($users)) {
        $users = array_keys($users);
    }

    // if groupmembersonly used, remove users who are not in any group
    if ($users and !empty($CFG->enablegroupmembersonly) and $cm->groupmembersonly) {
        if ($groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id')) {
            $users = array_intersect($users, array_keys($groupingusers));
        }
    }

    $tablecolumns = array('picture', 'fullname', 'grade', 'submissioncomment', 'timemodified', 'timemarked', 'status', 'finalgrade');
    if ($uses_outcomes) {
        $tablecolumns[] = 'outcome'; // no sorting based on outcomes column
    }

    $tableheaders = array('',
                          get_string('fullname'),
                          get_string('grade'),
                          get_string('comment', 'lti'),
                          get_string('lastmodified').' ('.get_string('submission', 'lti').')',
                          get_string('lastmodified').' ('.get_string('grade').')',
                          get_string('status'),
                          get_string('finalgrade', 'grades'));
    if ($uses_outcomes) {
        $tableheaders[] = get_string('outcome', 'grades');
    }

    require_once($CFG->libdir.'/tablelib.php');
    $table = new flexible_table('mod-lti-submissions');

    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($CFG->wwwroot.'/mod/lti/submissions.php?id='.$cm->id.'&amp;currentgroup='.$currentgroup);

    $table->sortable(true, 'lastname');//sorted by lastname by default
    $table->collapsible(true);
    $table->initialbars(true);

    $table->column_suppress('picture');
    $table->column_suppress('fullname');

    $table->column_class('picture', 'picture');
    $table->column_class('fullname', 'fullname');
    $table->column_class('grade', 'grade');
    $table->column_class('submissioncomment', 'comment');
    $table->column_class('timemodified', 'timemodified');
    $table->column_class('timemarked', 'timemarked');
    $table->column_class('status', 'status');
    $table->column_class('finalgrade', 'finalgrade');
    if ($uses_outcomes) {
        $table->column_class('outcome', 'outcome');
    }

    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'attempts');
    $table->set_attribute('class', 'submissions');
    $table->set_attribute('width', '100%');

    $table->no_sorting('finalgrade');
    $table->no_sorting('outcome');

    // Start working -- this is necessary as soon as the niceties are over
    $table->setup();

    if (empty($users)) {
        echo $OUTPUT->heading(get_string('noviewusers', 'lti'));
        echo '</div>';
        return true;
    }

       /// Construct the SQL
    list($where, $params) = $table->get_sql_where();
    if ($where) {
        $where .= ' AND ';
    }

    if ($sort = $table->get_sql_sort()) {
        $sort = ' ORDER BY '.$sort;
    }

    $ufields = user_picture::fields('u');

    $gradeitem = $DB->get_record('grade_items', array('courseid' => $cm->course, 'iteminstance' => $cm->instance));

    $select = "SELECT $ufields,
              g.rawgrade, g.feedback,
              g.timemodified, g.timecreated ";

    $sql = 'FROM {user} u'.
            ' LEFT JOIN {grade_grades} g ON u.id = g.userid AND g.itemid = '.$gradeitem->id.
            ' LEFT JOIN {grade_items} i ON g.itemid = i.id'.
            ' AND i.iteminstance = '.$basiclti->id.
            ' WHERE '.$where.'u.id IN ('.implode(',', $users).') ';

    $ausers = $DB->get_records_sql($select.$sql.$sort, $params, $table->get_page_start(), $table->get_page_size());

    $table->pagesize($perpage, count($users));

    ///offset used to calculate index of student in that particular query, needed for the pop up to know who's next
    $offset = $page * $perpage;
    $strupdate = get_string('update');
    $strgrade  = get_string('grade');
    $grademenu = make_grades_menu($basiclti->grade);
    if ($ausers !== false) {
        $grading_info = grade_get_grades($course->id, 'mod', 'lti', $basiclti->id, array_keys($ausers));
        $endposition = $offset + $perpage;
        $currentposition = 0;
        foreach ($ausers as $auser) {

            if ($auser->timemodified > 0) {
                $timemodified = '<div id="ts'.$auser->id.'">'.userdate($auser->timemodified).'</div>';
            } else {
                $timemodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
            }
            if ($auser->timecreated > 0) {
                $timecreated = '<div id="ts'.$auser->id.'">'.userdate($auser->timecreated).'</div>';
            } else {
                $timecreated = '<div id="ts'.$auser->id.'">&nbsp;</div>';
            }

            if ($currentposition == $offset && $offset < $endposition) {
                $final_grade = $grading_info->items[0]->grades[$auser->id];
                $grademax = $grading_info->items[0]->grademax;
                $final_grade->formatted_grade = round($final_grade->grade, 2) .' / ' . round($grademax, 2);
                $locked_overridden = 'locked';
                if ($final_grade->overridden) {
                    $locked_overridden = 'overridden';
                }

                /// Calculate user status
                $picture = $OUTPUT->user_picture($auser);

                $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';
                $status          = '<div id="st'.$auser->id.'">&nbsp;</div>';

                if ($final_grade->locked or $final_grade->overridden) {
                    $grade = '<div id="g'.$auser->id.'">'.$final_grade->formatted_grade . '</div>';
                } else if ($quickgrade) {   // allow editing
                    $attributes = array();
                    $attributes['tabindex'] = $tabindex++;
                    if ($auser->rawgrade != "") {
                        $menu = html_writer::select(make_grades_menu($basiclti->grade), 'menu['.$auser->id.']', round($auser->rawgrade, 0), array(-1=>get_string('nograde')), $attributes);
                    } else {
                        $menu = html_writer::select(make_grades_menu($basiclti->grade), 'menu['.$auser->id.']', -1, array(-1=>get_string('nograde')), $attributes);
                    }
                    $grade = '<div id="g'.$auser->id.'">'.$menu.'</div>';
                } else if ($final_grade->grade) {
                    if ($auser->rawgrade != "") {
                        $grade = '<div id="g'.$auser->id.'">'.$final_grade->formatted_grade.'</div>';
                    } else {
                        $grade = '<div id="g'.$auser->id.'">-1</div>';
                    }

                } else {
                    $grade = '<div id="g'.$auser->id.'">No Grade</div>';
                }

                if ($final_grade->locked or $final_grade->overridden) {
                    $comment = '<div id="com'.$auser->id.'">'.$final_grade->str_feedback.'</div>';
                } else if ($quickgrade) {
                    $comment = '<div id="com'.$auser->id.'">'
                             . '<textarea tabindex="'.$tabindex++.'" name="submissioncomment['.$auser->id.']" id="submissioncomment'
                             . $auser->id.'" rows="2" cols="20">'.($auser->feedback).'</textarea></div>';
                } else {
                    $comment = '<div id="com'.$auser->id.'">'.shorten_text(strip_tags($auser->feedback), 15).'</div>';
                }

                if (empty($auser->status)) { /// Confirm we have exclusively 0 or 1
                    $auser->status = 0;
                } else {
                    $auser->status = 1;
                }

                $buttontext = ($auser->status == 1) ? $strupdate : $strgrade;

                ///No more buttons, we use popups ;-).
                $popup_url = '/mod/lti/submissions.php?id='.$cm->id
                           . '&amp;userid='.$auser->id.'&amp;mode=single'.'&amp;filter='.$filter.'&amp;offset='.$offset++;

                $button = $OUTPUT->action_link($popup_url, $buttontext);

                $status  = '<div id="up'.$auser->id.'" class="s'.$auser->status.'">'.$button.'</div>';

                $finalgrade = '<span id="finalgrade_'.$auser->id.'">'.$final_grade->str_grade.'</span>';

                $outcomes = '';

                if ($uses_outcomes) {

                    foreach ($grading_info->outcomes as $n => $outcome) {
                        $outcomes .= '<div class="outcome"><label>'.$outcome->name.'</label>';
                        $options = make_grades_menu(-$outcome->scaleid);

                        if ($outcome->grades[$auser->id]->locked or !$quickgrade) {
                            $options[0] = get_string('nooutcome', 'grades');
                            $outcomes .= ': <span id="outcome_'.$n.'_'.$auser->id.'">'.$options[$outcome->grades[$auser->id]->grade].'</span>';
                        } else {
                            $attributes = array();
                            $attributes['tabindex'] = $tabindex++;
                            $attributes['id'] = 'outcome_'.$n.'_'.$auser->id;
                            $outcomes .= ' '.html_writer::select($options, 'outcome_'.$n.'['.$auser->id.']', $outcome->grades[$auser->id]->grade, array(0=>get_string('nooutcome', 'grades')), $attributes);
                        }
                        $outcomes .= '</div>';
                    }
                }

                $userlink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $auser->id . '&amp;course=' . $course->id . '">' . fullname($auser, has_capability('moodle/site:viewfullnames', $context)) . '</a>';
                $row = array($picture, $userlink, $grade, $comment, $timemodified, $timecreated, $status, $finalgrade);
                if ($uses_outcomes) {
                    $row[] = $outcomes;
                }

                $table->add_data($row);
            }
            $currentposition++;
        }
    }

    $table->print_html();  /// Print the whole table

    /// Print quickgrade form around the table
    if ($quickgrade && $table->started_output) {
        $mailinfopref = false;
        if (get_user_preferences('lti_mailinfo', 1)) {
            $mailinfopref = true;
        }
        $emailnotification =  html_writer::checkbox('mailinfo', 1, $mailinfopref, get_string('enableemailnotification', 'lti'));

        $emailnotification .= $OUTPUT->help_icon('enableemailnotification', 'lti');
        echo html_writer::tag('div', $emailnotification, array('class'=>'emailnotification'));

        $savefeedback = html_writer::empty_tag('input', array('type'=>'submit', 'name'=>'fastg', 'value'=>get_string('saveallfeedback', 'lti')));
        echo html_writer::tag('div', $savefeedback, array('class'=>'fastgbutton'));

        echo html_writer::end_tag('form');
    } else if ($quickgrade) {
        echo html_writer::end_tag('form');
    }

    echo '</div>';
    /// End of fast grading form

    /// Mini form for setting user preference

    $formaction = new moodle_url('/mod/lti/submissions.php', array('id'=>$cm->id));
    $mform = new MoodleQuickForm('optionspref', 'post', $formaction, '', array('class'=>'optionspref'));

    $mform->addElement('hidden', 'updatepref');
    $mform->setDefault('updatepref', 1);
    $mform->addElement('header', 'qgprefs', get_string('optionalsettings', 'lti'));
//        $mform->addElement('select', 'filter', get_string('show'),  $filters);

    $mform->setDefault('filter', $filter);

    $mform->addElement('text', 'perpage', get_string('pagesize', 'lti'), array('size'=>1));
    $mform->setDefault('perpage', $perpage);

    $mform->addElement('checkbox', 'quickgrade', get_string('quickgrade', 'lti'));
    $mform->setDefault('quickgrade', $quickgrade);
    $mform->addHelpButton('quickgrade', 'quickgrade', 'lti');

    $mform->addElement('submit', 'savepreferences', get_string('savepreferences'));

    $mform->display();

    echo $OUTPUT->footer();
}

/**
 * Create grade item for given basiclti
 *
 * @param object $basiclti object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function lti_grade_item_update($basiclti, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $params = array('itemname'=>$basiclti->name, 'idnumber'=>$basiclti->cmidnumber);

    if ($basiclti->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $basiclti->grade;
        $params['grademin']  = 0;

    } else if ($basiclti->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$basiclti->grade;

    } else {
        $params['gradetype'] = GRADE_TYPE_TEXT; // allow text comments only
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/lti', $basiclti->course, 'mod', 'lti', $basiclti->id, 0, $grades, $params);
}

/**
 * Delete grade item for given basiclti
 *
 * @param object $basiclti object
 * @return object basiclti
 */
function lti_grade_item_delete($basiclti) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/lti', $basiclti->course, 'mod', 'lti', $basiclti->id, 0, null, array('deleted'=>1));
}

