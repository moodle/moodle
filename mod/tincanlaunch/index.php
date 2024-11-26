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
 * This lists all instances of tincanlaunch activities in a course.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

// Trigger instances list viewed event.
$event = \mod_tincanlaunch\event\course_module_instance_list_viewed::create(
    array('context' => context_course::instance($course->id))
);
$event->add_record_snapshot('course', $course);
$event->trigger();

$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/tincanlaunch/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

if (! $tincanlaunchs = get_all_instances_in_course('tincanlaunch', $course)) {
    notice(get_string('notincanlaunchs', 'tincanlaunch'),
        new moodle_url('/course/view.php', array('id' => $course->id)));
}

$table = new html_table();

$table->head  = array (get_string('tincanlaunchname', 'tincanlaunch'), 'Section number', 'Custom completion requirements');

foreach ($tincanlaunchs as $tincanlaunch) {
    if (!$tincanlaunch->visible) {
        $link = html_writer::link(
            new moodle_url('/mod/tincanlaunch/view.php', array('id' => $tincanlaunch->coursemodule)),
            format_string($tincanlaunch->name, true),
            array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/tincanlaunch/view.php', array('id' => $tincanlaunch->coursemodule)),
            format_string($tincanlaunch->name, true));
    }

    $completionrequirements = '';

    $tincanverbid = $tincanlaunch->tincanverbid;

    if ($tincanverbid != '') {
        $tincanverb = ucfirst(substr($tincanverbid, strrpos($tincanverbid, '/') + 1));
        $description = get_string('completiondetail:completionbyverbdesc', 'tincanlaunch', $tincanverb);
        $completionrequirements .= $description;
    }

    if ($tincanlaunch->tincanexpiry > 0) {
        $description = get_string('completiondetail:completionexpirydesc', 'tincanlaunch', $tincanlaunch->tincanexpiry);
        $completionrequirements .= '<br/>' . $description;
    }

    $table->data[] = array($link, $tincanlaunch->section, $completionrequirements);

}
echo $OUTPUT->heading(get_string('modulenameplural', 'tincanlaunch'), 2);

echo html_writer::table($table);
echo $OUTPUT->footer();
