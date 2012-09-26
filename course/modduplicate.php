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
 * Duplicates a given course module
 *
 * The script backups and restores a single activity as if it was imported
 * from the same course, using the default import settings. The newly created
 * copy of the activity is then moved right below the original one.
 *
 * @package    core
 * @subpackage course
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/filelib.php');

$cmid           = required_param('cmid', PARAM_INT);
$courseid       = required_param('course', PARAM_INT);
$sectionreturn  = optional_param('sr', null, PARAM_INT);

$course     = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm         = get_coursemodule_from_id('', $cmid, $course->id, true, MUST_EXIST);
$cmcontext  = context_module::instance($cm->id);
$context    = context_course::instance($courseid);
$section    = $DB->get_record('course_sections', array('id' => $cm->section, 'course' => $cm->course));

require_login($course);
require_sesskey();
require_capability('moodle/course:manageactivities', $context);
// Require both target import caps to be able to duplicate, see make_editing_buttons()
require_capability('moodle/backup:backuptargetimport', $context);
require_capability('moodle/restore:restoretargetimport', $context);

$PAGE->set_title(get_string('duplicate'));
$PAGE->set_heading($course->fullname);
$PAGE->set_url(new moodle_url('/course/modduplicate.php', array('cmid' => $cm->id, 'courseid' => $course->id)));
$PAGE->set_pagelayout('incourse');

$output = $PAGE->get_renderer('core', 'backup');

$a          = new stdClass();
$a->modtype = get_string('modulename', $cm->modname);
$a->modname = format_string($cm->name);

if (!plugin_supports('mod', $cm->modname, FEATURE_BACKUP_MOODLE2)) {
    $url = course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn));
    print_error('duplicatenosupport', 'error', $url, $a);
}

// backup the activity

$bc = new backup_controller(backup::TYPE_1ACTIVITY, $cm->id, backup::FORMAT_MOODLE,
        backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);

$backupid       = $bc->get_backupid();
$backupbasepath = $bc->get_plan()->get_basepath();

$bc->execute_plan();

$bc->destroy();

// restore the backup immediately

$rc = new restore_controller($backupid, $courseid,
        backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);

if (!$rc->execute_precheck()) {
    $precheckresults = $rc->get_precheck_results();
    if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backupbasepath);
        }

        echo $output->header();
        echo $output->precheck_notices($precheckresults);
        $url = course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn));
        echo $output->continue_button($url);
        echo $output->footer();
        die();
    }
}

$rc->execute_plan();

// now a bit hacky part follows - we try to get the cmid of the newly
// restored copy of the module
$newcmid = null;
$tasks = $rc->get_plan()->get_tasks();
foreach ($tasks as $task) {
    if (is_subclass_of($task, 'restore_activity_task')) {
        if ($task->get_old_contextid() == $cmcontext->id) {
            $newcmid = $task->get_moduleid();
            break;
        }
    }
}

// if we know the cmid of the new course module, let us move it
// right below the original one. otherwise it will stay at the
// end of the section
if ($newcmid) {
    $newcm = get_coursemodule_from_id('', $newcmid, $course->id, true, MUST_EXIST);
    moveto_module($newcm, $section, $cm);
    moveto_module($cm, $section, $newcm);
}

$rc->destroy();

if (empty($CFG->keeptempdirectoriesonbackup)) {
    fulldelete($backupbasepath);
}

echo $output->header();

if ($newcmid) {
    echo $output->confirm(
        get_string('duplicatesuccess', 'core', $a),
        new single_button(
            new moodle_url('/course/modedit.php', array('update' => $newcmid, 'sr' => $sectionreturn)),
            get_string('duplicatecontedit'),
            'get'),
        new single_button(
            course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)),
            get_string('duplicatecontcourse'),
            'get')
    );

} else {
    echo $output->notification(get_string('duplicatesuccess', 'core', $a), 'notifysuccess');
    echo $output->continue_button(course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)));
}

echo $output->footer();
