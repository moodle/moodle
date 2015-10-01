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
 * Edit instance of enrol_guest.
 *
 * Adds new instance of enrol_guest to specified course
 * or edits current instance.
 *
 * @package    enrol_guest
 * @copyright  2015 Andrew Hancox <andrewdchancox@googlemail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$courseid   = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/guest:config', $context);

$PAGE->set_url('/enrol/guest/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/instances.php', array('id' => $course->id));
if (!enrol_is_enabled('guest')) {
    redirect($return);
}

$plugin = enrol_get_plugin('guest');

if ($instanceid) {
    $conditions = array('courseid' => $course->id, 'enrol' => 'guest', 'id' => $instanceid);
    $instance = $DB->get_record('enrol', $conditions, '*', MUST_EXIST);
} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id' => $course->id)));

    $instance = (object)$plugin->get_instance_defaults();
    $instance->id       = null;
    $instance->courseid = $course->id;
}

$mform = new \enrol_guest\enrol_guest_edit_form(null, array($instance, $plugin));
$mform->set_data($instance);

if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {

    if ($instance->id) {
        $reset = ($instance->status != $data->status);

        $instance->status         = $data->status;
        $instance->password       = $data->password;
        $instance->timemodified   = time();
        $DB->update_record('enrol', $instance);

        if ($reset) {
            $context->mark_dirty();
        }

        \core\event\enrol_instance_updated::create_from_record($instance)->trigger();
    } else {
        $fields = array(
            'status'          => $data->status,
            'password'        => $data->password);
        $plugin->add_instance($course, $fields);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_guest'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_guest'));
$mform->display();
echo $OUTPUT->footer();
