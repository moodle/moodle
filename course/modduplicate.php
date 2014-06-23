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
// Require both target import caps to be able to duplicate, see course_get_cm_edit_actions()
require_capability('moodle/backup:backuptargetimport', $context);
require_capability('moodle/restore:restoretargetimport', $context);

$PAGE->set_title(get_string('duplicate'));
$PAGE->set_heading($course->fullname);
$PAGE->set_url(new moodle_url('/course/modduplicate.php', array('cmid' => $cm->id, 'courseid' => $course->id)));
$PAGE->set_pagelayout('incourse');

$output = $PAGE->get_renderer('core', 'backup');

// Duplicate the module.
$newcm = duplicate_module($course, $cm);

echo $output->header();

$a          = new stdClass();
$a->modtype = get_string('modulename', $cm->modname);
$a->modname = format_string($cm->name);

if (!empty($newcm)) {
    echo $output->confirm(
        get_string('duplicatesuccess', 'core', $a),
        new single_button(
            new moodle_url('/course/modedit.php', array('update' => $newcm->id, 'sr' => $sectionreturn)),
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
