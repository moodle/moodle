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
 * @package   turnitintooltwo
 * @copyright 2012 iParadigms LLC
 */

define('AJAX_SCRIPT', 1);
global $CFG;

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/lib.php");
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_view.class.php');

require_login();
$action = required_param('action', PARAM_ALPHAEXT);

switch ($action) {
    case "check_anon":
        $assignmentid = required_param('assignment', PARAM_INT);
        $partid = required_param('part', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $part = $turnitintooltwoassignment->get_part_details($partid);

        $anondata = array(
            'anon' => $turnitintooltwoassignment->turnitintooltwo->anon,
            'unanon' => $part->unanon,
            'submitted' => $part->submitted
        );
        echo json_encode($anondata);
        break;

    case "edit_field":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $assignmentid = required_param('assignment', PARAM_INT);
        $partid = required_param('pk', PARAM_INT);
        $return = array();

        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $fieldname = required_param('name', PARAM_ALPHA);
            switch ($fieldname) {
                case 'partname':
                    $fieldvalue = required_param('value', PARAM_TEXT);
                    break;

                case "maxmarks":
                    $fieldvalue = required_param('value', PARAM_RAW);
                    break;

                case "dtstart":
                case "dtdue":
                case "dtpost":
                    $fieldvalue = required_param('value', PARAM_RAW);
                    // We need to work out the users timezone or GMT offset.
                    $usertimezone = get_user_timezone();

                    if (is_numeric($usertimezone)) {
                        if ($usertimezone > 13) {
                            $usertimezone = "";
                        } else if ($usertimezone <= 13 && $usertimezone > 0) {
                            $usertimezone = "GMT+$usertimezone";
                        } else if ($usertimezone < 0) {
                            $usertimezone = "GMT$usertimezone";
                        } else {
                            $usertimezone = 'GMT';
                        }
                    }

                    $fieldvalue = strtotime($fieldvalue.' '.$usertimezone);
                    break;
            }

            $return = $turnitintooltwoassignment->edit_part_field($partid, $fieldname, $fieldvalue);
        } else {
            $return["aaData"] = '';
        }

        $partdetails = $turnitintooltwoassignment->get_parts();

        $unanonymised = ($turnitintooltwoassignment->turnitintooltwo->anon == 0 || time() > $partdetails[$partid]->dtpost);
        $return['export_option'] = ($unanonymised) ? "tii_export_options_show" : "tii_export_options_hide";

        echo json_encode($return);
        break;

    case "useragreement":
        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:read', context_module::instance($cm->id))) {
            $user = new turnitintooltwo_user($USER->id, "Learner");
            echo turnitintooltwo_view::output_dv_launch_form("useragreement", 0, $user->tiiuserid, "Learner", "Submit", true);
        }
        break;

    case "acceptuseragreement":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $message = optional_param('message', '', PARAM_ALPHAEXT);

        // Get the id from the turnitintooltwo_users table so we can update.
        $turnitinuser = $DB->get_record('turnitintooltwo_users', array('userid' => $USER->id));

        // Build user object for update.
        $eulauser = new \stdClass();
        $eulauser->id = $turnitinuser->id;
        $eulauser->user_agreement_accepted = 0;
        if ($message == 'turnitin_eula_accepted') {
            $eulauser->user_agreement_accepted = 1;
        }

        // Update the user using the above object.
        $DB->update_record('turnitintooltwo_users', $eulauser, false);
        break;

    case "downloadoriginal":
    case "default":
    case "origreport":
    case "grademark":
        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:read', context_module::instance($cm->id))) {
            $submissionid = required_param('submission', PARAM_INT);
            $userrole = (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) ? 'Instructor' : 'Learner';

            $user = new turnitintooltwo_user($USER->id, $userrole);

            $launchform = turnitintooltwo_view::output_dv_launch_form($action, $submissionid, $user->tiiuserid, $userrole, '');
            if ($action == 'downloadoriginal') {
                echo $launchform;
            } else {
                $launchform = html_writer::tag("div", $launchform, array('style' => 'display: none'));
                echo json_encode($launchform);
            }
        }
        break;

    case "orig_zip":
    case "xls_inbox":
    case "origchecked_zip":
    case "gmpdf_zip":

        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {

            $partid = optional_param('part', 0, PARAM_INT);
            if ($partid != 0 && ($action == "origchecked_zip" || $action == "gmpdf_zip")) {
                $partdetails = $turnitintooltwoassignment->get_part_details($partid, "moodle");
                $partid = $partdetails->tiiassignid;
            }

            $user = new turnitintooltwo_user($USER->id, 'Instructor');
            $user->edit_tii_user();

            if ($action == "orig_zip") {
                $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
                $partdetails = $turnitintooltwoassignment->get_part_details($partid, "turnitin");
                $submissions = $turnitintooltwoassignment->get_submissions($cm, $partdetails->id);

                $submissionids = array();
                foreach ($submissions[$partdetails->id] as $k => $v) {
                    if (!empty($v->submission_objectid)) {
                        $submissionids[] = $v->submission_objectid;
                    }
                }
            } else {
                $submissionids = optional_param_array('submission_ids', array(), PARAM_INT);
            }

            echo turnitintooltwo_view::output_download_launch_form($action, $user->tiiuserid, $partid, $submissionids);
        }
        break;

    case "get_users":
        $PAGE->set_context(context_system::instance());
        if (is_siteadmin()) {
            echo json_encode(turnitintooltwo_getusers());
        } else {
            throw new moodle_exception('accessdenied', 'admin');
        }
        break;

    case "get_migration_assignments":
        include_once("classes/v1migration/v1migration.php");

        $PAGE->set_context(context_system::instance());
        if (is_siteadmin()) {
            echo json_encode(v1migration::turnitintooltwo_getassignments());
        } else {
            throw new moodle_exception('accessdenied', 'admin');
        }
        break;

    case "initialise_redraw":
        $PAGE->set_context(context_system::instance());
        $return["aaData"] = array();

        echo json_encode($return);
        break;

    case "sync_all_submissions":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }
        raise_memory_limit(MEMORY_EXTRA);

        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $parts = $turnitintooltwoassignment->get_parts();

        foreach ($parts as $part) {
            $i = 0;
            $turnitintooltwoassignment->get_submission_ids_from_tii($part, false);
            $total = count($_SESSION["TiiSubmissions"][$part->id]);

            while ($i < $total) {
                $turnitintooltwoassignment->refresh_submissions($cm, $part, $i, true);
                $i += TURNITINTOOLTWO_SUBMISSION_GET_LIMIT;
            }

            unset($_SESSION["TiiSubmissions"][$part->id]);
        }

        echo json_encode( array('success' => true) );
        break;

    case "get_submissions":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));
        $return = array();

        if (has_capability('mod/turnitintooltwo:read', context_module::instance($cm->id))) {
            $partid = required_param('part', PARAM_INT);
            $refreshrequested = required_param('refresh_requested', PARAM_INT);
            $start = required_param('start', PARAM_INT);
            $total = required_param('total', PARAM_INT);
            $parts = $turnitintooltwoassignment->get_parts();
            $updatefromtii = ($refreshrequested || $turnitintooltwoassignment->turnitintooltwo->autoupdates == 1) ? 1 : 0;
            $istutor = (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) ? true : false;

            if ($refreshrequested && $start == 0) {
                $turnitintooltwoassignment->update_assignment_from_tii();
            }

            if ($updatefromtii && $start == 0) {
                $turnitintooltwoassignment->get_submission_ids_from_tii($parts[$partid]);
                $total = count($_SESSION["TiiSubmissions"][$partid]);
            }

            if ($start < $total && $updatefromtii) {
                $turnitintooltwoassignment->refresh_submissions($cm, $parts[$partid], $start);
            }

            $PAGE->set_context(context_module::instance($cm->id));
            $turnitintooltwoview = new turnitintooltwo_view();

            $view = $turnitintooltwoview->get_submission_inbox($cm, $turnitintooltwoassignment, $parts, $partid, $start);
            $return["aaData"] = $view;
            $totalsubmitters = $DB->count_records('turnitintooltwo_submissions',
                                                    array('turnitintooltwoid' => $turnitintooltwoassignment->turnitintooltwo->id,
                                                            'submission_part' => $partid));
            $return["end"] = $start + TURNITINTOOLTWO_SUBMISSION_GET_LIMIT;
            $return["total"] = $_SESSION["num_submissions"][$partid];

            // Remove any leftover submissions from session and update grade timestamp.
            if ($return["end"] >= $return["total"]) {
                unset($_SESSION["submissions"][$partid]);

                // Only update the timestamp if an instructor has refreshed.
                if ( $istutor ) {
                    $updatepart = new stdClass();
                    $updatepart->id = $partid;
                    // Set timestamp to 10 minutes ago to account for time taken to complete (somewhat exagerrated).
                    $updatepart->gradesupdated = time() - (60 * 10);
                    $DB->update_record('turnitintooltwo_parts', $updatepart);
                }
            }
        } else {
            $return["aaData"] = '';
        }

        echo json_encode($return);
        break;

    case "refresh_user_messages":
        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $turnitintooltwouser = new turnitintooltwo_user($USER->id, 'Instructor');
            $turnitintooltwouser->set_user_values_from_tii();

            echo $turnitintooltwouser->get_user_messages();
        } else {
            echo 0;
        }
        break;

    case "refresh_peermark_assignments":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:read', context_module::instance($cm->id))) {
            $partid = required_param('part', PARAM_INT);
            $refreshrequested = optional_param('refresh_requested', 0, PARAM_INT);
            $partdetails = $turnitintooltwoassignment->get_part_details($partid);

            if ($refreshrequested) {
                $turnitintooltwoassignment->update_assignment_from_tii(array($partdetails->tiiassignid));
                $partdetails = $turnitintooltwoassignment->get_part_details($partid);
            }

            $PAGE->set_context(context_module::instance($cm->id));

            $turnitintooltwoview = new turnitintooltwo_view();
            $peermarkdata['peermark_table'] = $turnitintooltwoview->show_peermark_assignment($partdetails->peermark_assignments);
            $peermarkdata['no_of_peermarks'] = count($partdetails->peermark_assignments);
            $peermarkdata['peermarks_active'] = false;
            foreach ($partdetails->peermark_assignments as $peermarkassignment) {
                if (time() > $peermarkassignment->dtstart) {
                    $peermarkdata['peermarks_active'] = true;
                    break;
                }
            }
            echo json_encode($peermarkdata);
        }
        break;

    case "refresh_submission_row":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:read', context_module::instance($cm->id))) {
            $partid = required_param('part', PARAM_INT);
            $userid = required_param('user', PARAM_INT);
            $istutor = (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) ? true : false;

            $parts = $turnitintooltwoassignment->get_parts();

            // Get the id of the submission in the row and update it from Turnitin then get the new details.
            $submission = $turnitintooltwoassignment->get_user_submissions($userid, $assignmentid, $partid);
            $submissionid = current(array_keys($submission));

            if (!empty($submissionid)) {
                $submission = new turnitintooltwo_submission($submissionid);
                $submission->update_submission_from_tii(true);

                // Get the submission details again in case the submission has been transferred within Turnitin.
                $submission = $turnitintooltwoassignment->get_user_submissions($userid, $assignmentid, $partid);
                $submissionid = current(array_keys($submission));
            }

            $submission = new turnitintooltwo_submission($submissionid);
            if (empty($submissionid)) {
                $user = new turnitintooltwo_user($userid, 'Learner', false);

                $submission->firstname = $user->firstname;
                $submission->lastname = $user->lastname;
                $submission->fullname = $user->fullname;
                $submission->userid = $user->id;
            }
            // Check if student is actually enrolled in the Moodle course.
            if ( !is_enrolled(context_module::instance($cm->id), $submission->userid, '', true) ) {
                $submission->nmoodle = 1;

                $submission->firstname = $submission->submission_nmfirstname;
                $submission->lastname = $submission->submission_nmlastname;
                $submission->fullname = $submission->firstname.' '.$submission->lastname;
            }

            $useroverallgrades = array();

            $PAGE->set_context(context_module::instance($cm->id));

            $turnitintooltwoview = new turnitintooltwo_view();
            $submissionrow["submission_id"] = $submission->submission_objectid;
            $submissionrow["row"] = $turnitintooltwoview->get_submission_inbox_row($cm, $turnitintooltwoassignment, $parts,
                                                                                $partid, $submission, $useroverallgrades,
                                                                                $istutor, 'refresh_row');

            echo json_encode($submissionrow);
        }
        break;

    case "enrol_all_students":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            echo $turnitintooltwoassignment->enrol_all_students($cm);
        }
        break;

    case "refresh_rubric_select":
        $courseid = required_param('course', PARAM_INT);
        $assignmentid = required_param('assignment', PARAM_INT);
        $modulename = required_param('modulename', PARAM_ALPHA);

        $PAGE->set_context(context_course::instance($courseid));

        if (has_capability('moodle/course:update', context_course::instance($courseid))) {
            // Set Rubric options to instructor rubrics.
            $instructor = new turnitintooltwo_user($USER->id, 'Instructor');
            $instructor->set_user_values_from_tii();
            $instructorrubrics = $instructor->get_instructor_rubrics();

            $options = array('' => get_string('norubric', 'turnitintooltwo')) + $instructorrubrics;

            // Get rubrics that are shared on the Turnitin account.
            if ($modulename == "turnitintooltwo") {
                $turnitinclass = new turnitintooltwo_class($courseid);
            } else {
                require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');
                $turnitinclass = new turnitin_class($courseid);
            }
            $turnitinclass->read_class_from_tii();
            $sharedrubrics = $turnitinclass->sharedrubrics;

            foreach ($sharedrubrics as $group => $grouprubrics) {
                foreach ($grouprubrics as $rubricid => $rubricname) {
                    $options[$group][$rubricid] = $rubricname;
                }
            }

            // Get assignment details.
            if (!empty($assignmentid)) {
                if ($modulename == "turnitintooltwo") {
                    $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
                } else {
                    $pluginturnitin = new plagiarism_plugin_turnitin();
                    $cm = get_coursemodule_from_instance($modulename, $assignmentid);
                    $plagiarismsettings = $pluginturnitin->get_settings($cm->id);
                }
            }

            // Add in selected rubric if it belongs to another instructor.
            if (!empty($assignmentid)) {
                if ($modulename == "turnitintooltwo") {
                    if (!empty($turnitintooltwoassignment->turnitintooltwo->rubric)) {
                        if (isset($options[$turnitintooltwoassignment->turnitintooltwo->rubric])) {
                            $rubricname = $options[$turnitintooltwoassignment->turnitintooltwo->rubric];
                        } else {
                            $rubricname = get_string('otherrubric', 'turnitintooltwo');
                        }
                        $options[$turnitintooltwoassignment->turnitintooltwo->rubric] = $rubricname;
                    }
                } else {
                    if (!empty($plagiarismsettings["plagiarism_rubric"])) {
                        if (isset($options[$plagiarismsettings["plagiarism_rubric"]])) {
                            $rubricname = $options[$plagiarismsettings["plagiarism_rubric"]];
                        } else {
                            $rubricname = get_string('otherrubric', 'turnitintooltwo');
                        }
                        $options[$plagiarismsettings["plagiarism_rubric"]] = $rubricname;
                    }
                }
            }
        } else {
            $options = array();
        }

        echo json_encode($options);
        break;

    case "get_files":
        $PAGE->set_context(context_system::instance());
        if (is_siteadmin()) {
            $modules = $DB->get_record('modules', array('name' => 'turnitintooltwo'));
            echo json_encode(turnitintooltwo_getfiles($modules->id));
        }
        break;

    case "get_members":
        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));

        $return["aaData"] = array();
        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $role = required_param('role', PARAM_ALPHA);
            $members = $turnitintooltwoassignment->get_tii_users_by_role($role);

            $PAGE->set_context(context_module::instance($cm->id));
            $turnitintooltwoview = new turnitintooltwo_view();
            $return["aaData"] = $turnitintooltwoview->get_tii_members_by_role($cm, $turnitintooltwoassignment, $members, $role);
        }
        echo json_encode($return);
        break;

    case "reveal_submission_name":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $assignmentid = required_param('assignment', PARAM_INT);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);
        $PAGE->set_context(context_module::instance($cm->id));
        $return = array("status" => "fail", "msg" => get_string('unanonymiseerror', 'turnitintooltwo'));

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $submissionid = required_param('submission_id', PARAM_INT);
            $reason = optional_param('reason', get_string('noreason', 'turnitintooltwo'), PARAM_TEXT);

            $turnitintooltwosubmission = new turnitintooltwo_submission($submissionid, "turnitin");
            if ($turnitintooltwosubmission->unanonymise_submission($reason)) {
                if ($turnitintooltwosubmission->userid == 0) {
                    $tmpuser = new stdClass();
                    $tmpuser->firstname = $turnitintooltwosubmission->firstname;
                    $tmpuser->lastname = $turnitintooltwosubmission->lastname;
                    $return["name"] = fullname($tmpuser);
                } else {
                    $return["name"] = $turnitintooltwosubmission->fullname;
                }
                $return["status"] = "success";
                $return["userid"] = $turnitintooltwosubmission->userid;
                $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
                $return["courseid"] = $turnitintooltwoassignment->turnitintooltwo->course;
                $return["msg"] = "";
            }

            // Refresh submission and save.
            $turnitintooltwosubmission->update_submission_from_tii();
        }

        echo json_encode($return);
        break;

    case "search_classes":
        $PAGE->set_context(context_system::instance());
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $title = optional_param('course_title', '', PARAM_TEXT);
        $courseintegration = optional_param('course_integration', '', PARAM_ALPHANUM);
        $enddate = optional_param('course_end_date', null, PARAM_TEXT);
        $source = optional_param('request_source', 'mod', PARAM_TEXT);

        $modules = $DB->get_record('modules', array('name' => 'turnitintooltwo'));

        $return = turnitintooltwo_get_courses_from_tii(turnitintooltwo_get_integration_ids(), $title, $courseintegration, $enddate, $source);
        echo json_encode($return);
        break;

    case "create_courses":
        $PAGE->set_context(context_system::instance());
        set_time_limit(0);
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        if (has_capability('moodle/course:create', context_system::instance())) {
            $coursecategory = optional_param('course_category', 0, PARAM_INT);
            $createassignments = optional_param('create_assignments', 0, PARAM_INT);
            $classids = required_param('class_ids', PARAM_SEQUENCE);
            $classids = explode(",", $classids);

            $i = 0;
            foreach ($classids as $tiiclassid) {
                $coursename = $_SESSION['tii_classes'][$tiiclassid];

                $course = turnitintooltwo_assignment::create_moodle_course($tiiclassid, $coursename, $coursename, $coursecategory);
                if ($createassignments == 1 && !empty($course)) {
                    $return = turnitintooltwo_get_assignments_from_tii($tiiclassid, "raw");

                    foreach ($return as $assignment) {
                        turnitintooltwo_assignment::create_migration_assignment(array($assignment["tii_id"]),
                                                                                $course->id, $assignment["tii_title"]);
                    }
                }
                $i++;
            }

            $result = new stdClass();
            $result->completed = $i;
            $result->total = count($classids);
            $msg = get_string('recreatemulticlassescomplete', 'turnitintooltwo', $result);
        } else {
            $msg = get_string('nopermissions', 'error', get_string('course:create', 'role'));
        }

        echo $msg;
        break;

    case "create_course":
        $PAGE->set_context(context_system::instance());
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        if (has_capability('moodle/course:create', context_system::instance())) {
            $tiicoursename = optional_param('tii_course_name', get_string('defaultcoursetiititle', 'turnitintooltwo'), PARAM_TEXT);
            $coursecategory = optional_param('course_category', 0, PARAM_INT);
            $tiicourseid = optional_param('tii_course_id', 0, PARAM_INT);
            $coursename = urldecode(optional_param('course_name', '', PARAM_TEXT));
            if (empty($coursename)) {
                $coursename = get_string('defaultcoursetiititle', 'turnitintooltwo')." (".$tiicourseid.")";
            }

            $course = turnitintooltwo_assignment::create_moodle_course($tiicourseid, urldecode($tiicoursename),
                                                                        $coursename, $coursecategory);

            $newcourse = array('courseid' => $course->id, 'coursename' => $course->fullname);
            echo json_encode($newcourse);
        } else {
            throw new moodle_exception('nopermissions', 'error', '', get_string('course:create', 'role'));
        }
        break;

    case "link_course":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        if (has_capability('moodle/course:update', context_system::instance())) {
            $tiicoursename = optional_param('tii_course_name', get_string('defaultcoursetiititle', 'turnitintooltwo'), PARAM_TEXT);
            $tiicourseid = optional_param('tii_course_id', 0, PARAM_INT);
            $coursetolink = optional_param('course_to_link', 0, PARAM_INT);

            $turnitincourse = new stdClass();
            $turnitincourse->courseid = $coursetolink;
            $turnitincourse->ownerid = $USER->id;
            $turnitincourse->turnitin_cid = $tiicourseid;
            $turnitincourse->turnitin_ctl = urldecode($tiicoursename);
            $turnitincourse->course_type = 'TT';

            $PAGE->set_context(context_system::instance($coursetolink));

            if (!$insertid = $DB->insert_record('turnitintooltwo_courses', $turnitincourse)) {
                echo "0";
            } else {
                $course = $DB->get_record("course", array("id" => $coursetolink), 'fullname');
                $newcourse = array('courseid' => $coursetolink, 'coursename' => $course->fullname);

                echo json_encode($newcourse);
            }
        } else {
            throw new moodle_exception('nopermissions', 'error', '', get_string('course:update', 'role'));
        }
        break;

    case "get_assignments":
        set_time_limit(0);
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $PAGE->set_context(context_system::instance());

        if (has_capability('moodle/course:update', context_system::instance())) {
            $tiicourseid = required_param('tii_course_id', PARAM_INT);
            $return = turnitintooltwo_get_assignments_from_tii($tiicourseid, "json");
            $return["number_of_assignments"] = count($return["aaData"]);
        } else {
            $return["number_of_assignments"] = 0;
        }
        echo json_encode($return);
        break;

    case "create_assignment":
        set_time_limit(0);
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        if (has_capability('mod/turnitintooltwo:addinstance', context_system::instance())) {
            $partids = required_param('parts', PARAM_SEQUENCE);
            $courseid = optional_param('course_id', 0, PARAM_INT);
            $assignmentname = optional_param('assignment_name', '', PARAM_TEXT);
            if (empty($assignmentname)) {
                $assignmentname = get_string('defaultassignmenttiititle', 'turnitintooltwo');
            } else {
                $assignmentname = urldecode($assignmentname);
            }

            $partids = explode(',', $partids);
            if (is_array($partids)) {
                turnitintooltwo_assignment::create_migration_assignment($partids, $courseid, $assignmentname);
            }
        }
        break;

    case "edit_course_end_date":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        if (has_capability('moodle/course:update', context_system::instance())) {
            $tiicourseid = required_param('tii_course_id', PARAM_INT);
            $tiicoursetitle = required_param('tii_course_title', PARAM_TEXT);
            $enddated = required_param('end_date_d', PARAM_INT);
            $enddatem = required_param('end_date_m', PARAM_INT);
            $enddatey = required_param('end_date_y', PARAM_INT);

            $enddate = mktime(00, 00, 00, $enddatem, $enddated, $enddatey);

            $PAGE->set_context(context_system::instance());

            if (turnitintooltwo_assignment::edit_tii_course_end_date($tiicourseid, $tiicoursetitle, $enddate)) {
                $return["status"] = "success";
                $return["end_date"] = userdate($enddate, get_string('strftimedate', 'langconfig'));
            } else {
                $return["status"] = "fail";
                $return["msg"] = get_string('unanonymiseerror', 'turnitintooltwo');
            }
        } else {
            $return["status"] = "fail";
            $return["msg"] = get_string('nopermissions', 'error', get_string('course:update', 'role'));
        }
        echo json_encode($return);
        break;

    case "test_connection":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }
        $data = array("connection_status" => "fail", "msg" => get_string('connecttestcommerror', 'turnitintooltwo'));

        $PAGE->set_context(context_system::instance());
        if (is_siteadmin()) {
            // Initialise API connection.

            $accountid = required_param('accountid', PARAM_RAW);
            $accountshared = required_param('accountshared', PARAM_RAW);
            $url = required_param('url', PARAM_RAW);

            $turnitincomms = new turnitintooltwo_comms($accountid, $accountshared, $url);

            $testingconnection = true; // Provided by Androgogic to override offline mode for testing connection.

            // We only want an API log entry for this if diagnostic mode is set to Debugging.
            if (empty($config)) {
                $config = turnitintooltwo_admin_config();
            }
            if ($config->enablediagnostic != 2) {
                $turnitincomms->set_diagnostic(0);
            }

            $tiiapi = $turnitincomms->initialise_api($testingconnection);

            $class = new TiiClass();
            $class->setTitle('Test finding a class to see if connection works');

            try {
                $response = $tiiapi->findClasses($class);
                $data["connection_status"] = "success";
                $data["msg"] = get_string('connecttestsuccess', 'turnitintooltwo');
            } catch (Exception $e) {
                $turnitincomms->handle_exceptions($e, 'connecttesterror', false);
            }
        }
        echo json_encode($data);
        break;

    case "submit_nothing":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $assignmentid = required_param('assignment', PARAM_INT);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);

        $PAGE->set_context(context_system::instance());

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $partid = required_param('part', PARAM_INT);
            $userid = required_param('user', PARAM_INT);
            $turnitintooltwosubmission = new turnitintooltwo_submission();
            $data = $turnitintooltwosubmission->do_tii_nothing_submission($cm, $turnitintooltwoassignment, $partid, $userid);
        } else {
            header("HTTP/1.0 403 Forbidden");
            exit();
        }
        if ( !is_array( $data ) ) {
            header("HTTP/1.0 400 Bad Request");
            echo $data;
            exit();
        } else {
            echo json_encode($data);
        }
        break;

    case "deletesubmission":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $submissionid = required_param('paper', PARAM_INT);
        $part = required_param('part', PARAM_INT);
        $assignmentid = required_param('assignment', PARAM_INT);

        $turnitintooltwoassignment = new turnitintooltwo_assignment($assignmentid);
        $turnitintooltwosubmission = new turnitintooltwo_submission($submissionid, "moodle", $turnitintooltwoassignment);

        $cm = get_coursemodule_from_instance("turnitintooltwo", $assignmentid);

        if (has_capability('mod/turnitintooltwo:read', context_module::instance($cm->id))) {
            $istutor = (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) ? true : false;
        }

        // Allow instructors to delete submission and students to delete if the submission hasn't gone to Turnitin.
        if (($istutor && $submissionid != 0) ||
            ($USER->id == $turnitintooltwosubmission->userid && empty($turnitintooltwosubmission->submission_objectid))) {
            $_SESSION["notice"] = $turnitintooltwosubmission->delete_submission();
        }
        exit();
        break;

    case "begin_migration":

        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $courseid = required_param('courseid', PARAM_INT);
        $turnitintoolid = required_param('turnitintoolid', PARAM_INT);

        include_once("classes/v1migration/v1migration.php");
        $turnitintool = $DB->get_record("turnitintool", array("id" => $turnitintoolid));

        $v1migration = new v1migration($courseid, $turnitintool);

        try {
            $turnitintooltwoid = $v1migration->migrate();
            $cm = get_coursemodule_from_instance("turnitintooltwo", $turnitintooltwoid);

            // The returned CMID will be used for the redirect.
            if ((int)$turnitintooltwoid > 0) {
                echo '{ "id": '.$cm->id.' }';
                exit();
            }
        } catch(Exception $e) {
            header("HTTP/1.0 400 Bad Request");

            $errorresponse = array(
                'error' => get_string('migrationtoolerror', 'turnitintooltwo'),
                'message' => $e->getMessage()
            );

            if ($CFG->debug == DEBUG_DEVELOPER) {
                $errorresponse = array_merge($errorresponse, array(
                    'exception' => $e,
                    'trace' => $e->getTrace()
                ));
            }

            echo json_encode($errorresponse);
            exit();
        }

    case "check_migrated":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $turnitintoolid = required_param('turnitintoolid', PARAM_INT);

        // Check if v1 id is linked to a v2 id in the session.
        $turnitintooltwoid = 0;
        if ( isset( $_SESSION["migrationtool"][$turnitintoolid] ) && is_numeric( $_SESSION["migrationtool"][$turnitintoolid] ) ) {
            $turnitintooltwoid = intval( $_SESSION["migrationtool"][$turnitintoolid] );
        }

        if ( $turnitintooltwoid != 0 ) {
            $cm = get_coursemodule_from_instance("turnitintooltwo", $turnitintooltwoid);
            echo json_encode(array(
                    'migrated' => true,
                    'v2id' => $cm->id
            ));
        } else {
            echo json_encode(array(
                    'migrated' => false
            ));
        }
}
