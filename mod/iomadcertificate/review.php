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
 * This page reviews a iomadcertificate
 *
 * @package    mod_iomadcertificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('locallib.php');
require_once("$CFG->libdir/pdflib.php");

// Retrieve any variables that are passed
$id = required_param('id', PARAM_INT);    // Course Module ID
$action = optional_param('action', '', PARAM_ALPHA);

if (!$cm = get_coursemodule_from_id('iomadcertificate', $id)) {
    print_error('Course Module ID was incorrect');
}

if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
    print_error('course is misconfigured');
}

if (!$iomadcertificate = $DB->get_record('iomadcertificate', array('id'=> $cm->instance))) {
    print_error('course module is incorrect');
}

// Requires a course login
require_login($course, true, $cm);

// Check the capabilities
$context = context_module::instance($cm->id);
require_capability('mod/iomadcertificate:view', $context);

// Initialize $PAGE, compute blocks
$PAGE->set_url('/mod/iomadcertificate/review.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title(format_string($iomadcertificate->name));
$PAGE->set_heading(format_string($course->fullname));

// Get previous cert record
if (!$certrecord = $DB->get_record('iomadcertificate_issues', array('userid' => $USER->id, 'iomadcertificateid' => $iomadcertificate->id))) {
    notice(get_string('noiomadcertificatesissued', 'iomadcertificate'), "$CFG->wwwroot/course/view.php?id=$course->id");
    die;
}

// Load the specific iomadcertificatetype
require ("$CFG->dirroot/mod/iomadcertificate/type/$iomadcertificate->iomadcertificatetype/iomadcertificate.php");

if ($action) {
    $filename = iomadcertificate_get_iomadcertificate_filename($iomadcertificate, $cm, $course) . '.pdf';
    $filecontents = $pdf->Output('', 'S');
    // Open in browser.
    send_file($filecontents, $filename, 0, 0, true, false, 'application/pdf');
    exit();
}

echo $OUTPUT->header();

$reviewurl = new moodle_url('/mod/iomadcertificate/review.php', array('id' => $cm->id));
groups_print_activity_menu($cm, $reviewurl);
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

echo html_writer::tag('p', get_string('viewed', 'iomadcertificate'). '<br />' . userdate($certrecord->timecreated), array('style' => 'text-align:center'));

$link = new moodle_url('/mod/iomadcertificate/review.php?id='.$cm->id.'&action=get');
$linkname = get_string('reviewiomadcertificate', 'iomadcertificate');
$button = new single_button($link, $linkname);
$button->add_action(new popup_action('click', $link, array('height' => 600, 'width' => 800)));

echo html_writer::tag('div', $OUTPUT->render($button), array('style' => 'text-align:center'));

echo $OUTPUT->footer($course);
