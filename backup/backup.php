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

require_once('../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');


$courseid = required_param('id', PARAM_INT);
$sectionid = optional_param('section', null, PARAM_INT);
$cmid = optional_param('cm', null, PARAM_INT);
/**
 * Part of the forms in stages after initial, is POST never GET
 */
$backupid = optional_param('backup', false, PARAM_ALPHANUM);

$url = new moodle_url('/backup/backup.php', array('id'=>$courseid));
if ($sectionid !== null) {
    $url->param('section', $sectionid);
}
if ($cmid !== null) {
    $url->param('cm', $cmid);
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$id = $courseid;
$cm = null;
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$type = backup::TYPE_1COURSE;
if (!is_null($sectionid)) {
    $section = $DB->get_record('course_sections', array('course'=>$course->id, 'id'=>$sectionid), '*', MUST_EXIST);
    $type = backup::TYPE_1SECTION;
    $id = $sectionid;
}
if (!is_null($cmid)) {
    $cm = get_coursemodule_from_id(null, $cmid, $course->id, false, MUST_EXIST);
    $type = backup::TYPE_1ACTIVITY;
    $id = $cmid;
}
require_login($course, false, $cm);

switch ($type) {
    case backup::TYPE_1COURSE :
        require_capability('moodle/backup:backupcourse', get_context_instance(CONTEXT_COURSE, $course->id));
        $heading = get_string('backupcourse', 'backup', $course->shortname);
        break;
    case backup::TYPE_1SECTION :
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/backup:backupsection', $coursecontext);
        if ((string)$section->name !== '') {
            $sectionname = format_string($section->name, true, array('context' => $coursecontext));
            $heading = get_string('backupsection', 'backup', $sectionname);
            $PAGE->navbar->add($sectionname);
        } else {
            $heading = get_string('backupsection', 'backup', $section->section);
            $PAGE->navbar->add(get_string('section').' '.$section->section);
        }
        break;
    case backup::TYPE_1ACTIVITY :
        require_capability('moodle/backup:backupactivity', get_context_instance(CONTEXT_MODULE, $cm->id));
        $heading = get_string('backupactivity', 'backup', $cm->name);
        break;
    default :
        print_error('unknownbackuptype');
}

if (!($bc = backup_ui::load_controller($backupid))) {
    $bc = new backup_controller($type, $id, backup::FORMAT_MOODLE,
                            backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);
}
$backup = new backup_ui($bc);
$backup->process();
if ($backup->get_stage() == backup_ui::STAGE_FINAL) {
    $backup->execute();
} else {
    $backup->save_controller();
}

$PAGE->set_title($heading.': '.$backup->get_stage_name());
$PAGE->set_heading($heading);
$PAGE->navbar->add($backup->get_stage_name());

$renderer = $PAGE->get_renderer('core','backup');
echo $OUTPUT->header();
if ($backup->enforce_changed_dependencies()) {
    echo $renderer->dependency_notification(get_string('dependenciesenforced','backup'));
}
echo $renderer->progress_bar($backup->get_progress_bar());
echo $backup->display();
$backup->destroy();
unset($backup);
echo $OUTPUT->footer();