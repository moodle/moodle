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
 * Edit the section basic information and availability
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/formslib.php');

$id = required_param('id', PARAM_INT);    // course_sections.id
$sectionreturn = optional_param('sr', null, PARAM_INT);
$deletesection = optional_param('delete', 0, PARAM_BOOL);
$showonly = optional_param('showonly', 0, PARAM_TAGLIST);

$returnparams = [];
$params = ['id' => $id];
if (!is_null($sectionreturn)) {
    $params['sr'] = $sectionreturn;
    $returnparams['sr'] = $sectionreturn;
}
if (!empty($showonly)) {
    $params['showonly'] = $showonly;
}
$PAGE->set_url('/course/editsection.php', $params);

$section = $DB->get_record('course_sections', array('id' => $id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $section->course), '*', MUST_EXIST);
$sectionnum = $section->section;

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:update', $context);

// Get section_info object with all availability options.
$sectioninfo = get_fast_modinfo($course)->get_section_info($sectionnum);

// Deleting the section.
if ($deletesection) {
    $cancelurl = course_get_url($course, $sectioninfo, $returnparams);
    if (course_can_delete_section($course, $sectioninfo)) {
        $confirm = optional_param('confirm', false, PARAM_BOOL) && confirm_sesskey();
        if (!$confirm && optional_param('sesskey', null, PARAM_RAW) !== null &&
                empty($sectioninfo->summary) && empty($sectioninfo->sequence) && confirm_sesskey()) {
            // Do not ask for confirmation if section is empty and sesskey is already provided.
            $confirm = true;
        }
        if ($confirm) {
            course_delete_section($course, $sectioninfo, true, true);
            $courseurl = course_get_url($course, $sectioninfo->section - 1, $returnparams);
            redirect($courseurl);
        } else {
            if (get_string_manager()->string_exists('deletesection', 'format_' . $course->format)) {
                $strdelete = get_string('deletesection', 'format_' . $course->format);
            } else {
                $strdelete = get_string('deletesection');
            }
            $PAGE->navbar->add($strdelete);
            $PAGE->set_title($strdelete);
            $PAGE->set_heading($course->fullname);
            echo $OUTPUT->header();
            echo $OUTPUT->box_start('noticebox');
            $optionsyes = array('id' => $id, 'confirm' => 1, 'delete' => 1, 'sesskey' => sesskey());
            $deleteurl = new moodle_url('/course/editsection.php', $optionsyes);
            $formcontinue = new single_button($deleteurl, get_string('delete'));
            $formcancel = new single_button($cancelurl, get_string('cancel'), 'get');
            echo $OUTPUT->confirm(get_string('confirmdeletesection', '',
                get_section_name($course, $sectioninfo)), $formcontinue, $formcancel);
            echo $OUTPUT->box_end();
            echo $OUTPUT->footer();
            exit;
        }
    } else {
        notice(get_string('nopermissions', 'error', get_string('deletesection')), $cancelurl);
    }
}

$editoroptions = array(
    'context'   => $context,
    'maxfiles'  => EDITOR_UNLIMITED_FILES,
    'maxbytes'  => $CFG->maxbytes,
    'trusttext' => false,
    'noclean'   => true,
    'subdirs'   => true
);

$courseformat = course_get_format($course);

if ($sectioninfo->name) {
    $defaultsectionname = $sectioninfo->name;
} else {
    $defaultsectionname = $courseformat->get_default_section_name($section);
}

$customdata = [
    'cs' => $sectioninfo,
    'editoroptions' => $editoroptions,
    'defaultsectionname' => $defaultsectionname,
    'showonly' => $showonly,
];

$mform = $courseformat->editsection_form($PAGE->url, $customdata);

// set current value, make an editable copy of section_info object
// this will retrieve all format-specific options as well
$initialdata = convert_to_array($sectioninfo);
if (!empty($CFG->enableavailability)) {
    $initialdata['availabilityconditionsjson'] = $sectioninfo->availability;
}
$mform->set_data($initialdata);
if (!empty($showonly)) {
    $mform->filter_shown_headers(explode(',', $showonly));
}

if ($mform->is_cancelled()){
    // Form cancelled, return to course.
    redirect(course_get_url($course, $section, $returnparams));
} else if ($data = $mform->get_data()) {
    // Data submitted and validated, update and return to course.

    // For consistency, we set the availability field to 'null' if it is empty.
    if (!empty($CFG->enableavailability)) {
        // Renamed field.
        $data->availability = $data->availabilityconditionsjson;
        unset($data->availabilityconditionsjson);
        if ($data->availability === '') {
            $data->availability = null;
        }
    }
    course_update_section($course, $section, $data);

    $PAGE->navigation->clear_cache();
    redirect(course_get_url($course, $section, $returnparams));
}

// The edit form is displayed for the first time or if there was validation error on the previous step.
$sectionname = get_section_name($course, $sectionnum);
$stredit = get_string('editsectiontitle', '', $sectionname);
$strsummaryof = get_string('editsectionsettings');

$PAGE->set_title($stredit . moodle_page::TITLE_SEPARATOR . $course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($stredit);
echo $OUTPUT->header();

echo $OUTPUT->heading($strsummaryof);

$mform->display();
echo $OUTPUT->footer();
