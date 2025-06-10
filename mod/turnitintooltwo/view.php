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

require_once("../../config.php");
require_once(__DIR__."/lib.php");
require_once($CFG->libdir."/formslib.php");
require_once($CFG->libdir."/form/text.php");
require_once($CFG->libdir."/form/datetimeselector.php");
require_once($CFG->libdir."/form/hidden.php");
require_once($CFG->libdir."/form/button.php");
require_once($CFG->libdir."/form/submit.php");
require_once($CFG->libdir."/uploadlib.php");

// views
require_once(__DIR__.'/classes/view/members.php');

// Offline mode provided by Androgogic. Set tiioffline in config.php.
if (!empty($CFG->tiioffline)) {
    turnitintooltwo_print_error('turnitintoolofflineerror', 'turnitintooltwo');
}

require_once(__DIR__."/turnitintooltwo_view.class.php");
$turnitintooltwoview = new turnitintooltwo_view();

require_once(__DIR__.'/classes/nonsubmitters/nonsubmitters_message.php');
$nonsubmitters = new nonsubmitters_message();

// Get/Set variables and work out which function to perform.
$id = required_param('id', PARAM_INT); // Course Module ID.
$a = optional_param('a', 0, PARAM_INT); // Turnitintooltwo ID.
$part = optional_param('part', 0, PARAM_INT); // Part ID.
$user = optional_param('user', 0, PARAM_INT); // User ID.
$do = optional_param('do', "submissions", PARAM_ALPHAEXT);
$action = optional_param('action', "", PARAM_ALPHA);
$viewcontext = optional_param('view_context', "window", PARAM_ALPHAEXT);
$migrated = optional_param('migrated', 0, PARAM_INT); // Migrated

// If v1 migration tool is being enabled then this will prompt user to migrate when
// they return to the previous v1 assignment.
$_SESSION["migrationtool"]["lastasked"] = 0;

$notice = null;
if (isset($_SESSION["notice"])) {
    $notice = $_SESSION["notice"];
    $notice["type"] = (empty($_SESSION["notice"]["type"])) ? "general" : $_SESSION["notice"]["type"];
    unset($_SESSION["notice"]);
}

if ($id) {
    list($course, $cm) = get_course_and_cm_from_cmid($id, 'turnitintooltwo');

    if (!$cm) {
        turnitintooltwo_print_error('coursemodidincorrect', 'turnitintooltwo');
    }
    if (!$course = $DB->get_record("course", array("id" => $cm->course))) {
        turnitintooltwo_print_error('coursemisconfigured', 'turnitintooltwo');
    }
    if (!$turnitintooltwo = $DB->get_record("turnitintooltwo", array("id" => $cm->instance))) {
        turnitintooltwo_print_error('coursemodincorrect', 'turnitintooltwo');
    }
} else {
    if (!$turnitintooltwo = $DB->get_record("turnitintooltwo", array("id" => $a))) {
        turnitintooltwo_print_error('coursemodincorrect', 'turnitintooltwo');
    }
    if (!$course = $DB->get_record("course", array("id" => $turnitintooltwo->course))) {
        turnitintooltwo_print_error('coursemisconfigured', 'turnitintooltwo');
    }
    if (!$cm = get_coursemodule_from_instance("turnitintooltwo", $turnitintooltwo->id, $course->id)) {
        turnitintooltwo_print_error('coursemodidincorrect', 'turnitintooltwo');
    }
}

// If opening DV then $viewcontext needs to be set to box.
$viewcontext = ($do == "origreport" || $do == "grademark" || $do == "default") ? "box" : $viewcontext;

require_login($course->id, true, $cm);

// Check if the user has the capability to view the page - used when an assignment is set to hidden.
$context = context_module::instance($cm->id);
require_capability('mod/turnitintooltwo:view', $context);

// Set the page layout to incourse - to make it full width.
$PAGE->set_pagelayout('incourse');
$PAGE->set_cm($cm);
$config = turnitintooltwo_admin_config();

// Don't show messages popup if we are in submission modal.
$forbiddenmsgscreens = array('submission_success', 'submitpaper');
if (in_array($do, $forbiddenmsgscreens)) {
    $PAGE->set_popup_notification_allowed(false);
}

// Configure URL correctly.
$urlparams = array('id' => $id, 'a' => $a, 'part' => $part, 'user' => $user, 'do' => $do, 'action' => $action,
                    'view_context' => $viewcontext);
$url = new moodle_url('/mod/turnitintooltwo/view.php', $urlparams);

// Load Javascript and CSS.
$turnitintooltwoview->load_page_components();

$turnitintooltwoassignment = new turnitintooltwo_assignment($turnitintooltwo->id, $turnitintooltwo);

if (isset($_SESSION["migrationtool"]["status"])) {
    $notice = array();
    switch ($_SESSION["migrationtool"]["status"]) {
        case "success":
            $notice["type"] = "success";
            $notice["message"] = get_string('migrationtool:successful', 'turnitintooltwo');
            $error = false;
            break;
        case "cron":
            $notice["type"] = "success";
            $notice["message"] = get_string('migrationtool:successfulcron', 'turnitintooltwo');
            $error = false;
            break;
        case "gradebookerror":
            $notice["type"] = "danger";
            $notice["message"] = get_string('migrationtool:gradebookerror', 'turnitintooltwo');
            $error = true;
            break;
    }
    include_once("classes/v1migration/v1migration.php");
    v1migration::check_account($config->accountid, $error);

    unset($_SESSION["migrationtool"]["status"]);
}

// Define file upload options.
$maxbytessite = $CFG->maxbytes;
if ($CFG->maxbytes == 0 || $CFG->maxbytes > TURNITINTOOLTWO_MAX_FILE_UPLOAD_SIZE) {
    $maxbytessite = TURNITINTOOLTWO_MAX_FILE_UPLOAD_SIZE;
}

$maxbytescourse = $COURSE->maxbytes;
if ($COURSE->maxbytes == 0 || $COURSE->maxbytes > TURNITINTOOLTWO_MAX_FILE_UPLOAD_SIZE) {
    $maxbytescourse = TURNITINTOOLTWO_MAX_FILE_UPLOAD_SIZE;
}

$maxfilesize = get_user_max_upload_file_size($context,
                                                $maxbytessite,
                                                $maxbytescourse,
                                                $turnitintooltwoassignment->turnitintooltwo->maxfilesize);
$maxfilesize = ($maxfilesize <= 0) ? TURNITINTOOLTWO_MAX_FILE_UPLOAD_SIZE : $maxfilesize;

if ($turnitintooltwoassignment->turnitintooltwo->allownonor) {
  $acceptedtypes = ['*'];
}
else {
  $acceptedtypes = ['.doc', '.docx', '.ppt', '.pptx', '.pps', '.ppsx',
                    '.pdf', '.txt', '.htm', '.html', '.hwp', '.hwpx',
                    '.odt', '.wpd', '.ps', '.rtf', '.xls', '.xlsx'];
}
$turnitintooltwofileuploadoptions = ['maxbytes' => $maxfilesize,
                                     'subdirs' => false, 'maxfiles' => 1, 'accepted_types' => $acceptedtypes];

if (!$parts = $turnitintooltwoassignment->get_parts()) {
    turnitintooltwo_print_error('partgeterror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
}

// Get whether user is a tutor/student.
$istutor = has_capability('mod/turnitintooltwo:grade', $context);
$cansubmit = has_capability('mod/turnitintooltwo:submit', $context);
$userrole = ($istutor) ? 'Instructor' : 'Learner';

// Get the course type for this assignment.
$coursetype = turnitintooltwo_get_course_type($turnitintooltwoassignment->turnitintooltwo->legacy);
$course = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);

// Deal with actions here.
if (!empty($action)) {
    if ($action != "submission") {
        turnitintooltwo_activitylog("Action: ".$action." | Id: ".$turnitintooltwo->id." | Part: ".$part, "REQUEST");
    }

    switch ($action) {
        case "delpart":
            if (!confirm_sesskey()) {
                throw new moodle_exception('invalidsesskey', 'error');
            }

            if (!$istutor) {
                throw new moodle_exception('nopermissions', 'error', '', 'delpart');
            }

            // Check we have more than one part before deleting.
            if (count($turnitintooltwoassignment->get_parts(false)) > 1) {
                if ($turnitintooltwoassignment->delete_moodle_assignment_part($turnitintooltwoassignment->turnitintooltwo->id, $part)) {
                    $_SESSION["notice"]['message'] = get_string('partdeleted', 'turnitintooltwo');
                }
            } else {
                $_SESSION["notice"]['message'] = get_string('partdeleteerror', 'turnitintooltwo', '');
            }

            redirect(new moodle_url('/course/mod.php', array('update' => $cm->id,
                                            'return' => true, 'sesskey' => sesskey())));
            exit;
            break;

        case "addtutor":
            if (!confirm_sesskey()) {
                throw new moodle_exception('invalidsesskey', 'error');
            }

            if ($istutor) {
                $tutorid = required_param('turnitintutors', PARAM_INT);
                $_SESSION["notice"]['message'] = $turnitintooltwoassignment->add_tii_tutor($tutorid);
            }

            redirect(new moodle_url('/mod/turnitintooltwo/view.php', array('id' => $id, 'do' => $do)));
            exit;
            break;

        case "removetutor":
        case "removestudent":
            if (!confirm_sesskey()) {
                throw new moodle_exception('invalidsesskey', 'error');
            }

            $memberrole = ($action == "removetutor") ? "Instructor" : "Learner";

            if ($istutor) {
                $membershipid = required_param('membership_id', PARAM_INT);
                $_SESSION["notice"]['message'] = $turnitintooltwoassignment->remove_tii_user_by_role($membershipid, $memberrole);
            }
            redirect(new moodle_url('/mod/turnitintooltwo/view.php', array('id' => $id, 'do' => $do)));
            exit;
            break;

        case "submission":
            if (!confirm_sesskey()) {
                throw new moodle_exception('invalidsesskey', 'error');
            }

            $PAGE->set_url($url);

            $do = "submission_success";
            $error = false;

            // Clean posted variables.
            $post = array();
            $post['submissiontype'] = required_param('submissiontype', PARAM_INT);
            $post['submissiontext'] = optional_param('submissiontext', '', PARAM_RAW);
            $post['submissiontext'] = trim($post['submissiontext']);
            $post['submissiontitle'] = optional_param('submissiontitle', '', PARAM_TEXT);
            $post['submissiontitle'] = trim(filter_var($post['submissiontitle'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW));
            // Remove characters which aren't permitted in Windows file systems.
            $post['submissiontitle'] = str_replace(array("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "", $post['submissiontitle']);
            $post['studentsname'] = optional_param('studentsname', $USER->id, PARAM_INT);
            $post['studentsname'] = ($istutor) ? $post['studentsname'] : $USER->id;
            $post['submissionpart'] = required_param('submissionpart', PARAM_INT);
            $post['submissionagreement'] = required_param('submissionagreement', PARAM_INT);

            // Default params for redirecting if there is a problem.
            $extraparams = array("part" => $post['submissionpart'], "user" => $post['studentsname']);

            // Check that text content has been provided for submission if applicable.
            if ($post['submissiontype'] == 2 && empty($post['submissiontext'])) {
                $_SESSION["notice"]["message"] = get_string('submissiontexterror', 'turnitintooltwo');
                $error = true;
                $do = "submitpaper";
            }

            // Check that title for submission has been entered.
            if (empty($post['submissiontitle'])) {
                $_SESSION["notice"]["message"] = get_string('submissiontitleerror', 'turnitintooltwo');
                $error = true;
                $do = "submitpaper";
            }

            // Check that student has accepted disclaimer if applicable.
            if (empty($post['submissionagreement'])) {
                $_SESSION["notice"]["message"] = get_string('copyrightagreementerror', 'turnitintooltwo');
                $error = true;
                $do = "submitpaper";
            }

            // Get Moodle Course Object and update in Turnitin.
            $turnitintooltwoassignment->edit_tii_course($course);

            if ($error) {
                // Save data in session incase of error.
                $_SESSION['form_data']->submissiontype = $post['submissiontype'];
                $_SESSION['form_data']->submissiontitle = $post['submissiontitle'];
                $_SESSION['form_data']->submissiontext = $post['submissiontext'];
            } else {
                // Check for previous submission to this part.
                if (!$prevsubmission = $turnitintooltwoassignment->get_user_submissions($post['studentsname'],
                                                    $turnitintooltwoassignment->turnitintooltwo->id, $post['submissionpart'])) {
                    // Create submission object if not a previous one.
                    $turnitintooltwosubmission = new turnitintooltwo_submission(0, "moodle", $turnitintooltwoassignment);
                    if (!$turnitintooltwosubmission->create_submission($post)) {
                        $_SESSION["notice"]["message"] = get_string('createsubmissionerror', 'turnitintooltwo');
                        $do = "submitpaper";
                    }
                } else {
                    foreach ($prevsubmission as $prev) {
                        $submission = $prev;
                    }
                    $turnitintooltwosubmission = new turnitintooltwo_submission($submission->id, "moodle",
                                                                $turnitintooltwoassignment);
                    $turnitintooltwosubmission->reset_submission($post);
                }

                if ($turnitintooltwosubmission) {
                    if ($post['submissiontype'] == 1) {
                        // Upload file.
                        $doupload = $turnitintooltwosubmission->do_file_upload($cm, $turnitintooltwofileuploadoptions);
                        if (!$doupload["result"]) {
                            if (!$prevsubmission) {
                                $turnitintooltwosubmission->delete_submission('failed');
                            }
                            $_SESSION["notice"]["message"] = $doupload["message"];
                            $_SESSION["notice"]["type"] = "error";
                            $do = "submitpaper";
                        }
                    } else if ($post['submissiontype'] == 2) {
                        $turnitintooltwosubmission->prepare_text_submission($cm, $post);
                    }

                    if ($do == "submission_success") {
                        // Log successful submission to Moodle.
                        turnitintooltwo_add_to_log(
                            $turnitintooltwoassignment->turnitintooltwo->course,
                            "add submission",
                            'view.php?id='.$cm->id,
                            get_string('addsubmissiondesc', 'turnitintooltwo') . " '" . $post['submissiontitle'] . "'",
                            $cm->id, $post['studentsname']
                        );

                        $tiisubmission = $turnitintooltwosubmission->do_tii_submission($cm, $turnitintooltwoassignment);
                        $_SESSION["digital_receipt"] = $tiisubmission;
                        $_SESSION["digital_receipt"]["is_manual"] = 0;

                        if ($tiisubmission['success'] == true) {
                            $lockedassignment = new stdClass();
                            $lockedassignment->id = $turnitintooltwoassignment->turnitintooltwo->id;
                            $lockedassignment->submitted = 1;
                            $DB->update_record('turnitintooltwo', $lockedassignment);

                            $lockedpart = new stdClass();
                            $lockedpart->id = $post['submissionpart'];
                            $lockedpart->submitted = 1;

                            // Disable anonymous marking if post date has passed.
                            if ($parts[$post['submissionpart']]->dtpost <= time()) {
                                $lockedpart->unanon = 1;
                            }

                            $DB->update_record('turnitintooltwo_parts', $lockedpart);
                        } else {
                            $do = "submission_failure";
                        }
                        $extraparams = array();
                        unset($_SESSION['form_data']);
                    }
                }
            }

            $params = array_merge(array('id' => $id, 'do' => $do, 'view_context' => $viewcontext), $extraparams);
            redirect(new moodle_url('/mod/turnitintooltwo/view.php', $params));
            exit;
            break;

        case "manualsubmission":
            if (!confirm_sesskey()) {
                throw new moodle_exception('invalidsesskey', 'error');
            }

            $submissionid = required_param('sub', PARAM_INT);
            $turnitintooltwosubmission = new turnitintooltwo_submission($submissionid, "moodle", $turnitintooltwoassignment);

            $digitalreceipt = $turnitintooltwosubmission->do_tii_submission($cm, $turnitintooltwoassignment);
            $_SESSION["digital_receipt"] = $digitalreceipt;

            redirect(new moodle_url('/mod/turnitintooltwo/view.php', array('id' => $id, 'do' => 'submissions')));
            exit;
            break;

        case "emailnonsubmitters":
            if (!confirm_sesskey()) {
                throw new moodle_exception('invalidsesskey', 'error');
            }

            $subject = required_param('nonsubmitters_subject', PARAM_TEXT);
            $message = required_param('nonsubmitters_message', PARAM_TEXT);
            $sendtoself = optional_param('nonsubmitters_sendtoself', 0, PARAM_INT);

            // Error handling for non submitters form.
            $error = false;
            if (empty($subject) || empty($message)) {
                $_SESSION['embeddednotice'] = array("type" => "error");
                $_SESSION["embeddednotice"]["message"] = get_string('nonsubmitterserror', 'turnitintooltwo');
                $error = true;
                $do = "emailnonsubmittersform";
            }

            if ($error) {
                // Save data in session incase of error.
                $_SESSION['form_data'] = new stdClass;
                $_SESSION['form_data']->nonsubmitters_subject = $subject;
                $_SESSION['form_data']->nonsubmitters_message = $message;
                $_SESSION['form_data']->nonsubmitters_sendtoself = $sendtoself;
            } else {

                // Get all users enrolled in the class.
                $allusers = get_enrolled_users($context, 'mod/turnitintooltwo:submit', groups_get_activity_group($cm), 'u.id');

                // Get users who've submitted.
                $params = array('turnitintooltwoid' => $turnitintooltwo->id, 'submission_part' => $part);
                $submittedusers = $DB->get_records('turnitintooltwo_submissions', $params, '', 'userid');

                // Send message to all non submitted users. Excluding suspended students.
                $suspendedusers = get_suspended_userids($context, true);
                $nonsubmittedusers = array_diff_key((array)$allusers, (array)$suspendedusers, (array)$submittedusers);
                foreach ($nonsubmittedusers as $nonsubmitteduser) {
                    // Send a message to the user's Moodle inbox with the digital receipt.
                    $nonsubmitters->send_message($nonsubmitteduser->id, $subject, $message, $cm->course);
                }

                // Send a copy of message to the instructor if appropriate.
                if (!empty($sendtoself)) {
                    $nonsubmitters->send_message($USER->id, $subject, $message, $cm->course);
                }

                $do = "emailsent";
            }

            $params = array('id' => $id, 'do' => $do, 'view_context' => 'box_solid');
            redirect(new moodle_url('/mod/turnitintooltwo/view.php', $params));
            exit;
            break;
    }
}

// Enable activity completion on page view.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Show header and navigation.
if ($viewcontext == "box" || $viewcontext == "box_solid") {

    $PAGE->set_pagelayout('embedded');

    $turnitintooltwoview->output_header($url);
} else {
    $turnitintooltwoview->output_header(
            $url,
            $turnitintooltwoassignment->turnitintooltwo->name,
            $COURSE->fullname);

    // Gracefully error if the user is a guest.
    if (isguestuser()) {
        // Show summary box.
        if (!empty($turnitintooltwoassignment->turnitintooltwo->intro)) {
            $introtext = format_module_intro('turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo, $cm->id);
            echo html_writer::tag("div", $introtext);
        }

        echo html_writer::tag("p", get_string('noguests', 'turnitintooltwo'));

        $do = "";
    } else {
        // Dropdown to filter by groups.
        $groupmode = groups_get_activity_groupmode($cm);
        if ($groupmode) {
            groups_get_activity_group($cm, true);
            groups_print_activity_menu($cm, $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$id.'&do='.$do);
        }

        $turnitintooltwoview->draw_tool_tab_menu($cm, $do);
    }
}

echo html_writer::start_tag('div', array('class' => 'mod_turnitintooltwo'));

// Include the css for if javascript isn't enabled when a student is logged in.
if (!$istutor) {
    $noscriptcss = html_writer::tag('link', '', array("rel" => "stylesheet", "type" => "text/css",
                                                        "href" => $CFG->wwwroot."/mod/turnitintooltwo/css/student_noscript.css"));
    echo html_writer::tag('noscript', $noscriptcss);
}

if (isset($notice["message"])) {
    echo $turnitintooltwoview->show_notice($notice);
}

// Show a warning (and hide the rest of the output) if javascript is not enabled while a tutor is logged in.
if ($istutor) {
    echo html_writer::tag('noscript', get_string('noscript', 'turnitintooltwo'), array("class" => "warning"));
}
// Determine if javascript is required and apply class which will hide/show appropriate content.
$class = ($istutor) ? "js_required" : "";

echo html_writer::start_tag("div", array("class" => $class));
echo html_writer::tag("div", $viewcontext, array("id" => "view_context"));

switch ($do) {
    case "submission_success":
        $digitalreceipt = $turnitintooltwoview->show_digital_receipt($_SESSION["digital_receipt"]);
        if ($viewcontext == "box_solid") {
            $digitalreceipt = html_writer::tag("div", $digitalreceipt, array("id" => "box_receipt"));
        }
        echo $digitalreceipt;
        unset($_SESSION["digital_receipt"]);
        break;

    case "submission_failure":

        $output = $OUTPUT->box($OUTPUT->pix_icon('icon', get_string('turnitin', 'turnitintooltwo'),
                                                    'mod_turnitintooltwo'), 'centered_div');

        $output .= html_writer::tag("div", $_SESSION["digital_receipt"]["message"], array("class" => "mod_turnitintooltwo_general_warning"));
        if ($viewcontext == "box_solid") {
            $output = html_writer::tag("div", $output, array("class" => "submission_failure_msg"));
        }
        echo $output;
        unset($_SESSION["digital_receipt"]);
        break;

    case "digital_receipt":
        $submissionid = required_param('submissionid', PARAM_INT);
        $submission = new turnitintooltwo_submission($submissionid, 'turnitin');

        if ($istutor || $USER->id == $submission->userid) {
            $table = new html_table();
            $table->data = array(
                array(get_string('submissionauthor', 'turnitintooltwo'), $submission->firstname . ' ' . $submission->lastname),
                array(get_string('turnitinpaperid', 'turnitintooltwo') . ' <small>(' . get_string('refid', 'turnitintooltwo') . ')</small>',
                        $submissionid),
                array(get_string('submissiontitle', 'turnitintooltwo'), $submission->submission_title),
                array(get_string('receiptassignmenttitle', 'turnitintooltwo'), $turnitintooltwoassignment->turnitintooltwo->name),
                array(get_string('submissiondate', 'turnitintooltwo'), date("d/m/y, H:i", $submission->submission_modified))
            );

            $digitalreceipt = $OUTPUT->pix_icon('tii-logo', get_string('turnitin', 'turnitintooltwo'),
                                'mod_turnitintooltwo', array('class' => 'mod_turnitintooltwo_logo'));
            $digitalreceipt .= '<h2>'.get_string('digitalreceipt', 'turnitintooltwo').'</h2>';
            $digitalreceipt .= '<p>'.get_string('receiptparagraph', 'turnitintooltwo').'</p>';
            $digitalreceipt .= html_writer::table($table);
            $printericon = $OUTPUT->pix_icon('printer', get_string('turnitin', 'turnitintooltwo'), 'mod_turnitintooltwo');
            $digitalreceipt .= '<a href="#" id="mod_turnitintooltwo_receipt_print">' . $printericon . ' ' . get_string('print', 'turnitintooltwo') .'</a>';
        } else {
            $digitalreceipt = "";
        }

        echo html_writer::tag("div", $digitalreceipt, array("id" => "mod_turnitintooltwo_digital_receipt_box"));
        break;

    case "submitpaper":
        if ($istutor || ($cansubmit && $user == $USER->id)) {
            echo $turnitintooltwoview->show_submission_form($cm, $turnitintooltwoassignment, $part,
                                                            $turnitintooltwofileuploadoptions, "box_solid", $user);
            unset($_SESSION['form_data']);

            // Add loader icon for when iframe refreshes.
            $loadericon = html_writer::tag('i', '', array('class' => 'fa fa-spinner fa-spin fa-5x'));
            $output = html_writer::tag('div', $loadericon, array('id' => 'refresh_loading'));

            // Create div for submitting text.
            $icon = $OUTPUT->pix_icon('icon', get_string('uploadingsubtoturnitin', 'turnitintooltwo'), 'mod_turnitintooltwo');
            $text = html_writer::tag('p', get_string('uploadingsubtoturnitin', 'turnitintooltwo'));
            $loadericon = $OUTPUT->pix_icon('loader-lrg', get_string('uploadingsubtoturnitin', 'turnitintooltwo'),
                                                    'mod_turnitintooltwo');

            // Add loader icon and text for submission.
            $output .= html_writer::tag('div', $icon.$text.$loadericon, array('id' => 'submitting_loader'));

        } else {
            $output = html_writer::tag("div", get_string('permissiondeniederror', 'turnitintooltwo'), array("id" => "box_receipt"));
        }

        echo $output;
        break;

    case "export_pdfs":
        $submissionids = array();
        $downloadtype = "pdf_zip";
        foreach ($_REQUEST as $k => $v) {
            if (strstr($k, "submission_id") !== false) {
                $submissionids[] = (int)$v;
                $downloadtype = "gmpdf_zip";
            }
        }

        if ($istutor) {
            $user = new turnitintooltwo_user($USER->id, "Instructor");
            echo html_writer::tag("div", $turnitintooltwoview->output_download_launch_form($downloadtype, $user->tiiuserid,
                                                    $parts[$part]->tiiassignid, $submissionids), array("class" => "launch_form"));
        }
        break;

    case "rubricview":
        if ($cansubmit) {
            $user = new turnitintooltwo_user($USER->id, "Learner");
            $course = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);
            $user->join_user_to_class($course->turnitin_cid);

            echo html_writer::tag("div", $turnitintooltwoview->output_lti_form_launch('rubric_view', 'Learner',
                                                    $parts[$part]->tiiassignid), array("class" => "launch_form"));
        }
        break;

    case "loadmessages":
        if ($istutor || $cansubmit) {
            echo html_writer::tag("div", $turnitintooltwoview->output_lti_form_launch('messages_inbox', $userrole),
                                                    array("id" => "inbox_form"));
        }
        break;

    case "peermarkmanager":
        if ($istutor) {
            echo html_writer::tag("div", $turnitintooltwoview->output_lti_form_launch('peermark_manager', 'Instructor',
                                                    $parts[$part]->tiiassignid), array("class" => "launch_form"));
        }
        break;

    case "peermarkreviews":
        if ($istutor || $cansubmit) {
            echo html_writer::tag("div", $turnitintooltwoview->output_lti_form_launch('peermark_reviews', $userrole,
                                                    $parts[$part]->tiiassignid), array("class" => "launch_form"));
        }
        break;

    case "origreport":
    case "grademark":
    case "downloadoriginal":
    case "default":
        $submissionid = required_param('submissionid', PARAM_INT);
        $user = new turnitintooltwo_user($USER->id, $userrole);

        echo html_writer::tag("div", $turnitintooltwoview->output_dv_launch_form($do, $submissionid, $user->tiiuserid, $userrole),
                                                                                array("class" => "launch_form"));
        if ($do === "origreport") {
            $submission = new turnitintooltwo_submission($submissionid, 'turnitin');
            turnitintooltwo_add_to_log($turnitintooltwoassignment->turnitintooltwo->course, "view submission",
                'view.php?id='.$cm->id, get_string('viewsubmissiondesc', 'turnitintooltwo') . " '$submission->submission_title'",
                $cm->id, $submission->userid);
        }
        break;

    case "submissions":
        // Output a link for the student to accept the turnitin licence agreement.
        $noscriptula = "";
        $ula = "";

        if (!$istutor) {
            echo html_writer::start_tag("div", array("class" => "inbox inbox-student"));

            $eulaaccepted = false;
            $user = new turnitintooltwo_user($USER->id, $userrole);
            $coursedata = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);
            $user->join_user_to_class($coursedata->turnitin_cid);
            // Has the student accepted the EULA?
            $eulaaccepted = $user->useragreementaccepted;
            if ($user->useragreementaccepted != 1) {
                $eulaaccepted = $user->get_accepted_user_agreement();
            }

            // Check if the submitting user has accepted the EULA.
            if ($eulaaccepted != 1) {
                // Moodle strips out form and script code for forum posts so we have to do the Eula Launch differently.
                $eulaurl = $CFG->wwwroot.'/mod/turnitintooltwo/extras.php?cmid='.$cm->id.'&cmd=useragreement&view_context=box_solid';
                $eulalink = html_writer::link($eulaurl,
                                        html_writer::tag('i', '',
                                            array('class' => 'tiiicon icon-warn icon-2x mod_turnitintooltwo_eula_warn')) .'</br></br>'.
                                        get_string('turnitinula', 'turnitintooltwo')." ".get_string('turnitinula_btn', 'turnitintooltwo'),
                                        array("class" => "turnitin_eula_link"));

                $eula = html_writer::tag('div', $eulalink, array('class' => 'mod_turnitintooltwo_eula js_required',
                                            'data-userid' => $user->id));

                $noscriptula = html_writer::tag('noscript',
                                turnitintooltwo_view::output_dv_launch_form("useragreement", 0, $user->tiiuserid,
                                    "Learner", get_string('turnitinula', 'turnitintooltwo'), false)." ".
                                        get_string('noscriptula', 'turnitintooltwo'),
                                            array('class' => 'warning mod_turnitintooltwo_eula_noscript'));
                echo $eula.$noscriptula;
            }
        } else {
            echo html_writer::start_tag("div", array("class" => "inbox inbox-instructor"));
        }

        $listsubmissionsdesc = ($istutor) ? "listsubmissionsdesc" : "listsubmissionsdesc_student";
        turnitintooltwo_add_to_log($turnitintooltwoassignment->turnitintooltwo->course, "list submissions",
                            'view.php?id='.$cm->id, get_string($listsubmissionsdesc, 'turnitintooltwo') . ": $course->id", $cm->id);

        if (!$istutor && !$cansubmit) {
            turnitintooltwo_print_error('permissiondeniederror', 'turnitintooltwo');
            exit();
        }

        $turnitintooltwouser = new turnitintooltwo_user($USER->id, $userrole);

        // Get course data.
        if ($istutor) {
            $course = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);
        }

        // Update Assignment from Turnitin on first visit.
        if (empty($_SESSION["assignment_updated"][$turnitintooltwoassignment->turnitintooltwo->id])) {
            $turnitintooltwoassignment->update_assignment_from_tii();
            // Enrol the tutor on the class.
            if ($istutor) {
                $turnitintooltwouser->join_user_to_class($course->turnitin_cid);
            }
        }

        // Show submission failure if this has been a manual submission.
        if (isset($_SESSION["digital_receipt"]["success"]) && $_SESSION["digital_receipt"]["success"] == false) {
            $output = html_writer::tag("div", $_SESSION["digital_receipt"]["message"],
                                    array("class" => "mod_turnitintooltwo_general_warning manual_submission_failure_msg"));
            if ($viewcontext == "box_solid") {
                $output = html_writer::tag("div", $output, array("class" => "submission_failure_msg"));
            }
            echo $output;
            unset($_SESSION["digital_receipt"]);
        }

        // Show duplicate assignment warning if applicable.
        if ($istutor) {
            echo $turnitintooltwoview->show_duplicate_assignment_warning($turnitintooltwoassignment, $parts);
        }

        if ($cansubmit &&
                !empty($_SESSION["digital_receipt"]) && !isset($_SESSION["digital_receipt"]["is_manual"])) {
            echo $turnitintooltwoview->show_digital_receipt($_SESSION["digital_receipt"]);
            unset($_SESSION["digital_receipt"]);
        }

        // Convert the course overview events to MDL33+ events if necessary.
        if (($CFG->branch >= 33) && ($istutor)) {
            foreach ($parts as $part) {
                turnitintooltwo_update_event($turnitintooltwoassignment->turnitintooltwo, $part, null, true);
            }
        }

        // Initialise inbox, if a student is logged in then populate it also incase they have no javascript.
        echo $turnitintooltwoview->init_submission_inbox($cm, $turnitintooltwoassignment, $parts, $turnitintooltwouser);

        // Show submission form for students (only shows if they don't have javascript enabled).
        if (!$istutor) {
            echo html_writer::start_tag("div", array("class" => "js_hide"));
            echo $turnitintooltwoview->show_submission_form($cm, $turnitintooltwoassignment, $part,
                                                    $turnitintooltwofileuploadoptions, "window", $USER->id);
            echo html_writer::end_tag("div");
        } else if ($turnitintooltwoassignment->turnitintooltwo->anon > 0) {
            // Put the html for unanonymising a submission below the form for including in lightbox.
            echo $turnitintooltwoview->show_unanonymise_form();
        }
        echo html_writer::end_tag("div");
        break;

    case "students":
    case "tutors":
        $membersview = new members_view($course, $cm, $turnitintooltwoview, $turnitintooltwoassignment);
        $membershtml = $membersview->build_members_view($do);

        echo $membershtml;
        break;

    case "emailnonsubmittersform":
        if (!$istutor) {
            turnitintooltwo_print_error('permissiondeniederror', 'turnitintooltwo');
            exit();
        }

        $output = '';

        if (isset($_SESSION["embeddednotice"])) {
            $output = html_writer::tag("div", $_SESSION["embeddednotice"]["message"], array('class' => 'mod_turnitintooltwo_general_warning'));
            unset($_SESSION["embeddednotice"]);
        }

        $elements = array();
        $elements[] = array('header', 'nonsubmitters_header', get_string('messagenonsubmitters', 'turnitintooltwo'));
        $elements[] = array('static', 'nonsubmittersformdesc', get_string('nonsubmittersformdesc', 'turnitintooltwo'), '', '');
        $elements[] = array('text', 'nonsubmitters_subject', get_string('nonsubmitterssubject', 'turnitintooltwo'), '', '',
                                'required', get_string('nonsubmitterssubjecterror', 'turnitintooltwo'), PARAM_TEXT);
        $elements[] = array('textarea', 'nonsubmitters_message', get_string('nonsubmittersmessage', 'turnitintooltwo'), '', '',
                                'required', get_string('nonsubmittersmessageerror', 'turnitintooltwo'), PARAM_TEXT);
        $elements[] = array('advcheckbox', 'nonsubmitters_sendtoself', get_string('nonsubmitterssendtoself', 'turnitintooltwo'),
                            '', array(0, 1));
        $customdata["checkbox_label_after"] = true;

        $elements[] = array('hidden', 'id', $cm->id);
        $elements[] = array('hidden', 'part', $part);
        $elements[] = array('hidden', 'action', 'emailnonsubmitters');
        $elements[] = array('submit', 'send_email', get_string('nonsubmitterssubmit', 'turnitintooltwo'));

        $customdata["elements"] = $elements;
        $customdata["hide_submit"] = true;
        $customdata["disable_form_change_checker"] = true;

        $optionsform = new turnitintooltwo_form('', $customdata);

        echo html_writer::tag('div', $output.$optionsform->display(), array('class' => 'mod_turnitintooltwo_nonsubmittersform'));
        unset($_SESSION['form_data']);
        break;

    case "emailsent":
        echo html_writer::tag('div', get_string('nonsubmittersformsuccess', 'turnitintooltwo'),
                                array('class' => 'mod_turnitintooltwo_nonsubmittersformsuccessmsg'));
        break;
}

echo html_writer::end_tag("div");
echo html_writer::end_tag("div");
echo $OUTPUT->footer();

// This comment is here as it is useful for product support.
$partsstring = "(";
foreach ($parts as $part) {
    $partsstring .= ($partsstring != "(") ? " | " : "";
    $partsstring .= $part->partname.': '.$part->tiiassignid;
}
$partsstring .= ")";
$courseid = $course->turnitin_cid;

echo '<!-- Turnitin Moodle Direct Version: '.turnitintooltwo_get_version().' - course ID: '.$courseid.' - '.$partsstring.' -->';

