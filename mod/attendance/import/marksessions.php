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
 * Mark attendance sessions using a csv import.
 *
 * @package mod_attendance
 * @author Dan Marsden
 * @copyright 2020 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/mod/attendance/lib.php');
require_once($CFG->dirroot . '/mod/attendance/locallib.php');

$pageparams = new mod_attendance_take_page_params();

$id                     = required_param('id', PARAM_INT);
$pageparams->sessionid  = required_param('sessionid', PARAM_INT);
$pageparams->grouptype  = optional_param('grouptype', null, PARAM_INT);
$pageparams->page       = optional_param('page', 1, PARAM_INT);
$importid               = optional_param('importid', null, PARAM_INT);

$cm                     = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course                 = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att                    = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

// Check this is a valid session for this attendance.
$session                = $DB->get_record('attendance_sessions', array('id' => $pageparams->sessionid, 'attendanceid' => $att->id),
                                  '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/attendance:takeattendances', $context);

$pageparams->init($course->id);

$PAGE->set_context($context);
$url = new moodle_url('/mod/attendance/import/marksessions.php');
$PAGE->set_url($url);
$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->navbar->add($att->name);

$att = new mod_attendance_structure($att, $cm, $course, $PAGE->context, $pageparams);

// Form processing and displaying is done here.
$output = $PAGE->get_renderer('mod_attendance');

$formparams = ['id' => $cm->id,
               'sessionid' => $pageparams->sessionid,
               'grouptype' => $pageparams->grouptype];
$form = null;
if (optional_param('needsconfirm', 0, PARAM_BOOL)) {
    $form = new \mod_attendance\form\import\marksessions($url->out(false), $formparams);
} else if (optional_param('confirm', 0, PARAM_BOOL)) {
    $importer = new \mod_attendance\import\marksessions(null, $att, null, null, $importid);
    $formparams['importer'] = $importer;
    $form = new \mod_attendance\form\import\marksessions_confirm(null, $formparams);
} else {
    $form = new \mod_attendance\form\import\marksessions($url->out(false), $formparams);
}

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/attendance/take.php',
             array('id' => $cm->id,
             'sessionid' => $pageparams->sessionid,
             'grouptype' => $pageparams->grouptype)));
    return;
} else if ($data = $form->get_data()) {
    if ($data->confirm) {
        $importid = $data->importid;
        $importer = new \mod_attendance\import\marksessions(null, $att, null, null, $importid, $data, true);
        $error = $importer->get_error();
        if ($error) {
            $form = new \mod_attendance\form\import\marksessions($url->out(false), $formparams);
            $form->set_import_error($error);
        } else {
            echo $output->header();
            $sessions = $importer->import();
            mod_attendance_notifyqueue::show();
            $url = new moodle_url('/mod/attendance/manage.php', array('id' => $att->cmid));
            echo $output->continue_button($url);
            echo $output->footer();
            die();
        }
    } else {
        $text = $form->get_file_content('attendancefile');
        $encoding = $data->encoding;
        $delimiter = $data->separator;
        $importer = new \mod_attendance\import\marksessions($text, $att, $encoding, $delimiter, 0, null, true);
        $formparams['importer'] = $importer;
        $confirmform = new \mod_attendance\form\import\marksessions_confirm(null, $formparams);
        $form = $confirmform;
        $pagetitle = get_string('confirmcolumnmappings', 'attendance');
    }
}

// Output for the file upload form starts here.
echo $output->header();
echo $output->heading(get_string('attendanceforthecourse', 'attendance') . ' :: ' . format_string($course->fullname));
echo $output->box(get_string('marksessionimportcsvhelp', 'attendance'));
mod_attendance_notifyqueue::show();
$form->display();
echo $output->footer();
