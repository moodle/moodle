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
 * Page allowing instructors to configure course-level tools.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lti/edit_form.php');
require_once($CFG->dirroot.'/mod/lti/lib.php');

$courseid = required_param('course', PARAM_INT);
$typeid = optional_param('typeid', null, PARAM_INT);

// Permissions etc.
require_login($courseid, false);
require_capability('mod/lti:addcoursetool', context_course::instance($courseid));
if (!empty($typeid)) {
    $type = lti_get_type_type_config($typeid);
    if ($type->course != $courseid || $type->course == get_site()->id) {
        throw new moodle_exception('You do not have permissions to edit this tool type.');
    }
} else {
    $type = (object) ['lti_clientid' => null];
}

// Page setup.
$url = new moodle_url('/mod/lti/coursetooledit.php', ['courseid' => $courseid]);
$pageheading = !empty($typeid) ? get_string('courseexternaltooledit', 'mod_lti', $type->lti_typename) :
    get_string('courseexternaltooladd', 'mod_lti');

$PAGE->set_url($url);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($pageheading);
$PAGE->set_secondary_active_tab('coursetools');
$PAGE->add_body_class('limitedwidth');

$form = new mod_lti_edit_types_form($url, (object)array('id' => $typeid, 'clientid' => $type->lti_clientid, 'iscoursetool' => true));
if ($form->is_cancelled()) {

    redirect(new moodle_url('/mod/lti/coursetools.php', ['id' => $courseid]));
} else if ($data = $form->get_data()) {

    require_sesskey();

    if (!empty($data->typeid)) {
        $type = (object) ['id' => $data->typeid];
        lti_load_type_if_cartridge($data);
        lti_update_type($type, $data);
        $redirecturl = new moodle_url('/mod/lti/coursetools.php', ['id' => $courseid]);
        $notice = get_string('courseexternaltooleditsuccess', 'mod_lti');
    } else {
        $type = (object) ['state' => LTI_TOOL_STATE_CONFIGURED, 'course' => $data->course];
        lti_load_type_if_cartridge($data);
        lti_add_type($type, $data);
        $redirecturl = new moodle_url('/mod/lti/coursetools.php', ['id' => $courseid]);
        $notice = get_string('courseexternaltooladdsuccess', 'mod_lti', $type->name);
    }

    redirect($redirecturl, $notice, 0, notification::NOTIFY_SUCCESS);
}

// Display the form.
echo $OUTPUT->header();
echo $OUTPUT->heading($pageheading);

if (!empty($typeid)) {
    $form->set_data($type);
}
$form->display();

echo $OUTPUT->footer();
