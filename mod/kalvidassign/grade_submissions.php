<?php
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
 * Kaltura grade submission script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/renderer.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/grade_preferences_form.php');

$id      = required_param('cmid', PARAM_INT);           // Course Module ID
$mode    = optional_param('mode', 0, PARAM_TEXT);
$tifirst = optional_param('tifirst', '', PARAM_TEXT);
$tilast  = optional_param('tilast', '', PARAM_TEXT);
$page    = optional_param('page', 0, PARAM_INT);

$url = new moodle_url('/mod/kalvidassign/grade_submissions.php');
$url->param('cmid', $id);

if (!empty($mode)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }
}

list($cm, $course, $kalvidassignobj) = kalvidassign_validate_cmid($id);

require_login($course->id, false, $cm);

global $PAGE, $OUTPUT, $USER;

$currentcrumb = get_string('singlesubmissionheader', 'kalvidassign');
$PAGE->set_url($url);
$PAGE->set_title(format_string($kalvidassignobj->name));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($currentcrumb);

$renderer = $PAGE->get_renderer('mod_kalvidassign');
$courseid = $PAGE->context->get_course_context(false);

echo $OUTPUT->header();

require_capability('mod/kalvidassign:gradesubmission', context_module::instance($cm->id));

$prefform =  new kalvidassign_gradepreferences_form(null, array('cmid' => $cm->id, 'groupmode' => $cm->groupmode));
$data = null;

if ($data = $prefform->get_data()) {
    set_user_preference('kalvidassign_group_filter', $data->group_filter);

    set_user_preference('kalvidassign_filter', $data->filter);

    if ($data->perpage > 0) {
        set_user_preference('kalvidassign_perpage', $data->perpage);
    }

    if (isset($data->quickgrade)) {
        set_user_preference('kalvidassign_quickgrade', $data->quickgrade);
    } else {
        set_user_preference('kalvidassign_quickgrade', '0');
    }

}

if (empty($data)) {
    $data = new stdClass();
}

$data->filter       = get_user_preferences('kalvidassign_filter', 0);
$data->perpage      = get_user_preferences('kalvidassign_perpage', 10);
$data->quickgrade   = get_user_preferences('kalvidassign_quickgrade', 0);
$data->group_filter = get_user_preferences('kalvidassign_group_filter', 0);

$gradedata = data_submitted();

// Check if fast grading was passed to the form and process the data
if (!empty($gradedata->mode)) {

    $usersubmission = array();
    $time = time();
    $updated = false;

    foreach ($gradedata->users as $userid => $val) {

        $param = array('vidassignid' => $kalvidassignobj->id, 'userid' => $userid);

        $usersubmissions = $DB->get_record('kalvidassign_submission', $param);

        if ($usersubmissions) {

            if (array_key_exists($userid, $gradedata->menu)) {

                // Update grade
                if (($gradedata->menu[$userid] != $usersubmissions->grade)) {

                    $usersubmissions->grade = $gradedata->menu[$userid];
                    $usersubmissions->timemarked = $time;
                    $usersubmissions->teacher = $USER->id;

                    $updated = true;
                }
            }

            if (array_key_exists($userid, $gradedata->submissioncomment)) {

                if (0 != strcmp($usersubmissions->submissioncomment, $gradedata->submissioncomment[$userid])) {
                    $usersubmissions->submissioncomment = $gradedata->submissioncomment[$userid];

                    $updated = true;

                }
            }

            // trigger grade event
            if ($DB->update_record('kalvidassign_submission', $usersubmissions)) {

                $grade = new stdClass();
                $grade->userid = $userid;
                $grade = kalvidassign_get_submission_grade_object($kalvidassignobj->id, $userid);

                $kalvidassignobj->cmidnumber = $cm->idnumber;

                kalvidassign_grade_item_update($kalvidassignobj, $grade);

            }

        } else {
            // No user submission however the instructor has submitted grade data
            $usersubmissions                = new stdClass();
            $usersubmissions->vidassignid   = $cm->instance;
            $usersubmissions->userid        = $userid;
            $usersubmissions->entry_id      = '';
            $usersubmissions->teacher       = $USER->id;
            $usersubmissions->timemarked    = $time;

            // Need to prevent completely empty submissions from getting entered
            // into the video submissions' table
            // Check for unchanged grade value and an empty feedback value
            $emptygrade = array_key_exists($userid, $gradedata->menu) && '-1' == $gradedata->menu[$userid];

            $emptycomment = array_key_exists($userid, $gradedata->submissioncomment) && empty($gradedata->submissioncomment[$userid]);

            if ($emptygrade && $emptycomment ) {
                continue;
            }

            if (array_key_exists($userid, $gradedata->menu)) {
                $usersubmissions->grade = $gradedata->menu[$userid];
            }

            if (array_key_exists($userid, $gradedata->submissioncomment)) {
                $usersubmissions->submissioncomment = $gradedata->submissioncomment[$userid];
            }

            // trigger grade event
            if ($DB->insert_record('kalvidassign_submission', $usersubmissions)) {

                $grade = new stdClass();
                $grade->userid = $userid;
                $grade = kalvidassign_get_submission_grade_object($kalvidassignobj->id, $userid);

                $kalvidassignobj->cmidnumber = $cm->idnumber;

                kalvidassign_grade_item_update($kalvidassignobj, $grade);

            }

        }

        $updated = false;
    }
}

$renderer->display_submissions_table($cm, $data->group_filter, $data->filter, $data->perpage, $data->quickgrade, $tifirst, $tilast, $page);

$prefform->set_data($data);
$prefform->display();

$PAGE->requires->yui_module('moodle-local_kaltura-ltipanel', 'M.local_kaltura.initreviewsubmission');

echo $OUTPUT->footer();