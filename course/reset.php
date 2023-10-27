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
 * The purpose of this feature is to quickly remove all user related data from a course
 * in order to make it available for a new semester.  This feature can handle the removal
 * of general course data like students, teachers, logs, events and groups as well as module
 * specific data.  Each module must be modified to take advantage of this new feature.
 * The feature will also reset the start date of the course if necessary.
 *
 * @copyright Mark Flach and moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require('../config.php');
require_once('reset_form.php');
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_helper.class.php');

$id = required_param('id', PARAM_INT);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception("invalidcourseid");
}

$PAGE->set_url('/course/reset.php', array('id'=>$id));
$PAGE->set_pagelayout('admin');

require_login($course);
require_capability('moodle/course:reset', context_course::instance($course->id));

$strreset       = get_string('reset');
$strresetcourse = get_string('resetcourse');
$strremove      = get_string('remove');

$PAGE->set_title($course->fullname.': '.$strresetcourse);
$PAGE->set_heading($course->fullname);
$PAGE->set_secondary_active_tab('coursereuse');

$mform = new course_reset_form();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/course/view.php?id='.$id);

} else if ($data = $mform->get_data()) { // no magic quotes

    if (isset($data->selectdefault)) {
        $_POST = array();
        $mform = new course_reset_form();
        $mform->load_defaults();

    } else if (isset($data->deselectall)) {
        $_POST = array();
        $mform = new course_reset_form();

    } else {
        echo $OUTPUT->header();
        \backup_helper::print_coursereuse_selector('reset');

        $data->reset_start_date_old = $course->startdate;
        $data->reset_end_date_old = $course->enddate;
        $status = reset_course_userdata($data);

        $data = array();
        foreach ($status as $item) {
            $line = array();
            $line[] = $item['component'];
            $line[] = $item['item'];
            $line[] = ($item['error'] === false) ? get_string('statusok') : '<div class="notifyproblem">'.$item['error'].'</div>';
            $data[] = $line;
        }

        $table = new html_table();
        $table->head  = array(get_string('resetcomponent'), get_string('resettask'), get_string('resetstatus'));
        $table->size  = array('20%', '40%', '40%');
        $table->align = array('left', 'left', 'left');
        $table->width = '80%';
        $table->data  = $data;
        echo html_writer::table($table);

        echo $OUTPUT->continue_button('view.php?id='.$course->id);  // Back to course page
        echo $OUTPUT->footer();
        exit;
    }
}

echo $OUTPUT->header();
\backup_helper::print_coursereuse_selector('reset');

echo $OUTPUT->box(get_string('resetinfo'));

$mform->display();
echo $OUTPUT->footer();
