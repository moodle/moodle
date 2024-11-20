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
 * @package   enrol_license
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('edit_form.php');

$courseid   = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT); // Instanceid.

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/license:config', $context);

$PAGE->set_url('/enrol/license/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/instances.php', array('id' => $course->id));
if (!enrol_is_enabled('license')) {
    redirect($return);
}

$plugin = enrol_get_plugin('license');

if ($instanceid) {
    $instance = $DB->get_record('enrol', array('courseid' => $course->id,
                                               'enrol' => 'license',
                                               'id' => $instanceid), '*', MUST_EXIST);
} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id' => $course->id)));
    $instance = new stdClass();
    $instance->id       = null;
    $instance->courseid = $course->id;
}

$mform = new enrol_license_edit_form(null, array($instance, $plugin, $context));

if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {
    if ($instance->id) {
        $instance->status         = $data->status;
        $instance->name           = $data->name;
        $instance->password       = null;
        $instance->customint1     = 0;
        $instance->customint2     = $data->customint2;
        $instance->customint3     = 0;
        $instance->customint4     = $data->customint4;
        $instance->customtext1    = $data->customtext1;
        $instance->roleid         = $data->roleid;
        $instance->enrolperiod    = 0;
        $instance->enrolstartdate = 0;
        $instance->enrolenddate   = 0;
        $instance->timemodified   = time();
        $DB->update_record('enrol', $instance);

    } else {
        $fields = array('status' => $data->status,
                        'name' => $data->name,
                        'password' => null,
                        'customint1' => 0,
                        'customint2' => $data->customint2,
                        'customint3' => 0,
                        'customint4' => $data->customint4,
                        'customtext1' => $data->customtext1,
                        'roleid' => $data->roleid,
                        'enrolperiod' => 0,
                        'enrolstartdate' => 0,
                        'enrolenddate' => 0);
        $plugin->add_instance($course, $fields);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_license'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_license'));
$mform->display();
echo $OUTPUT->footer();
