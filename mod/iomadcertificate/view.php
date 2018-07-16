<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Handles viewing a iomadcertificate
 *
 * @package    mod_iomadcertificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("$CFG->dirroot/mod/iomadcertificate/locallib.php");
require_once("$CFG->dirroot/mod/iomadcertificate/deprecatedlib.php");
require_once("$CFG->libdir/pdflib.php");

$id = required_param('id', PARAM_INT);    // Course Module ID
$action = optional_param('action', '', PARAM_ALPHA);
$edit = optional_param('edit', -1, PARAM_BOOL);
$userid = optional_param('userid', 0, PARAM_INT);

// Are we doing this for another user?
if (!empty($userid)  && has_capability('mod/iomadcertificate:viewother', context_system::instance())) {
    $certuser = $DB->get_record('user', array('id' => $userid));
    // Check the companies match for both users.
    if (!company::check_can_manage($userid)) {
        print_error('Invalid user');
    }
} else {
    $certuser = $USER;
}

if (!$cm = get_coursemodule_from_id('iomadcertificate', $id)) {
    print_error('Course Module ID was incorrect');
}
if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
    print_error('course is misconfigured');
}
if (!$iomadcertificate = $DB->get_record('iomadcertificate', array('id'=> $cm->instance))) {
    print_error('course module is incorrect');
}

// IOMAD - If has ability to view completion reports should be able to see the certificates
if (!has_capability('mod/iomadcertificate:viewother',context_system::instance())) {
    if ($USER->id != $userid) {
        require_login($course->id, true, $cm);
    }
}

$context = context_module::instance($cm->id);
require_capability('mod/iomadcertificate:view', $context);

$event = \mod_iomadcertificate\event\course_module_viewed::create(array(
    'objectid' => $iomadcertificate->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('iomadcertificate', $iomadcertificate);
$event->trigger();

$completion=new completion_info($course);
$completion->set_module_viewed($cm);

// Initialize $PAGE, compute blocks
$PAGE->set_url('/mod/iomadcertificate/view.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title(format_string($iomadcertificate->name));
$PAGE->set_heading(format_string($course->fullname));

if (($edit != -1) and $PAGE->user_allowed_editing()) {
     $USER->editing = $edit;
}

// Add block editing button
if ($PAGE->user_allowed_editing()) {
    $editvalue = $PAGE->user_is_editing() ? 'off' : 'on';
    $strsubmit = $PAGE->user_is_editing() ? get_string('blockseditoff') : get_string('blocksediton');
    $url = new moodle_url($CFG->wwwroot . '/mod/iomadcertificate/view.php', array('id' => $cm->id, 'edit' => $editvalue));
    $PAGE->set_button($OUTPUT->single_button($url, $strsubmit));
}

// Check if the user can view the iomadcertificate
if ($iomadcertificate->requiredtime && !has_capability('mod/iomadcertificate:manage', $context)) {
    if (iomadcertificate_get_course_time($course->id) < ($iomadcertificate->requiredtime * 60)) {
        $a = new stdClass;
        $a->requiredtime = $iomadcertificate->requiredtime;
        notice(get_string('requiredtimenotmet', 'iomadcertificate', $a), "$CFG->wwwroot/course/view.php?id=$course->id");
        die;
    }
}

// Create new iomadcertificate record, or return existing record
$certrecord = iomadcertificate_get_issue($course, $USER, $iomadcertificate, $cm);

make_cache_directory('tcpdf');

// Load the specific iomadcertificate type.
require("$CFG->dirroot/mod/iomadcertificate/type/$iomadcertificate->iomadcertificatetype/certificate.php");

if (empty($action)) { // Not displaying PDF
    echo $OUTPUT->header();

    $viewurl = new moodle_url('/mod/iomadcertificate/view.php', array('id' => $cm->id));
    groups_print_activity_menu($cm, $viewurl);
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

    if (has_capability('mod/iomadcertificate:manage', $context)) {
        $numusers = count(iomadcertificate_get_issues($iomadcertificate->id, 'ci.timecreated ASC', $groupmode, $cm));
        $url = html_writer::tag('a', get_string('viewiomadcertificateviews', 'iomadcertificate', $numusers),
            array('href' => $CFG->wwwroot . '/mod/iomadcertificate/report.php?id=' . $cm->id));
        echo html_writer::tag('div', $url, array('class' => 'reportlink'));
    }

    if (!empty($iomadcertificate->intro)) {
        echo $OUTPUT->box(format_module_intro('iomadcertificate', $iomadcertificate, $cm->id), 'generalbox', 'intro');
    }

    if ($attempts = iomadcertificate_get_attempts($iomadcertificate->id)) {
        echo iomadcertificate_print_attempts($course, $iomadcertificate, $attempts);
    }
    if ($iomadcertificate->delivery == 0)    {
        $str = get_string('openwindow', 'iomadcertificate');
    } elseif ($iomadcertificate->delivery == 1)    {
        $str = get_string('opendownload', 'iomadcertificate');
    } elseif ($iomadcertificate->delivery == 2)    {
        $str = get_string('openemail', 'iomadcertificate');
    }
    echo html_writer::tag('p', $str, array('style' => 'text-align:center'));
    $linkname = get_string('getiomadcertificate', 'iomadcertificate');

    $link = new moodle_url('/mod/iomadcertificate/view.php?id='.$cm->id.'&action=get');
    $button = new single_button($link, $linkname);
    if ($iomadcertificate->delivery != 1) {
        $button->add_action(new popup_action('click', $link, 'view' . $cm->id, array('height' => 600, 'width' => 800)));
    }

    echo html_writer::tag('div', $OUTPUT->render($button), array('style' => 'text-align:center'));
    echo $OUTPUT->footer($course);
    exit;
} else { // Output to pdf

    // No debugging here, sorry.
    $CFG->debugdisplay = 0;
    @ini_set('display_errors', '0');
    @ini_set('log_errors', '1');

    $filename = iomadcertificate_get_iomadcertificate_filename($iomadcertificate, $cm, $course) . '.pdf';

    // PDF contents are now in $file_contents as a string.
    $filecontents = $pdf->Output('', 'S');

    if ($iomadcertificate->savecert == 1) {
        iomadcertificate_save_pdf($filecontents, $certrecord->id, $filename, $context->id);
    }

    if ($iomadcertificate->delivery == 0) {
        // Open in browser.
        send_file($filecontents, $filename, 0, 0, true, false, 'application/pdf');
    } elseif ($iomadcertificate->delivery == 1) {
        // Force download.
        send_file($filecontents, $filename, 0, 0, true, true, 'application/pdf');
    } elseif ($iomadcertificate->delivery == 2) {
        iomadcertificate_email_student($course, $iomadcertificate, $certrecord, $context, $filecontents, $filename);
        // Open in browser after sending email.
        send_file($filecontents, $filename, 0, 0, true, false, 'application/pdf');
    }
}
