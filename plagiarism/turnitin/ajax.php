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

use Integrations\PhpSdk\TiiClass;

require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_assignment.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_user.class.php');

require_login();

$action = required_param('action', PARAM_ALPHAEXT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);
if ( !empty( $cmid ) ) {
    $cm = get_coursemodule_from_id('', $cmid);
    $context = context_course::instance($cm->course);

    // Work out user role.
    $userrole = '';
    switch ($cm->modname) {
        case "forum":
        case "workshop":
            $userrole = (has_capability('plagiarism/turnitin:viewfullreport', $context)) ? 'Instructor' : 'Learner';
            break;
        default:
            $userrole = (has_capability('mod/'.$cm->modname.':grade', $context)) ? 'Instructor' : 'Learner';
            break;
    }
}

$pathnamehash = optional_param('pathnamehash', "", PARAM_ALPHANUM);
$submissiontype = optional_param('submission_type', "", PARAM_ALPHAEXT);
$return = array();

// Initialise plugin class.
$pluginturnitin = new plagiarism_plugin_turnitin();

switch ($action) {
    case "get_dv_html":
        $submissionid = required_param('submissionid', PARAM_INT);
        $dvtype = optional_param('dvtype', 'default', PARAM_ALPHAEXT);
        $user = new turnitin_user($USER->id, $userrole);
        $coursedata = turnitin_assignment::get_course_data($cm->course);

        if ($userrole == 'Instructor') {
            $user->join_user_to_class($coursedata->turnitin_cid);
        }

        // Update course data in Turnitin.
        $turnitinassignment = new turnitin_assignment(0);
        $turnitinassignment->edit_tii_course($coursedata);

        // Edit assignment in Turnitin in case any changes have been made that would affect DV.
        $pluginturnitin = new plagiarism_plugin_turnitin();
        $syncassignment = $pluginturnitin->sync_tii_assignment($cm, $coursedata->turnitin_cid);

        if ($syncassignment['success']) {
            $return = html_writer::tag(
                "div",
                turnitin_view::output_launch_form(
                    $dvtype,
                    $submissionid,
                    $user->tiiuserid,
                    $userrole,
                    ''
                ),
                array('style' => 'display: none')
            );
        }
        break;

    case "update_grade":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        include_once($CFG->libdir."/gradelib.php");

        $submissionid = optional_param('submission', 0, PARAM_INT);

        if ($userrole == 'Instructor') {
            $pluginturnitin->update_rubric_from_tii($cm);
            $return["status"] = $pluginturnitin->update_grades_from_tii($cm);

            $moduleconfigvalue = new stdClass();
            $moduleconfigvalue->value = time();

            // If we have a turnitin timestamp stored then update it, otherwise create it.
            if ($timestampid = $DB->get_record('plagiarism_turnitin_config',
                                        array('cm' => $cm->id, 'name' => 'grades_last_synced'), 'id')) {
                $moduleconfigvalue->id = $timestampid->id;
                $DB->update_record('plagiarism_turnitin_config', $moduleconfigvalue);
            } else {
                $moduleconfigvalue->cm = $cm->id;
                $moduleconfigvalue->name = 'grades_last_synced';
                $moduleconfigvalue->config_hash = $moduleconfigvalue->cm."_".$moduleconfigvalue->name;
                $DB->insert_record('plagiarism_turnitin_config', $moduleconfigvalue);
            }

        } else {
            $return["status"] = $pluginturnitin->update_grade_from_tii($cm, $submissionid);
        }
        break;

    case "refresh_peermark_assignments":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $tiiassignment = $DB->get_record('plagiarism_turnitin_config', array('cm' => $cm->id, 'name' => 'turnitin_assignid'));
        $pluginturnitin->refresh_peermark_assignments($cm, $tiiassignment->value);
        break;

    case "peermarkmanager":

        if ($userrole == 'Instructor') {
            $plagiarismpluginturnitin = new plagiarism_plugin_turnitin();
            $coursedata = $plagiarismpluginturnitin->get_course_data($cm->id, $cm->course);

            $tiiassignment = $DB->get_record('plagiarism_turnitin_config', array('cm' => $cm->id, 'name' => 'turnitin_assignid'));

            if ($tiiassignment) {
                $tiiassignmentid = $tiiassignment->value;
            } else {
                // Create the module as an assignment in Turnitin.
                $tiiassignment = $pluginturnitin->sync_tii_assignment($cm, $coursedata->turnitin_cid);
                $tiiassignmentid = $tiiassignment['tiiassignmentid'];
            }

            $user = new turnitin_user($USER->id, "Instructor");
            $user->join_user_to_class($coursedata->turnitin_cid);

            echo html_writer::tag(
                'div',
                turnitin_view::output_lti_form_launch('peermark_manager', 'Instructor', $tiiassignmentid),
                array(
                    'class' => 'launch_form',
                    'style' => 'display:none;'
                )
            );

            echo html_writer::script("<!--
                                    window.document.forms[0].submit();
                                    //-->");
        }
        break;

    case "rubricview":
        $replypost = 'mod/'.$cm->modname.':replypost';
        $submit = 'mod/'.$cm->modname.':submit';
        $isstudent = ($cm->modname == "forum") ? has_capability($replypost, $context) : has_capability($submit, $context);

        if ($isstudent) {
            $tiiassignment = $DB->get_record('plagiarism_turnitin_config', array('cm' => $cm->id, 'name' => 'turnitin_assignid'));

            $user = new turnitin_user($USER->id, "Learner");
            $coursedata = turnitin_assignment::get_course_data($cm->course);
            $user->join_user_to_class($coursedata->turnitin_cid);

            echo html_writer::tag(
                'div',
                turnitin_view::output_lti_form_launch('rubric_view', 'Learner', $tiiassignment->value),
                array(
                    'class' => 'launch_form',
                    'style' => 'display:none;'
                )
            );

            echo html_writer::script("<!--
                                    window.document.forms[0].submit();
                                    //-->");
        }
        break;

    case "peermarkreviews":
        $replypost = 'mod/'.$cm->modname.':replypost';
        $submit = 'mod/'.$cm->modname.':submit';
        $isstudent = ($cm->modname == "forum") ? has_capability($replypost, $context) : has_capability($submit, $context);

        if ($userrole == 'Instructor' || $isstudent) {
            $tiiassignment = $DB->get_record('plagiarism_turnitin_config', array('cm' => $cm->id, 'name' => 'turnitin_assignid'));

            $user = new turnitin_user($USER->id, $userrole);
            $coursedata = turnitin_assignment::get_course_data($cm->course);
            $user->join_user_to_class($coursedata->turnitin_cid);

            echo html_writer::tag(
                'div',
                turnitin_view::output_lti_form_launch('peermark_reviews', $userrole, $tiiassignment->value),
                array(
                    'class' => 'launch_form',
                    'style' => 'display:none;'
                )
            );

            echo html_writer::script("<!--
                                    window.document.forms[0].submit();
                                    //-->");
        }
        break;

    case "actionuseragreement":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $message = optional_param('message', '', PARAM_ALPHAEXT);

        // Get the id from the plagiarism_turnitin_users table so we can update.
        $turnitinuser = $DB->get_record('plagiarism_turnitin_users', array('userid' => $USER->id));

        // Build user object for update.
        $eulauser = new stdClass();
        $eulauser->id = $turnitinuser->id;
        $eulauser->user_agreement_accepted = 0;
        if ($message == 'turnitin_eula_accepted') {
            $eulauser->user_agreement_accepted = 1;
            $logstring = "User ".$USER->id." (".$turnitinuser->turnitin_uid.") accepted the EULA.";
            plagiarism_turnitin_activitylog($logstring, "PP_EULA_ACCEPTANCE");
        } else if ($message == 'turnitin_eula_declined') {
            $eulauser->user_agreement_accepted = -1;
            $logstring = "User ".$USER->id." (".$turnitinuser->turnitin_uid.") declined the EULA.";
            plagiarism_turnitin_activitylog($logstring, "PP_EULA_ACCEPTANCE");
        }

        // Update the user using the above object.
        $DB->update_record('plagiarism_turnitin_users', $eulauser, $bulk = false);
        break;

    case "resubmit_event":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $forumdata = optional_param('forumdata', '', PARAM_ALPHAEXT);
        $forumpost = optional_param('forumpost', '', PARAM_ALPHAEXT);
        $submissionid = required_param('submissionid', PARAM_INT);

        $tiisubmission = new turnitin_submission($submissionid,
                                                array('forumdata' => $forumdata, 'forumpost' => $forumpost));

        if ($tiisubmission->recreate_submission_event()) {
            $return = array('success' => true);
        }
        break;

    case "resubmit_events":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $submissionids = optional_param_array('submission_ids', array(), PARAM_INT);

        $submissionids = optional_param_array('submission_ids', array(), PARAM_INT);
        $errors = array();
        $return['success'] = true;
        foreach ($submissionids as $submissionid) {
            $tiisubmission = new turnitin_submission($submissionid);
            if (!$tiisubmission->recreate_submission_event()) {
                $return['success'] = false;
                $errors[] = $submissionid;
            }
        }
        $return['errors'] = $errors;
        break;

    case "test_connection":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }
        $data = array("connection_status" => "fail", "msg" => get_string('connecttestcommerror', 'plagiarism_turnitin'));

        $PAGE->set_context(context_system::instance());
        if (is_siteadmin()) {
            // Initialise API connection.

            $accountid = required_param('accountid', PARAM_RAW);
            $accountshared = required_param('accountshared', PARAM_RAW);
            $url = required_param('url', PARAM_RAW);

            $turnitincomms = new turnitin_comms($accountid, $accountshared, $url);

            // We only want an API log entry for this if diagnostic mode is set to Debugging.
            if (empty($config)) {
                $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
            }
            if (!isset($config->plagiarism_turnitin_enablediagnostic)) {
                $turnitincomms->set_diagnostic(0);
            } else {
                if ($config->plagiarism_turnitin_enablediagnostic != 2) {
                    $turnitincomms->set_diagnostic(0);
                }
            }

            $tiiapi = $turnitincomms->initialise_api(true);

            $class = new TiiClass();
            $class->setTitle('Test finding a class to see if connection works');

            try {
                $response = $tiiapi->findClasses($class);
                $data["connection_status"] = 200;
                $data["msg"] = get_string('connecttestsuccess', 'plagiarism_turnitin');
            } catch (Exception $e) {
                $turnitincomms->handle_exceptions($e, 'connecttesterror', false);
            }
        }
        echo json_encode($data);
        break;

    case "get_users":
        $PAGE->set_context(context_system::instance());
        if (is_siteadmin()) {
            echo json_encode(turnitin_user::plagiarism_turnitin_getusers());
        } else {
            throw new moodle_exception('accessdenied', 'admin');
        }
        break;

    case "refresh_rubric_select":
        $courseid = required_param('course', PARAM_INT);
        $assignmentid = required_param('assignment', PARAM_INT);
        $modulename = required_param('modulename', PARAM_ALPHA);

        $PAGE->set_context(context_course::instance($courseid));

        if (has_capability('moodle/course:update', context_course::instance($courseid))) {
            // Set Rubric options to instructor rubrics.
            $instructor = new turnitin_user($USER->id, 'Instructor');
            $instructor->set_user_values_from_tii();
            $instructorrubrics = $instructor->get_instructor_rubrics();

            $options = array(0 => get_string('norubric', 'plagiarism_turnitin')) + $instructorrubrics;

            // Get rubrics that are shared on the Turnitin account.
            $turnitinclass = new turnitin_class($courseid);

            $turnitinclass->read_class_from_tii();
            $sharedrubrics = $turnitinclass->sharedrubrics;

            foreach ($sharedrubrics as $group => $grouprubrics) {
                foreach ($grouprubrics as $rubricid => $rubricname) {
                    $options[$group][$rubricid] = $rubricname;
                }
            }

            // Get assignment details.
            if (!empty($assignmentid)) {
                $cm = get_coursemodule_from_instance($modulename, $assignmentid);
                $plagiarismsettings = $pluginturnitin->get_settings($cm->id);
            }

            // Add in selected rubric if it belongs to another instructor.
            if (!empty($assignmentid)) {
                if (!empty($plagiarismsettings["plagiarism_rubric"])) {
                    if (isset($options[$plagiarismsettings["plagiarism_rubric"]])) {
                        $rubricname = $options[$plagiarismsettings["plagiarism_rubric"]];
                    } else {
                        $rubricname = get_string('otherrubric', 'plagiarism_turnitin');
                    }
                    $options[$plagiarismsettings["plagiarism_rubric"]] = $rubricname;
                }
            }
        } else {
            $options = array();
        }

        echo json_encode($options);
        break;
}

if (!empty($return)) {
    echo json_encode($return);
}
